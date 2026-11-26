<header class="nav" data-nav>
    <a class="nav__logo" href="#top" aria-label="<?= e($full) ?>, back to top">
        <?php include __DIR__ . '/logo.php'; ?>
        <span><?= e(strtoupper($p['first'])) ?></span>
    </a>

    <nav class="nav__menu" aria-label="Main">
        <span class="nav__indicator" aria-hidden="true"></span>
        <?php foreach ($c['nav'] as $item): ?>
            <a href="#<?= e($item['id']) ?>" data-link="<?= e($item['id']) ?>"><?= e($item['label']) ?></a>
        <?php endforeach; ?>
    </nav>

    <div class="nav__end">
        <span class="nav__clock"><span data-clock>--:--</span> <?= e($p['city']) ?></span>
        <a class="btn btn--solid nav__cta" href="#contact" data-magnetic>
            <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M3 5.5A2.5 2.5 0 0 1 5.5 3h9A2.5 2.5 0 0 1 17 5.5v6a2.5 2.5 0 0 1-2.5 2.5H9l-4 3v-3h0A2 2 0 0 1 3 12z" fill="currentColor"/></svg>
            <span>Let's talk</span>
        </a>
        <button class="nav__burger" type="button" aria-expanded="false" aria-controls="mobile-menu" data-burger>
            <span></span><span></span><span class="sr">Menu</span>
        </button>
    </div>
</header>

<div class="mmenu" id="mobile-menu" data-mmenu hidden>
    <nav aria-label="Mobile">
        <?php foreach ($c['nav'] as $i => $item): ?>
            <a href="#<?= e($item['id']) ?>" style="--i:<?= $i ?>"><small>0<?= $i + 1 ?></small><?= e($item['label']) ?></a>
        <?php endforeach; ?>
    </nav>
    <a class="mmenu__mail" href="mailto:<?= e($p['email']) ?>"><?= e($p['email']) ?></a>
</div>
