<?php
declare(strict_types=1);

/** Bagian bersama untuk semua halaman /v3/ (tidak dibuka langsung). */
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../../inc/helpers.php';
$c = site_content();

/** Path aset relatif dari /v3/ */
function u(string $p): string
{
    return '../' . ltrim($p, '/');
}

/** Ikon bintang 4 sudut */
function spark(string $cls = 'spark'): string
{
    return '<svg class="' . $cls . '" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 0c.6 6.4 5.6 11.4 12 12-6.4.6-11.4 5.6-12 12-.6-6.4-5.6-11.4-12-12C6.4 11.4 11.4 6.4 12 0Z" fill="currentColor"/></svg>';
}

function arrow(): string
{
    return '<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M4.5 11.5 11.5 4.5M5.5 4.5h6v6" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

/** Ikon media sosial berdasarkan nama */
function social_icon(string $label): string
{
    $l = strtolower($label);
    $p = match (true) {
        str_contains($l, 'insta')   => '<rect x="4" y="4" width="16" height="16" rx="5" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3.6" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="16.8" cy="7.2" r="1.1" fill="currentColor"/>',
        str_contains($l, 'behance') => '<path d="M3.5 7h4.2c1.7 0 2.8.9 2.8 2.3 0 1-.6 1.6-1.3 1.9 1 .3 1.7 1.1 1.7 2.3 0 1.7-1.3 2.5-3.1 2.5H3.5Zm2 3.5h2c.7 0 1.1-.4 1.1-1s-.4-.9-1.1-.9h-2Zm0 3.8h2.2c.8 0 1.3-.4 1.3-1.1s-.5-1.1-1.3-1.1H5.5Z" fill="currentColor"/><path d="M14.2 7.6h4.6" stroke="currentColor" stroke-width="1.5"/><path d="M13.2 13.2c0-2 1.4-3.5 3.3-3.5s3.1 1.4 3.1 3.6v.4h-4.5c.1.9.7 1.4 1.6 1.4.6 0 1-.2 1.3-.6h1.6c-.4 1.3-1.5 2-2.9 2-2 0-3.5-1.4-3.5-3.3Zm4.5-.7c-.1-.8-.6-1.3-1.3-1.3s-1.2.5-1.3 1.3Z" fill="currentColor"/>',
        str_contains($l, 'dribbble') => '<circle cx="12" cy="12" r="8" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M5 9.5c4 .4 8.5-.4 12-3M8.5 4.8c2.6 3.4 4.6 8.4 5.6 14.6M4.3 13.6c4.4-2.4 10-2.8 15.4-.6" fill="none" stroke="currentColor" stroke-width="1.5"/>',
        str_contains($l, 'linkedin') => '<rect x="4.5" y="9.5" width="3" height="9.5" fill="currentColor"/><circle cx="6" cy="6.3" r="1.7" fill="currentColor"/><path d="M10.5 9.5h2.9v1.4c.5-.9 1.6-1.6 3-1.6 2.2 0 3.1 1.4 3.1 3.9V19h-3v-5.2c0-1.2-.4-1.9-1.4-1.9s-1.6.7-1.6 1.9V19h-3Z" fill="currentColor"/>',
        str_contains($l, 'tiktok')   => '<path d="M14 4v9.8a3.3 3.3 0 1 1-2.8-3.3V7.6a6.2 6.2 0 1 0 5.7 6.2V9.3a6 6 0 0 0 3.1 1V7.4A3.6 3.6 0 0 1 16.9 4Z" fill="currentColor"/>',
        str_contains($l, 'mail')     => '<rect x="3.5" y="5.5" width="17" height="13" rx="2" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="m4 7 8 6 8-6" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>',
        str_contains($l, 'whatsapp') => '<path d="M12 4a8 8 0 0 0-6.9 12l-1.1 4 4.1-1.1A8 8 0 1 0 12 4Z" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M9.3 8.6c.2-.4.5-.4.7-.4h.5c.2 0 .4.1.5.4l.7 1.6c.1.2 0 .4-.1.6l-.5.6c.6 1.1 1.5 2 2.6 2.6l.6-.5c.2-.2.4-.2.6-.1l1.6.7c.3.1.4.3.4.5v.5c0 .3-.1.6-.5.8-.6.3-1.6.5-3-.2a8.6 8.6 0 0 1-3.8-3.8c-.6-1.3-.5-2.4-.3-3.3Z" fill="currentColor"/>',
        default => '<circle cx="12" cy="12" r="8" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M4 12h16M12 4c2.4 2.2 3.5 5 3.5 8s-1.1 5.8-3.5 8c-2.4-2.2-3.5-5-3.5-8S9.6 6.2 12 4Z" fill="none" stroke="currentColor" stroke-width="1.5"/>',
    };
    return '<svg viewBox="0 0 24 24" aria-hidden="true">' . $p . '</svg>';
}

/** Ikon software: [singkatan, warna teks, warna latar] */
function tool_badge(string $name): array
{
    $l = strtolower($name);
    return match (true) {
        str_contains($l, 'illustrator') => ['Ai', '#ff9a00', '#330000'],
        str_contains($l, 'photoshop')   => ['Ps', '#31a8ff', '#001e36'],
        str_contains($l, 'indesign')    => ['Id', '#ff3366', '#49021f'],
        str_contains($l, 'figma')       => ['Fg', '#ffffff', '#1e1e1e'],
        str_contains($l, 'dimension')   => ['Dn', '#4bf2a8', '#00261b'],
        str_contains($l, 'after')       => ['Ae', '#9999ff', '#00005b'],
        str_contains($l, 'premiere')    => ['Pr', '#9999ff', '#00005b'],
        str_contains($l, 'blender')     => ['Bl', '#ffffff', '#ea7600'],
        str_contains($l, 'procreate')   => ['Pc', '#ffffff', '#2b2b2b'],
        str_contains($l, 'canva')       => ['Cv', '#ffffff', '#00c4cc'],
        str_contains($l, 'corel')       => ['Cd', '#ffffff', '#1a9a3c'],
        default => [mb_substr($name, 0, 2), '#0b0b0b', '#c8f31d'],
    };
}

/** Visual proyek: gambar pertama, atau mockup CSS sesuai jenisnya */
function project_art(array $pr, array $imgs): string
{
    if ($imgs) {
        return '<img src="' . e(u($imgs[0])) . '" alt="' . e($pr['title']) . '" loading="lazy" decoding="async">';
    }
    $m = $pr['mock'] ?? 'pack';
    if ($m === 'pack') {
        $k = $pr['pack'] ?? [];
        $st = '--c1:' . e($k['c1'] ?? '#ff5a1f') . ';--c2:' . e($k['c2'] ?? '#ffd8c7') . ';--c3:' . e($k['c3'] ?? '#fff4ee');
        return '<div class="art art--pack" style="' . $st . '"><span class="art-pouch"><i></i><b>' . e($k['brand'] ?? $pr['client']) . '</b><small>' . e($k['product'] ?? '') . '</small><em>' . e($k['size'] ?? '') . '</em></span><span class="art-box"><b>' . e($k['brand'] ?? $pr['client']) . '</b></span></div>';
    }
    if ($m === 'brand') {
        $b = $pr['brand'] ?? [];
        $st = '--c1:' . e($b['c1'] ?? '#111') . ';--c2:' . e($b['c2'] ?? '#ff5a1f') . ';--c3:' . e($b['c3'] ?? '#f6f6f6') . ';--c4:' . e($b['c4'] ?? '#ffd8c7');
        $leaf = '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M32 6c14 10 18 24 10 38-4 7-10 12-10 14 0-2-6-7-10-14C14 30 18 16 32 6Z" fill="currentColor"/></svg>';
        return '<div class="art art--brand" style="' . $st . '"><span class="t1">' . $leaf . '<b>' . e($b['name'] ?? $pr['client']) . '</b><small>' . e($b['tagline'] ?? '') . '</small></span><span class="t2">' . $leaf . '</span><span class="t3">' . $leaf . '</span><span class="t4"><i></i><i></i><i></i><i></i></span></div>';
    }
    return '<div class="art art--ui"><span class="ph"><i class="a"></i><i class="b"></i><i class="c"></i><i class="d"></i></span><span class="ph ph--2"><i class="a"></i><i class="c"></i><i class="b"></i></span></div>';
}

/** Visual layanan (CSS) */
function service_art(int $i): string
{
    return match ($i % 5) {
        0 => '<div class="sart sart--logo"><span>' . spark() . '</span><span></span><span></span><span></span></div>',
        1 => '<div class="sart sart--pack"><span class="art-pouch"><i></i><b>Brand</b></span><span class="art-box"><b>Brand</b></span></div>',
        2 => '<div class="sart sart--print"><span><b>SALE</b></span><span><b>FEST</b></span><span></span></div>',
        3 => '<div class="sart sart--social"><span></span><span></span><span></span><span></span><span></span><span></span></div>',
        default => '<div class="sart sart--ui"><span class="ph"><i class="a"></i><i class="b"></i><i class="c"></i></span><span class="ph ph--2"><i class="a"></i><i class="b"></i></span></div>',
    };
}

$p       = $c['person'];
$v       = $c['v3'];
$full    = $p['first'] . ' ' . $p['last'];
$cutout  = profile_cutout();
$photo   = profile_photo();
$cutoutC = $cutout ? profile_color('cutout') : null;
$photoC  = $photo ? profile_color('photo') : null;
$gallery = images_in('assets/img/gallery');
$wa      = !empty($p['whatsapp']) ? 'https://wa.me/' . preg_replace('/\D+/', '', $p['whatsapp']) : 'mailto:' . $p['email'];
$waText  = fn(string $t) => !empty($p['whatsapp']) ? $wa . '?text=' . rawurlencode($t) : $wa;
$stats   = $c['about']['stats'];
$cats    = array_values(array_unique(array_map(fn($x) => $x['category'] ?? 'Other', $c['projects'])));
$ribbon  = array_map(fn($s) => $s['title'], $c['services']);
$initials = mb_strtoupper(mb_substr($p['first'], 0, 1) . mb_substr($p['last'], 0, 1));
$years   = $stats[0] ?? ['value' => 4, 'suffix' => '+', 'label' => 'Years of experience'];
$nav     = [['home', 'Home'], ['portfolio', 'Portfolio'], ['about', 'About'], ['resume', 'Experience'], ['contact', 'Contact'], ['faq', 'FAQ']];

$ribbonHtml = function () use ($ribbon): string {
    $one = '';
    foreach ($ribbon as $r) {
        $one .= '<span>' . e($r) . '</span>' . spark();
    }
    return '<div class="ribbon" aria-hidden="true"><div class="ribbon__track"><div>' . $one . '</div><div>' . $one . '</div></div></div>';
};

/** Awalan link ke bagian beranda: "#" di beranda, "./#" di halaman lain */
$isHome = $isHome ?? false;
$L      = $isHome ? '#' : './#';

/** Link halaman detail proyek */
function project_url(array $pr): string
{
    return 'project.php?p=' . rawurlencode($pr['slug']);
}

/** Proyek yang ditandai tampil di beranda; kalau tidak ada yang ditandai, 4 proyek pertama. */
function featured_projects(array $all, int $max = 4): array
{
    $f = array_values(array_filter($all, fn($p) => !empty($p['featured'])));
    return array_slice($f ?: $all, 0, $max);
}

/** Paragraf dari teks dengan baris kosong sebagai pemisah */
function paragraphs(string $s): string
{
    $out = '';
    foreach (preg_split('/\n\s*\n/', trim($s)) ?: [] as $para) {
        if (trim($para) !== '') {
            $out .= '<p>' . nl2br(e(trim($para)), false) . '</p>';
        }
    }
    return $out;
}


/**
 * Kategori proyek untuk sebuah layanan.
 * Pakai isian 'category' di layanan; kalau kosong, dicocokkan dari kata di judulnya
 * ("Packaging design" → "Packaging", "Logo & brand identity" → "Logo & Brand identity").
 */
function service_category(array $s, array $cats): ?string
{
    $want = trim((string)($s['category'] ?? ''));
    if ($want !== '') {
        foreach ($cats as $k) {
            if (strcasecmp($k, $want) === 0) {
                return $k;
            }
        }
        return null;
    }
    $tok = fn(string $t) => array_diff(preg_split('/[^a-z0-9\/]+/', strtolower($t)) ?: [], ['', 'design', 'and', 'the', 'desain', 'dan']);
    $mine = $tok($s['title'] ?? '');
    $best = null;
    $score = 0;
    foreach ($cats as $k) {
        $n = count(array_intersect($mine, $tok($k)));
        if ($n > $score) {
            [$best, $score] = [$k, $n];
        }
    }
    return $best;
}

/** Proyek dalam satu kategori */
function projects_in(array $all, ?string $cat): array
{
    return $cat === null ? [] : array_values(array_filter($all, fn($x) => ($x['category'] ?: 'Other') === $cat));
}
