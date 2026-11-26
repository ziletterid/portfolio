<?php
declare(strict_types=1);

require __DIR__ . '/_parts/bootstrap.php';

$all  = $c['projects'];
$cat  = trim((string)($_GET['cat'] ?? ''));
$cat  = in_array($cat, $cats, true) ? $cat : '';
$pageTitle = ($cat ?: 'All projects') . ' — ' . $full;
$pageDesc  = 'Packaging, logo, branding and UI/UX projects by ' . $full . '.';
$counts = array_count_values(array_map(fn($x) => $x['category'] ?: 'Other', $all));

require __DIR__ . '/_parts/head.php';
?>
<main id="main">

<section class="pagehead">
    <div class="hero__grid" aria-hidden="true"></div>
    <div class="wrap">
        <h1>My <em>Projects</em></h1>
        <nav class="crumbs" aria-label="Breadcrumb"><a href="./">Home</a><span>/</span><span aria-current="page">Projects</span></nav>
    </div>
</section>

<?= $ribbonHtml() ?>

<section class="sec">
    <div class="wrap">
        <div class="listbar">
            <div class="filters" role="group" aria-label="Filter by category" data-filters>
                <a href="projects.php" data-f="*" class="<?= $cat === '' ? 'is-on' : '' ?>">All <sup><?= count($all) ?></sup></a>
                <?php foreach ($cats as $k): ?>
                    <a href="projects.php?cat=<?= rawurlencode($k) ?>" data-f="<?= e($k) ?>" class="<?= $cat === $k ? 'is-on' : '' ?>"><?= e($k) ?> <sup><?= $counts[$k] ?? 0 ?></sup></a>
                <?php endforeach; ?>
            </div>
            <label class="search"><svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="9" cy="9" r="5.5" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="m13.2 13.2 3.6 3.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg><input type="search" placeholder="Search projects…" aria-label="Search projects" data-search></label>
        </div>

        <div class="cards cards--grid" data-list>
            <?php foreach ($all as $i => $pr): $k = $pr['category'] ?: 'Other'; $imgs = project_images($pr); ?>
                <a class="pcard reveal" href="<?= e(project_url($pr)) ?>" data-cat="<?= e($k) ?>"
                   data-text="<?= e(mb_strtolower($pr['title'] . ' ' . $pr['client'] . ' ' . implode(' ', $pr['tags'] ?? []) . ' ' . $k)) ?>"
                   style="--i:<?= $i % 3 ?>" <?= $cat !== '' && $cat !== $k ? 'hidden' : '' ?>>
                    <span class="pcard__art"><?= project_art($pr, $imgs) ?><?php if (count($imgs) > 1): ?><span class="work__count"><?= count($imgs) ?> images</span><?php endif; ?></span>
                    <span class="pcard__cat"><?= e($k) ?></span>
                    <b><?= e($pr['title']) ?></b>
                    <small><?= e($pr['client']) ?><?= ($pr['year'] ?? '') !== '' ? ' · ' . e($pr['year']) : '' ?></small>
                    <span class="pcard__more">View case study <?= arrow() ?></span>
                </a>
            <?php endforeach; ?>
        </div>
        <p class="empty" data-empty hidden>No projects match your search.</p>

        <?php if ($gallery): ?>
            <header class="shead shead--sub reveal" id="archive">
                <span class="eyebrow">Archive</span>
                <h2><?= e($c['archive']['title']) ?></h2>
                <p><?= e($c['archive']['intro']) ?></p>
            </header>
            <ul class="gallery">
                <?php foreach ($gallery as $g): ?>
                    <li class="reveal"><button type="button" data-lightbox="gallery" data-src="<?= e(u($g)) ?>" data-caption="<?= e(label_from_file($g)) ?>"><img src="<?= e(u($g)) ?>" alt="<?= e(label_from_file($g)) ?>" loading="lazy" decoding="async"><span><?= e(label_from_file($g)) ?></span></button></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>

<?= $ribbonHtml() ?>

<?php require __DIR__ . '/_parts/foot.php'; ?>
