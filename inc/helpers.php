<?php
declare(strict_types=1);

/** Fungsi bersama untuk semua versi halaman (/ dan /v2/). */
const ROOT = __DIR__ . '/..';

/** File hasil edit dari dashboard admin (menimpa nilai di data/content.php). */
const CONTENT_OVERRIDE = ROOT . '/storage/content.json';

/**
 * Konten website: nilai bawaan dari data/content.php,
 * ditimpa oleh perubahan yang disimpan lewat dashboard admin.
 */
function site_content(bool $fresh = false): array
{
    static $c = null;
    if ($c !== null && !$fresh) {
        return $c;
    }
    $c = require ROOT . '/data/content.php';
    $o = is_file(CONTENT_OVERRIDE) ? json_decode((string)file_get_contents(CONTENT_OVERRIDE), true) : null;
    if (is_array($o)) {
        foreach ($o as $k => $v) {
            if (!array_key_exists($k, $c)) {
                continue;
            }
            $assoc = is_array($c[$k]) && !array_is_list($c[$k]) && is_array($v) && ($v === [] || !array_is_list($v));
            $c[$k] = $assoc ? array_replace($c[$k], $v) : $v;
        }
    }
    return $c;
}

/** Escape untuk output HTML. */
function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/** Pecah teks jadi kata yang dibungkus span (untuk animasi reveal). */
function words(string $s, string $class = 'w'): string
{
    $out = [];
    foreach (preg_split('/\s+/', trim($s)) as $i => $w) {
        $out[] = '<span class="' . $class . '" style="--wi:' . $i . '"><span>' . e($w) . '</span></span>';
    }
    return implode(' ', $out);
}

/** Daftar gambar di sebuah folder (relatif ke root situs), urut nama file. */
function images_in(string $dir): array
{
    $abs = ROOT . '/' . trim($dir, '/');
    if (!is_dir($abs)) {
        return [];
    }
    $files = glob($abs . '/*.{jpg,jpeg,png,webp,avif,gif,svg,JPG,JPEG,PNG,WEBP}', GLOB_BRACE) ?: [];
    natcasesort($files);
    return array_values(array_map(fn($f) => trim($dir, '/') . '/' . rawurlencode(basename($f)), $files));
}

/** "01-logo-kopi-senja.png" → "Logo Kopi Senja" */
function label_from_file(string $path): string
{
    $name = pathinfo(rawurldecode(basename($path)), PATHINFO_FILENAME);
    $name = preg_replace('/^\d+[\s._-]*/', '', $name);
    return ucwords(trim(str_replace(['-', '_'], ' ', $name)));
}

/** Gambar proyek dari assets/img/work/<slug>/ */
function project_images(array $pr): array
{
    return empty($pr['slug']) ? [] : images_in('assets/img/work/' . basename($pr['slug']));
}

/** Foto profil: assets/img/profile.(jpg|png|webp) */
function profile_photo(): ?string
{
    foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
        if (is_file(ROOT . '/assets/img/profile.' . $ext)) {
            return 'assets/img/profile.' . $ext . '?v=' . filemtime(ROOT . '/assets/img/profile.' . $ext);
        }
    }
    return null;
}

/** Foto potongan (PNG transparan) untuk hero v2: assets/img/profile-cutout.png */
function profile_cutout(): ?string
{
    $f = ROOT . '/assets/img/profile-cutout.png';
    return is_file($f) ? 'assets/img/profile-cutout.png?v=' . filemtime($f) : null;
}

/**
 * Versi berwarna dari foto hitam-putih (untuk efek hover).
 * Hanya dipakai kalau tidak lebih tua dari foto utamanya, supaya foto baru
 * dari dashboard tidak tertimpa warna foto lama.
 */
function profile_color(string $kind): ?string
{
    [$main, $color] = $kind === 'cutout'
        ? ['profile-cutout.png', 'profile-cutout-color.webp']
        : ['profile.jpg', 'profile-color.jpg'];
    $m = ROOT . '/assets/img/' . $main;
    $c = ROOT . '/assets/img/' . $color;
    if (!is_file($m) || !is_file($c) || filemtime($c) < filemtime($m) - 5) {
        return null;
    }
    return 'assets/img/' . $color . '?v=' . filemtime($c);
}
