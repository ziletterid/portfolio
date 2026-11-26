<?php
declare(strict_types=1);

require __DIR__ . '/lib.php';

admin_session();
header('X-Content-Type-Options: nosniff');

if (!is_logged_in()) {
    json_out(['ok' => false, 'error' => 'Sesi habis, silakan login lagi.'], 401);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$isJson = str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
$in     = $isJson ? (json_decode((string)file_get_contents('php://input'), true) ?: []) : $_POST;
$action = (string)($in['action'] ?? $_GET['action'] ?? 'state');

if ($method !== 'GET' && !csrf_ok($_SERVER['HTTP_X_CSRF'] ?? null)) {
    json_out(['ok' => false, 'error' => 'Token keamanan tidak valid. Muat ulang halaman.'], 403);
}

/** Folder target yang valid atau hentikan request. */
function need_dir(string $target): string
{
    $rel = target_dir($target);
    if (!$rel) {
        json_out(['ok' => false, 'error' => 'Target tidak dikenal.'], 400);
    }
    ensure_dir(ROOT . '/' . $rel);
    return $rel;
}

/** Nama file yang memang ada di folder, atau hentikan request. */
function need_file(string $rel, string $name): string
{
    $name = basename($name);
    if (!in_array($name, list_files($rel), true)) {
        json_out(['ok' => false, 'error' => 'File tidak ditemukan.'], 404);
    }
    return $name;
}

function state(): array
{
    $profile = null;
    foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
        if (is_file(ROOT . "/assets/img/profile.$ext")) {
            $profile = file_info('assets/img', "profile.$ext");
            break;
        }
    }
    $cutout = is_file(ROOT . '/assets/img/profile-cutout.png') ? file_info('assets/img', 'profile-cutout.png') : null;

    $projects = [];
    foreach (projects() as $pr) {
        $t   = 'project:' . ($pr['slug'] ?? '');
        $rel = target_dir($t);
        if (!$rel) {
            continue;
        }
        $projects[] = [
            'target' => $t,
            'slug'   => $pr['slug'],
            'data'   => $pr,
            'files'  => is_dir(ROOT . '/' . $rel) ? array_map(fn($n) => file_info($rel, $n), list_files($rel)) : [],
        ];
    }
    // Daftar kategori proyek untuk pilihan isian ('list' => '@categories')
    $cats = array_values(array_unique(array_filter(array_map(fn($x) => $x['category'] ?? '', projects()))));
    $fill = function (array $fields) use (&$fill, $cats): array {
        foreach ($fields as &$f) {
            if (($f['list'] ?? null) === '@categories') {
                $f['list'] = $cats;
            }
            if (isset($f['fields'])) {
                $f['fields'] = $fill($f['fields']);
            }
        }
        return $fields;
    };
    $panels = [];
    foreach (schema()['panels'] as $id => $panel) {
        $panel['fields'] = $fill($panel['fields']);
        $panels[$id] = $panel + ['values' => panel_values($panel)];
    }
    $g = 'assets/img/gallery';
    return [
        'ok'            => true,
        'profile'       => $profile,
        'cutout'        => $cutout,
        'projects'      => $projects,
        'projectFields' => schema()['project'],
        'panels'        => $panels,
        'gallery'       => array_map(fn($n) => file_info($g, $n), list_files($g)),
        'messages'      => messages(),
    ];
}

/** Lengkapi data proyek agar mockup tidak kosong. */
function fill_project(array $pr): array
{
    if ($pr['title'] === '') {
        $pr['title'] = 'Proyek tanpa judul';
    }
    if ($pr['client'] === '') {
        $pr['client'] = $pr['title'];
    }
    $pr['pack']['brand']   = ($pr['pack']['brand'] ?? '') !== '' ? $pr['pack']['brand'] : $pr['client'];
    $pr['pack']['product'] = ($pr['pack']['product'] ?? '') !== '' ? $pr['pack']['product'] : $pr['title'];
    $pr['brand']['name']   = ($pr['brand']['name'] ?? '') !== '' ? $pr['brand']['name'] : $pr['client'];
    $pr['brand']['tagline'] = ($pr['brand']['tagline'] ?? '') !== '' ? $pr['brand']['tagline'] : $pr['category'];
    return $pr;
}

/** Proyek yang ada, atau hentikan request. */
function need_project(string $slug): int
{
    $i = project_index($slug);
    if ($i < 0) {
        json_out(['ok' => false, 'error' => 'Proyek tidak ditemukan. Muat ulang halaman.'], 404);
    }
    return $i;
}

