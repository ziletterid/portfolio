<?php
declare(strict_types=1);

require __DIR__ . '/inc/helpers.php';
$c = site_content();

$p    = $c['person'];
$full = $p['first'] . ' ' . $p['last'];
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($c['meta']['title']) ?></title>
    <meta name="description" content="<?= e($c['meta']['description']) ?>">
    <meta name="theme-color" content="#05040c">
    <meta property="og:title" content="<?= e($c['meta']['title']) ?>">
    <meta property="og:description" content="<?= e($c['meta']['description']) ?>">
    <meta property="og:type" content="website">
    <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300..700&family=Geist+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/assets/css/style.css') ?>">

    <script type="importmap">
    {
        "imports": {
            "three": "https://unpkg.com/three@0.170.0/build/three.module.js",
            "three/addons/": "https://unpkg.com/three@0.170.0/examples/jsm/",
            "lenis": "https://unpkg.com/lenis@1.1.18/dist/lenis.mjs"
        }
    }
    </script>
    <script>document.documentElement.classList.replace('no-js', 'js');</script>
</head>
<body>
    <a class="skip" href="#main">Skip to content</a>

    <?php include __DIR__ . '/partials/loader.php'; ?>
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <main id="main">
        <?php include __DIR__ . '/partials/hero.php'; ?>
        <?php include __DIR__ . '/partials/clients.php'; ?>
        <?php include __DIR__ . '/partials/work.php'; ?>
        <?php include __DIR__ . '/partials/archive.php'; ?>
        <?php include __DIR__ . '/partials/about.php'; ?>
        <?php include __DIR__ . '/partials/services.php'; ?>
        <?php include __DIR__ . '/partials/process.php'; ?>
        <?php include __DIR__ . '/partials/testimonials.php'; ?>
        <?php include __DIR__ . '/partials/contact.php'; ?>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <div class="cursor" aria-hidden="true"><span class="cursor__dot"></span><span class="cursor__ring"><span class="cursor__label"></span></span></div>

    <script type="module" src="assets/js/main.js?v=<?= filemtime(__DIR__ . '/assets/js/main.js') ?>"></script>
</body>
</html>
