<?php
/**
 * Export desain /v3/ menjadi website statis (HTML biasa) di folder docs/,
 * siap di-upload ke GitHub Pages, Vercel, Netlify, atau Cloudflare Pages.
 *
 * Jalankan dari folder proyek:   php tools/export.php
 *
 * Semua isi diambil dari website lokal (termasuk editan dashboard di storage/),
 * jadi edit dulu lewat /admin/, lalu jalankan perintah ini.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../inc/helpers.php';
$out = ROOT . '/docs';
$c = site_content();

/* 1. Nyalakan server PHP sementara di port acak */
$port = random_int(18000, 18999);
$cmd  = [PHP_BINARY, '-S', "127.0.0.1:$port", '-t', ROOT, ROOT . '/router.php'];
$srv  = proc_open($cmd, [1 => ['file', '/dev/null', 'w'], 2 => ['file', '/dev/null', 'w']], $pipes, ROOT);
if (!is_resource($srv)) {
    fwrite(STDERR, "Gagal menyalakan server PHP.\n");
    exit(1);
}
$base = "http://127.0.0.1:$port";
for ($i = 0; $i < 50 && !@file_get_contents("$base/v3/"); $i++) {
    usleep(100_000);
}

function fetch(string $url, ?int &$status = null): string
{
    $ctx  = stream_context_create(['http' => ['ignore_errors' => true, 'timeout' => 20]]);
    $body = (string)@file_get_contents($url, false, $ctx);
    $status = 0;
    foreach ($http_response_header ?? [] as $h) {
        if (preg_match('#^HTTP/\S+\s+(\d{3})#', $h, $m)) {
            $status = (int)$m[1];
        }
    }
    return $body;
}

/** Ubah link PHP menjadi link file .html */
function to_static(string $html): string
{
    $html = preg_replace_callback('#project\.php\?p=([A-Za-z0-9%_.-]+)#', fn($m) => 'project-' . rawurldecode($m[1]) . '.html', $html);
    $html = str_replace(['projects.php?cat=', 'projects.php'], ['projects.html?cat=', 'projects.html'], $html);
    $html = str_replace(['"../assets/img/', "'../assets/img/", '(../assets/img/'], ['"img/', "'img/", '(img/'], $html);
    $html = str_replace('action="../api/contact.php"', 'action="#"', $html);
    $html = preg_replace('#<html([^>]*)>#', '<html$1 data-static>', $html, 1);
    return $html;
}

/** Salin folder (tanpa file server seperti .htaccess / .php) */
function copy_dir(string $from, string $to): int
{
    $n = 0;
    if (!is_dir($from)) {
        return 0;
    }
    @mkdir($to, 0755, true);
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
    foreach ($it as $f) {
        $rel = substr($f->getPathname(), strlen($from) + 1);
        if (preg_match('#(^|/)\.|\.(php|phtml|phar)$#i', $rel)) {
            continue;
        }
        $dest = "$to/$rel";
        if ($f->isDir()) {
            @mkdir($dest, 0755, true);
        } elseif (!is_file($dest) || filemtime($dest) < $f->getMTime() || filesize($dest) !== $f->getSize()) {
            copy($f->getPathname(), $dest);
            $n++;
        }
    }
    return $n;
}

try {
    @mkdir($out, 0755, true);

    /* Hapus halaman .html lama (mis. proyek yang sudah dihapus) — hanya file .html di docs/ */
    foreach (glob("$out/*.html") ?: [] as $old) {
        unlink($old);
    }

    $pages = ['index.html' => '/v3/', 'projects.html' => '/v3/projects.php'];
    foreach ($c['projects'] as $pr) {
        if (!empty($pr['slug']) && preg_match('/^[a-z0-9-]+$/', $pr['slug'])) {
            $pages['project-' . $pr['slug'] . '.html'] = '/v3/project.php?p=' . rawurlencode($pr['slug']);
        }
    }
    $pages['404.html'] = '/v3/project.php?p=__tidak-ada__';

    foreach ($pages as $file => $path) {
        $html = fetch($base . $path, $status);
        if ($html === '' || ($status !== 200 && $file !== '404.html')) {
            throw new RuntimeException("Gagal mengambil $path (status $status)");
        }
        file_put_contents("$out/$file", to_static($html));
        echo "  ✓ $file\n";
    }

    $n  = copy_dir(ROOT . '/v3/assets', "$out/assets");
    $n += copy_dir(ROOT . '/assets/img', "$out/img");
    file_put_contents("$out/.nojekyll", '');
    echo "  ✓ $n file aset disalin\n";
    echo "\nSelesai. Website statis ada di folder docs/ (" . count($pages) . " halaman).\n";
} catch (Throwable $e) {
    fwrite(STDERR, "\nExport gagal: " . $e->getMessage() . "\n");
    $fail = true;
} finally {
    proc_terminate($srv);
    proc_close($srv);
}
exit(empty($fail) ? 0 : 1);
