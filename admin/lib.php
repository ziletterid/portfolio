<?php
declare(strict_types=1);

/**
 * Admin: autentikasi, CSRF, dan pengelolaan file gambar.
 * File ini tidak boleh diakses langsung lewat browser.
 */
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'lib.php') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../inc/helpers.php';

const STORAGE    = ROOT . '/storage';
const AUTH_FILE  = STORAGE . '/admin.json';
const LOGIN_FILE = STORAGE . '/admin-login.json';
const TRASH_DIR  = STORAGE . '/trash';
const MAX_BYTES  = 15 * 1024 * 1024;   // 15 MB per file
const MAX_SIDE   = 2400;               // gambar lebih besar dari ini dikecilkan otomatis

const MIME_EXT = [
    'image/jpeg'    => 'jpg',
    'image/png'     => 'png',
    'image/webp'    => 'webp',
    'image/gif'     => 'gif',
    'image/svg+xml' => 'svg',
];

/* ------------------------------------------------------------------ */
/* Session & auth                                                      */
/* ------------------------------------------------------------------ */

function admin_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name('pf_admin');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Strict',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
}

function is_local_request(): bool
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    return in_array($ip, ['127.0.0.1', '::1'], true);
}

function has_password(): bool
{
    return is_file(AUTH_FILE);
}

function set_password(string $pw): void
{
    ensure_dir(STORAGE);
    file_put_contents(AUTH_FILE, json_encode(['hash' => password_hash($pw, PASSWORD_DEFAULT), 'created' => date('c')]), LOCK_EX);
    @chmod(AUTH_FILE, 0600);
}

function check_password(string $pw): bool
{
    $data = json_decode((string)@file_get_contents(AUTH_FILE), true);
    return is_array($data) && isset($data['hash']) && password_verify($pw, $data['hash']);
}

function is_logged_in(): bool
{
    return !empty($_SESSION['admin']) && ($_SESSION['admin_ua'] ?? '') === hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
}

function login_user(): void
{
    session_regenerate_id(true);
    $_SESSION['admin']    = true;
    $_SESSION['admin_ua'] = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
    $_SESSION['csrf']     = bin2hex(random_bytes(32));
}

function logout_user(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
}

/** Batasi percobaan login: 5 gagal / 15 menit per IP. */
function login_blocked(): bool
{
    $key  = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '');
    $data = json_decode((string)@file_get_contents(LOGIN_FILE), true) ?: [];
    $hits = array_filter($data[$key] ?? [], fn($t) => $t > time() - 900);
    return count($hits) >= 5;
}

function login_failed(): void
{
    ensure_dir(STORAGE);
    $key  = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '');
    $data = json_decode((string)@file_get_contents(LOGIN_FILE), true) ?: [];
    $data[$key]   = array_values(array_filter($data[$key] ?? [], fn($t) => $t > time() - 900));
    $data[$key][] = time();
    file_put_contents(LOGIN_FILE, json_encode($data), LOCK_EX);
}

function csrf_ok(?string $token): bool
{
    return is_string($token) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

/* ------------------------------------------------------------------ */
/* Targets: folder tujuan yang boleh diubah                            */
/* ------------------------------------------------------------------ */

/** Daftar proyek dari data/content.php */
function projects(): array
{
    return site_content()['projects'] ?? [];
}

/**
 * Ubah id target ("gallery", "project:<slug>") jadi folder relatif.
 * Hanya slug yang ada di content.php yang diizinkan.
 */
function target_dir(string $target): ?string
{
    if ($target === 'gallery') {
        return 'assets/img/gallery';
    }
    if (str_starts_with($target, 'project:')) {
        $slug = substr($target, 8);
        foreach (projects() as $pr) {
            if (($pr['slug'] ?? '') === $slug && preg_match('/^[a-z0-9][a-z0-9-]{0,80}$/', $slug)) {
                return 'assets/img/work/' . $slug;
            }
        }
    }
    return null;
}

function ensure_dir(string $abs): void
{
    if (!is_dir($abs)) {
        mkdir($abs, 0755, true);
    }
}

/** File gambar di folder (nama file saja), urut nama. */
function list_files(string $rel): array
{
    $abs = ROOT . '/' . $rel;
    if (!is_dir($abs)) {
        return [];
    }
    $out = [];
    foreach (scandir($abs) ?: [] as $f) {
        if ($f[0] === '.' || !is_file("$abs/$f")) {
            continue;
        }
        if (!in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'avif'], true)) {
            continue;
        }
        $out[] = $f;
    }
    natcasesort($out);
    return array_values($out);
}

