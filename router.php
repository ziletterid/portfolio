<?php
/**
 * Router untuk server lokal `php -S` (meniru aturan .htaccess):
 *   php -S localhost:8080 router.php
 * Folder storage/, data/, inc/ dan admin/lib.php & admin/schema.php tidak boleh dibuka lewat browser.
 */
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
if (preg_match('#^/(storage|data|inc|tools|docs)(/|$)#', $path) || str_contains($path, '/_parts/') || in_array($path, ['/admin/lib.php', '/admin/schema.php'], true) || $path === '/router.php' || str_contains($path, '/.')) {
    http_response_code(404);
    echo 'Not found';
    return true;
}
if (str_starts_with($path, '/assets/img/') && preg_match('/\.svg$/i', $path)) {
    header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'");
}
return false;
