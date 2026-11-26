<?php /* Kepala halaman + header /v3/ */ if (!isset($c)) { http_response_code(404); exit; } ?>
<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? $c['meta']['title']) ?></title>
    <meta name="description" content="<?= e($pageDesc ?? $c['meta']['description']) ?>">
    <meta name="theme-color" content="#0c0c0c">
    <meta property="og:title" content="<?= e($c['meta']['title']) ?>">
    <meta property="og:description" content="<?= e($c['meta']['description']) ?>">
    <link rel="icon" href="<?= u('assets/img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500&family=Caveat:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/v3.css?v=<?= filemtime(__DIR__ . '/../assets/v3.css') ?>">
    <script>document.documentElement.classList.replace('no-js', 'js');</script>
</head>
<body>

<a class="skip" href="#main">Skip to content</a>

<!-- ============ HEADER ============ -->
<header class="hdr" data-hdr>
    <div class="hdr__in">
        <a class="logo" href="<?= $L ?>home" aria-label="<?= e($full) ?>, back to top"><span class="logo__mark"><?= e($initials[0]) ?></span><span><?= e($p['first']) ?><i>.</i></span></a>
        <nav class="hdr__nav" aria-label="Main">
            <?php foreach ($nav as [$id, $label]): ?><a href="<?= $L . $id ?>"><?= e($label) ?></a><?php endforeach; ?>
        </nav>
        <a class="btn btn--dark hdr__cta" href="<?= $L ?>contact">Contact Me</a>
        <button class="burger" type="button" aria-label="Open menu" aria-expanded="false" data-burger><span></span><span></span></button>
    </div>
    <nav class="mnav" data-mnav hidden aria-label="Mobile">
        <?php foreach ($nav as [$id, $label]): ?><a href="<?= $L . $id ?>"><?= e($label) ?></a><?php endforeach; ?>
        <a href="<?= $L ?>contact" class="btn btn--accent">Contact Me</a>
    </nav>
</header>