function file_info(string $rel, string $name): array
{
    $abs = ROOT . "/$rel/$name";
    $dim = @getimagesize($abs);
    return [
        'name'    => $name,
        'url'     => '../' . $rel . '/' . rawurlencode($name) . '?v=' . filemtime($abs),
        'caption' => label_from_file($name),
        'size'    => filesize($abs),
        'w'       => $dim[0] ?? null,
        'h'       => $dim[1] ?? null,
    ];
}

/** "Logo Kopi Senja!!" → "logo-kopi-senja" */
function slugify(string $s): string
{
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
    return trim(substr($s, 0, 60), '-') ?: 'image';
}

/** Bagian nama tanpa nomor urut & ekstensi: "03-logo-kopi.png" → "logo-kopi" */
function base_of(string $name): string
{
    $b = pathinfo($name, PATHINFO_FILENAME);
    return preg_replace('/^\d+[\s._-]*/', '', $b) ?: 'image';
}

/** Ganti nama semua file jadi 01-xxx, 02-xxx … sesuai urutan $order. */
function renumber(string $rel, array $order): void
{
    $abs = ROOT . '/' . $rel;
    $tmp = [];
    foreach ($order as $i => $name) {
        $t = $abs . '/.tmp-' . $i . '-' . bin2hex(random_bytes(4));
        rename("$abs/$name", $t);
        $tmp[] = [$t, $name];
    }
    $used = [];
    foreach ($tmp as $i => [$t, $name]) {
        $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $base = base_of($name);
        $new  = sprintf('%02d-%s.%s', $i + 1, $base, $ext);
        while (isset($used[$new])) {
            $base .= '-x';
            $new = sprintf('%02d-%s.%s', $i + 1, $base, $ext);
        }
        $used[$new] = true;
        rename($t, "$abs/$new");
    }
}

/** Pindahkan file ke storage/trash (bisa dipulihkan manual). */
function trash(string $abs): void
{
    ensure_dir(TRASH_DIR);
    rename($abs, TRASH_DIR . '/' . date('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '-' . basename($abs));
}

/** Cek SVG tidak berisi script. */
function svg_safe(string $abs): bool
{
    $s = (string)file_get_contents($abs, false, null, 0, 2_000_000);
    return !preg_match('/<script|<foreignObject|\bon[a-z]+\s*=|javascript:|<iframe|<embed|<object/i', $s);
}

/**
 * Validasi & simpan satu file upload. Mengembalikan ekstensi yang dipakai.
 * @throws RuntimeException
 */
function accept_upload(array $f, string $destAbs): string
{
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $msg = match ($f['error'] ?? 0) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'file terlalu besar untuk server',
            UPLOAD_ERR_NO_FILE => 'tidak ada file',
            default => 'upload gagal (kode ' . ($f['error'] ?? '?') . ')',
        };
        throw new RuntimeException($msg);
    }
    if ($f['size'] > MAX_BYTES) {
        throw new RuntimeException('maksimal 15 MB per file');
    }
    if (!is_uploaded_file($f['tmp_name'])) {
        throw new RuntimeException('upload tidak valid');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    if ($mime === 'text/xml' || $mime === 'application/xml' || $mime === 'text/plain') {
        $head = (string)file_get_contents($f['tmp_name'], false, null, 0, 2048);
        if (stripos($head, '<svg') !== false) {
            $mime = 'image/svg+xml';
        }
    }
    $ext = MIME_EXT[$mime] ?? null;
    if (!$ext) {
        throw new RuntimeException('format tidak didukung (pakai JPG, PNG, WebP, GIF, atau SVG)');
    }
    if ($ext === 'svg' && !svg_safe($f['tmp_name'])) {
        throw new RuntimeException('SVG berisi script, ditolak');
    }
    if ($ext !== 'svg' && @getimagesize($f['tmp_name']) === false) {
        throw new RuntimeException('file bukan gambar yang valid');
    }
    $dest = $destAbs . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], $dest)) {
        throw new RuntimeException('tidak bisa menyimpan file');
    }
    @chmod($dest, 0644);
    shrink($dest, $ext);
    return $ext;
}