switch ($action) {
    case 'state':
        json_out(state());

    case 'upload':
        $rel   = need_dir((string)($in['target'] ?? ''));
        $files = $_FILES['files'] ?? null;
        if (!$files || !is_array($files['name'])) {
            json_out(['ok' => false, 'error' => 'Tidak ada file yang dikirim.'], 400);
        }
        $caption  = trim((string)($in['caption'] ?? ''));
        $existing = list_files($rel);
        if ($existing) {
            renumber($rel, $existing);   // rapikan dulu: 01-…, 02-…
        }
        $errors = [];
        $added  = 0;
        $multi  = count($files['name']) > 1;
        foreach (array_keys($files['name']) as $k) {
            $f = [
                'name' => $files['name'][$k], 'type' => $files['type'][$k], 'tmp_name' => $files['tmp_name'][$k],
                'error' => $files['error'][$k], 'size' => $files['size'][$k],
            ];
            $base = slugify($caption !== '' ? $caption . ($multi ? ' ' . ($k + 1) : '') : pathinfo((string)$f['name'], PATHINFO_FILENAME));
            $stem = sprintf('%02d-%s', count($existing) + $added + 1, $base);
            while (glob(ROOT . '/' . $rel . '/' . $stem . '.*')) {
                $stem .= '-2';
            }
            try {
                accept_upload($f, ROOT . '/' . $rel . '/' . $stem);
                $added++;
            } catch (RuntimeException $e) {
                $errors[] = $f['name'] . ': ' . $e->getMessage();
            }
        }
        json_out(['ok' => $added > 0, 'added' => $added, 'errors' => $errors, 'error' => $added ? null : ($errors[0] ?? 'Upload gagal.')] + ($added ? state() : []), $added ? 200 : 422);

    case 'reorder':
        $rel   = need_dir((string)($in['target'] ?? ''));
        $order = array_map('basename', (array)($in['order'] ?? []));
        $have  = list_files($rel);
        $s1 = $order; sort($s1);
        $s2 = $have;  sort($s2);
        if ($s1 !== $s2) {
            json_out(['ok' => false, 'error' => 'Daftar file berubah. Muat ulang halaman.'], 409);
        }
        renumber($rel, $order);
        json_out(state());

    case 'cover':
        $rel   = need_dir((string)($in['target'] ?? ''));
        $name  = need_file($rel, (string)($in['name'] ?? ''));
        $order = array_values(array_diff(list_files($rel), [$name]));
        array_unshift($order, $name);
        renumber($rel, $order);
        json_out(state());

    case 'delete':
        $rel  = need_dir((string)($in['target'] ?? ''));
        $name = need_file($rel, (string)($in['name'] ?? ''));
        trash(ROOT . "/$rel/$name");
        $left = list_files($rel);
        if ($left) {
            renumber($rel, $left);
        }
        json_out(state());

    case 'caption':
        $rel     = need_dir((string)($in['target'] ?? ''));
        $name    = need_file($rel, (string)($in['name'] ?? ''));
        $caption = trim((string)($in['caption'] ?? ''));
        if ($caption === '') {
            json_out(['ok' => false, 'error' => 'Keterangan tidak boleh kosong.'], 422);
        }
        preg_match('/^(\d+)/', $name, $m);
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $new = sprintf('%s-%s.%s', $m[1] ?? '99', slugify($caption), $ext);
        if ($new !== $name) {
            if (is_file(ROOT . "/$rel/$new")) {
                json_out(['ok' => false, 'error' => 'Nama itu sudah dipakai gambar lain.'], 409);
            }
            rename(ROOT . "/$rel/$name", ROOT . "/$rel/$new");
        }
        json_out(state());

    case 'profile':
        $kind = (string)($in['kind'] ?? 'photo');
        $f    = $_FILES['file'] ?? null;
        if (!$f) {
            json_out(['ok' => false, 'error' => 'Tidak ada file.'], 400);
        }
        $dir = ROOT . '/assets/img';
        $tmp = $dir . '/.upload-' . bin2hex(random_bytes(4));
        try {
            $ext = accept_upload($f, $tmp);
        } catch (RuntimeException $e) {
            json_out(['ok' => false, 'error' => $e->getMessage()], 422);
        }
        if ($kind === 'cutout') {
            if ($ext !== 'png') {
                @unlink("$tmp.$ext");
                json_out(['ok' => false, 'error' => 'Foto potongan harus PNG transparan.'], 422);
            }
            if (is_file("$dir/profile-cutout.png")) {
                trash("$dir/profile-cutout.png");
            }
            rename("$tmp.png", "$dir/profile-cutout.png");
        } else {
            if (!in_array($ext, ['jpg', 'png', 'webp'], true)) {
                @unlink("$tmp.$ext");
                json_out(['ok' => false, 'error' => 'Foto profil harus JPG, PNG, atau WebP.'], 422);
            }
            foreach (['jpg', 'jpeg', 'png', 'webp'] as $old) {
                if (is_file("$dir/profile.$old")) {
                    trash("$dir/profile.$old");
                }
            }
            rename("$tmp.$ext", "$dir/profile.$ext");
        }
        json_out(state());

    case 'profile_delete':
        $kind = (string)($in['kind'] ?? 'photo');
        $dir  = ROOT . '/assets/img';
        $list = $kind === 'cutout' ? ['profile-cutout.png'] : ['profile.jpg', 'profile.jpeg', 'profile.png', 'profile.webp'];
        foreach ($list as $n) {
            if (is_file("$dir/$n")) {
                trash("$dir/$n");
            }
        }
        json_out(state());

    case 'content_save':
        $panel = schema()['panels'][(string)($in['panel'] ?? '')] ?? null;
        if (!$panel) {
            json_out(['ok' => false, 'error' => 'Bagian tidak dikenal.'], 400);
        }
        $values = is_array($in['values'] ?? null) ? $in['values'] : [];
        $o = overrides();
        try {
            foreach ($panel['fields'] as $f) {
                if (array_key_exists($f['path'], $values)) {
                    path_set($o, $f['path'], clean_field($f, $values[$f['path']]));
                }
            }
        } catch (InvalidArgumentException $e) {
            json_out(['ok' => false, 'error' => $e->getMessage()], 422);
        }
        save_overrides($o);
        json_out(state());

    case 'content_reset':
        $panel = schema()['panels'][(string)($in['panel'] ?? '')] ?? null;
        if (!$panel) {
            json_out(['ok' => false, 'error' => 'Bagian tidak dikenal.'], 400);
        }
        $o = overrides();
        foreach ($panel['fields'] as $f) {
            path_unset($o, $f['path']);
        }
        save_overrides($o);
        json_out(state());

    case 'project_add':
        $title = clean_text($in['title'] ?? '', 80);
        if ($title === '') {
            json_out(['ok' => false, 'error' => 'Isi judul proyek dulu.'], 422);
        }
        $list = projects();
        if (count($list) >= 40) {
            json_out(['ok' => false, 'error' => 'Maksimal 40 proyek.'], 422);
        }
        $pr = clean_item(schema()['project'], [
            'title' => $title, 'client' => $title, 'category' => 'Packaging', 'year' => date('Y'),
            'role' => '', 'summary' => '', 'tags' => [], 'metrics' => [], 'mock' => 'pack',
        ]);
        $pr = ['slug' => unique_slug($title)] + fill_project($pr);
        ensure_dir(ROOT . '/assets/img/work/' . $pr['slug']);
        $list[] = $pr;
        save_projects($list);
        json_out(state() + ['slug' => $pr['slug']]);

    case 'project_save':
        $i    = need_project((string)($in['slug'] ?? ''));
        $list = projects();
        try {
            $pr = clean_item(schema()['project'], is_array($in['data'] ?? null) ? $in['data'] : []);
        } catch (InvalidArgumentException $e) {
            json_out(['ok' => false, 'error' => $e->getMessage()], 422);
        }
        $list[$i] = ['slug' => $list[$i]['slug']] + fill_project($pr);
        save_projects($list);
        json_out(state());

    case 'project_move':
        $i    = need_project((string)($in['slug'] ?? ''));
        $list = projects();
        $j    = $i + ((int)($in['dir'] ?? 0) < 0 ? -1 : 1);
        if ($j < 0 || $j >= count($list)) {
            json_out(state());
        }
        [$list[$i], $list[$j]] = [$list[$j], $list[$i]];
        save_projects($list);
        json_out(state());

    case 'project_delete':
        $i    = need_project((string)($in['slug'] ?? ''));
        $list = projects();
        $slug = $list[$i]['slug'];
        array_splice($list, $i, 1);
        save_projects($list);
        $dir = ROOT . '/assets/img/work/' . $slug;
        if (is_dir($dir) && preg_match('/^[a-z0-9][a-z0-9-]{0,80}$/', $slug)) {
            if (array_diff(scandir($dir) ?: [], ['.', '..'])) {
                trash($dir);   // folder beserta gambarnya ke storage/trash
            } else {
                rmdir($dir);
            }
        }
        json_out(state());

    default:
        json_out(['ok' => false, 'error' => 'Aksi tidak dikenal.'], 400);
}
