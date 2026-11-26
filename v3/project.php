<?php
declare(strict_types=1);

require __DIR__ . '/_parts/bootstrap.php';

$all  = array_values($c['projects']);
$slug = (string)($_GET['p'] ?? '');
$idx  = null;
foreach ($all as $i => $x) {
    if (($x['slug'] ?? '') === $slug) {
        $idx = $i;
        break;
    }
}
if ($idx === null) {
    http_response_code(404);
    $pageTitle = 'Project not found — ' . $full;
    require __DIR__ . '/_parts/head.php';
    echo '<main id="main"><section class="pagehead pagehead--404"><div class="wrap"><h1>Project <em>not found</em></h1><p>It may have been moved or removed.</p><a class="pillbtn" href="projects.php"><span>See all projects</span><i>' . arrow() . '</i></a></div></section>';
    require __DIR__ . '/_parts/foot.php';
    exit;
}

$pr    = $all[$idx];
$imgs  = project_images($pr);
$prev  = $all[($idx - 1 + count($all)) % count($all)];
$next  = $all[($idx + 1) % count($all)];
$cat   = $pr['category'] ?: 'Other';
$more  = array_slice(array_values(array_filter($all, fn($x) => $x['slug'] !== $pr['slug'] && ($x['category'] ?: 'Other') === $cat)), 0, 3);
if (count($more) < 3) {
    $more = array_merge($more, array_slice(array_values(array_filter($all, fn($x) => $x['slug'] !== $pr['slug'] && !in_array($x, $more, true))), 0, 3 - count($more)));
}
$story = trim((string)($pr['body'] ?? ''));
$pageTitle = $pr['title'] . ' — ' . $full;
$pageDesc  = $pr['summary'];

require __DIR__ . '/_parts/head.php';
?>
<main id="main">

<section class="pagehead pagehead--project">
    <div class="hero__grid" aria-hidden="true"></div>
    <div class="wrap">
        <nav class="crumbs" aria-label="Breadcrumb"><a href="./">Home</a><span>/</span><a href="projects.php">Projects</a><span>/</span><a href="projects.php?cat=<?= rawurlencode($cat) ?>"><?= e($cat) ?></a></nav>
        <h1><?= e($pr['title']) ?></h1>
        <p class="pagehead__lead"><?= e($pr['summary']) ?></p>
        <?php if (!empty($pr['tags'])): ?><ul class="ptags"><?php foreach ($pr['tags'] as $t): ?><li><?= e($t) ?></li><?php endforeach; ?></ul><?php endif; ?>
    </div>
</section>

<section class="detail">
    <div class="wrap">
        <?php if ($imgs): ?>
            <button type="button" class="detail__cover" data-lightbox="p" data-src="<?= e(u($imgs[0])) ?>" aria-label="Open image 1 of <?= count($imgs) ?>">
                <img src="<?= e(u($imgs[0])) ?>" alt="<?= e($pr['title']) ?> — cover" fetchpriority="high">
            </button>
        <?php else: ?>
            <div class="detail__cover detail__cover--art"><?= project_art($pr, []) ?></div>
        <?php endif; ?>

        <div class="detail__grid">
            <article class="detail__story">
                <span class="eyebrow">About the project</span>
                <?php if ($story !== ''): ?>
                    <?= paragraphs($story) ?>
                <?php else: ?>
                    <p><?= e($pr['summary']) ?></p>
                <?php endif; ?>

                <?php if (!empty($pr['metrics'])): ?>
                    <dl class="detail__metrics">
                        <?php foreach ($pr['metrics'] as $m): ?><div><dt><?= e($m[0]) ?></dt><dd><?= e($m[1]) ?></dd></div><?php endforeach; ?>
                    </dl>
                <?php endif; ?>
            </article>

            <aside class="detail__info">
                <dl>
                    <div><dt>Client</dt><dd><?= e($pr['client']) ?></dd></div>
                    <div><dt>Category</dt><dd><?= e($cat) ?></dd></div>
                    <?php if (($pr['role'] ?? '') !== ''): ?><div><dt>Role</dt><dd><?= e($pr['role']) ?></dd></div><?php endif; ?>
                    <?php if (($pr['year'] ?? '') !== ''): ?><div><dt>Year</dt><dd><?= e($pr['year']) ?></dd></div><?php endif; ?>
                    <?php if ($imgs): ?><div><dt>Images</dt><dd><?= count($imgs) ?></dd></div><?php endif; ?>
                </dl>
                <a class="btn btn--accent" href="<?= e($waText('Hi ' . $p['first'] . ', I saw your ' . $pr['title'] . ' project and would like something similar.')) ?>" target="_blank" rel="noopener">Start a similar project <?= arrow() ?></a>
                <?php if (!empty($pr['href'])): ?>
                    <a class="btn btn--dark" href="<?= e($pr['href']) ?>" target="_blank" rel="noopener">View on <?= e(parse_url($pr['href'], PHP_URL_HOST) ? preg_replace('/^www\./', '', (string)parse_url($pr['href'], PHP_URL_HOST)) : 'website') ?> <?= arrow() ?></a>
                <?php endif; ?>
            </aside>
        </div>

        <?php if (count($imgs) > 1): ?>
            <div class="detail__gallery">
                <?php foreach (array_slice($imgs, 1) as $k => $src): ?>
                    <button type="button" class="reveal <?= $k === 0 && (count($imgs) - 1) % 2 === 1 ? 'is-wide' : '' ?>" data-lightbox="p" data-src="<?= e(u($src)) ?>" aria-label="Open image <?= $k + 2 ?> of <?= count($imgs) ?>">
                        <img src="<?= e(u($src)) ?>" alt="<?= e($pr['title']) ?> — image <?= $k + 2 ?>" loading="lazy" decoding="async">
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <nav class="pnav" aria-label="More projects">
            <a href="<?= e(project_url($prev)) ?>" class="pnav__prev"><small>← Previous</small><b><?= e($prev['title']) ?></b></a>
            <a href="projects.php" class="pnav__all" aria-label="All projects"><span></span><span></span><span></span><span></span></a>
            <a href="<?= e(project_url($next)) ?>" class="pnav__next"><small>Next →</small><b><?= e($next['title']) ?></b></a>
        </nav>
    </div>
</section>

<?php if ($more): ?>
<section class="sec sec--soft">
    <div class="wrap">
        <header class="shead shead--row reveal">
            <div><span class="eyebrow">Keep exploring</span><h2>More <em>Projects</em></h2></div>
            <a class="pillbtn" href="projects.php"><span>View all</span><i><?= arrow() ?></i></a>
        </header>
        <div class="cards">
            <?php foreach ($more as $i => $m): ?>
                <a class="pcard reveal" href="<?= e(project_url($m)) ?>" style="--i:<?= $i ?>">
                    <span class="pcard__art"><?= project_art($m, project_images($m)) ?></span>
                    <span class="pcard__cat"><?= e($m['category']) ?></span>
                    <b><?= e($m['title']) ?></b>
                    <small><?= e($m['client']) ?></small>
                    <span class="pcard__more">View case study <?= arrow() ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/_parts/foot.php'; ?>