/** Kecilkan gambar raster yang sisi terpanjangnya > MAX_SIDE (butuh GD). */
function shrink(string $abs, string $ext): void
{
    if (!function_exists('imagecreatetruecolor') || !in_array($ext, ['jpg', 'png', 'webp'], true)) {
        return;
    }
    [$w, $h] = @getimagesize($abs) ?: [0, 0];
    if (!$w || max($w, $h) <= MAX_SIDE) {
        return;
    }
    $src = match ($ext) {
        'jpg'  => @imagecreatefromjpeg($abs),
        'png'  => @imagecreatefrompng($abs),
        'webp' => @imagecreatefromwebp($abs),
    };
    if (!$src) {
        return;
    }
    $r  = MAX_SIDE / max($w, $h);
    $nw = (int)round($w * $r);
    $nh = (int)round($h * $r);
    $dst = imagecreatetruecolor($nw, $nh);
    if ($ext !== 'jpg') {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
    match ($ext) {
        'jpg'  => imagejpeg($dst, $abs, 86),
        'png'  => imagepng($dst, $abs, 7),
        'webp' => imagewebp($dst, $abs, 86),
    };
}

/* ------------------------------------------------------------------ */
/* Teks: storage/content.json menimpa data/content.php                 */
/* ------------------------------------------------------------------ */

function schema(): array
{
    static $s = null;
    return $s ??= require __DIR__ . '/schema.php';
}

function overrides(): array
{
    $o = is_file(CONTENT_OVERRIDE) ? json_decode((string)file_get_contents(CONTENT_OVERRIDE), true) : [];
    return is_array($o) ? $o : [];
}

function save_overrides(array $o): void
{
    ensure_dir(STORAGE);
    $tmp = CONTENT_OVERRIDE . '.tmp';
    file_put_contents($tmp, json_encode($o, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
    rename($tmp, CONTENT_OVERRIDE);
    site_content(true);
}

/** Ambil nilai berdasarkan path "a.b" */
function path_get(array $a, string $path): mixed
{
    foreach (explode('.', $path) as $k) {
        if (!is_array($a) || !array_key_exists($k, $a)) {
            return null;
        }
        $a = $a[$k];
    }
    return $a;
}

function path_set(array &$a, string $path, mixed $v): void
{
    $keys = explode('.', $path);
    $last = array_pop($keys);
    $ref  = &$a;
    foreach ($keys as $k) {
        if (!isset($ref[$k]) || !is_array($ref[$k])) {
            $ref[$k] = [];
        }
        $ref = &$ref[$k];
    }
    $ref[$last] = $v;
}

function path_unset(array &$a, string $path): void
{
    $keys = explode('.', $path);
    if (count($keys) === 1) {
        unset($a[$keys[0]]);
        return;
    }
    [$top, $key] = $keys;
    if (isset($a[$top]) && is_array($a[$top])) {
        unset($a[$top][$key]);
        if (!$a[$top]) {
            unset($a[$top]);
        }
    }
}

/** Bersihkan teks: buang karakter kontrol, rapikan spasi, potong panjang. */
function clean_text(mixed $v, int $max, bool $multiline = false): string
{
    $s = is_scalar($v) ? (string)$v : '';
    $s = preg_replace($multiline ? '/[\x00-\x09\x0B-\x1F\x7F]/u' : '/[\x00-\x1F\x7F]/u', ' ', $s) ?? '';
    $s = trim(preg_replace($multiline ? '/[ \t]+/' : '/\s+/', ' ', $s) ?? '');
    return mb_substr($s, 0, $max);
}

/**
 * Validasi satu isian sesuai schema.
 * @throws InvalidArgumentException
 */
function clean_field(array $f, mixed $v): mixed
{
    $max = (int)($f['max'] ?? 200);
    $label = $f['label'] ?? $f['key'] ?? 'Isian';
    switch ($f['type']) {
        case 'textarea':
            return clean_text($v, $max ?: 1000, true);
        case 'number':
            return max(0, min(1_000_000, (int)(is_numeric($v) ? $v : 0)));
        case 'email':
            $s = clean_text($v, $max);
            if ($s !== '' && !filter_var($s, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("$label: format email tidak valid.");
            }
            return $s;
        case 'wa':
            $d = preg_replace('/\D+/', '', is_scalar($v) ? (string)$v : '') ?? '';
            if (str_starts_with($d, '0')) {
                $d = '62' . substr($d, 1);   // 0812… → 62812…
            }
            return substr($d, 0, 20);
        case 'url':
            $s = clean_text($v, $max);
            if ($s === '' || $s === '#') {
                return $s;
            }
            if (!preg_match('#^(https?://|mailto:)#i', $s)) {
                if (preg_match('#^[a-z0-9.-]+\.[a-z]{2,}(/|$)#i', $s)) {
                    $s = 'https://' . $s;   // "behance.net/…" → "https://behance.net/…"
                } else {
                    throw new InvalidArgumentException("$label: link harus diawali https://");
                }
            }
            return $s;
        case 'color':
            $s = is_string($v) ? trim($v) : '';
            return preg_match('/^#[0-9a-f]{6}$/i', $s) ? strtolower($s) : ($f['default'] ?? '#000000');
        case 'select':
            return array_key_exists((string)$v, $f['options']) ? (string)$v : (string)array_key_first($f['options']);
        case 'lines':
        case 'tags':
            if (is_string($v)) {
                $v = preg_split($f['type'] === 'tags' ? '/[,\n]/' : '/\n/', $v);
            }
            $out = [];
            foreach ((array)$v as $item) {
                $t = clean_text($item, (int)($f['itemMax'] ?? 60));
                if ($t !== '') {
                    $out[] = $t;
                }
            }
            return array_slice($out, 0, $max ?: 20);
        case 'repeater':
            $out = [];
            foreach (array_slice(is_array($v) ? array_values($v) : [], 0, $max ?: 20) as $item) {
                $out[] = clean_item($f['fields'], is_array($item) ? $item : []);
            }
            return $out;
        default:
            return clean_text($v, $max);
    }
}

/** Validasi satu baris repeater / satu proyek. Field "a.b" menjadi bersarang. */
function clean_item(array $fields, array $item): array
{
    $out = [];
    foreach ($fields as $f) {
        path_set($out, $f['key'], clean_field($f, path_get($item, $f['key'])));
    }
    return $out;
}

/** Nilai panel saat ini (sudah termasuk perubahan dari admin). */
function panel_values(array $panel): array
{
    $c   = site_content();
    $out = [];
    foreach ($panel['fields'] as $f) {
        $out[$f['path']] = path_get($c, $f['path']);
    }
    return $out;
}

/* ------------------------------------------------------------------ */
/* Proyek                                                              */
/* ------------------------------------------------------------------ */

/** Simpan daftar proyek lengkap ke storage/content.json */
function save_projects(array $list): void
{
    $o = overrides();
    $o['projects'] = array_values($list);
    save_overrides($o);
}

/** Slug unik dari judul, juga tidak bentrok dengan folder gambar lama. */
function unique_slug(string $title): string
{
    $base  = slugify($title);
    $base  = $base === 'image' ? 'proyek' : $base;
    $taken = array_column(projects(), 'slug');
    $slug  = $base;
    for ($i = 2; in_array($slug, $taken, true) || is_dir(ROOT . '/assets/img/work/' . $slug); $i++) {
        $slug = $base . '-' . $i;
    }
    return $slug;
}

function project_index(string $slug): int
{
    foreach (projects() as $i => $pr) {
        if (($pr['slug'] ?? '') === $slug) {
            return $i;
        }
    }
    return -1;
}

/** Baca pesan dari form kontak (terbaru dulu). */
function messages(int $limit = 200): array
{
    $f = STORAGE . '/messages.jsonl';
    if (!is_file($f)) {
        return [];
    }
    $out = [];
    foreach (array_reverse(file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: []) as $line) {
        $m = json_decode($line, true);
        if (is_array($m)) {
            $out[] = $m;
        }
        if (count($out) >= $limit) {
            break;
        }
    }
    return $out;
}

function json_out(array $data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
