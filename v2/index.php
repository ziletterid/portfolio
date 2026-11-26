<?php
declare(strict_types=1);

/*
 * Versi 2 — gaya terang (oranye / hitam / putih).
 * Teks diambil dari ../data/content.php, gambar dari ../assets/img/ (sama dengan versi 1).
 */
require __DIR__ . '/../inc/helpers.php';
$c = site_content();

/** Path ke file di root proyek, dilihat dari /v2/ */
function u(string $path): string
{
    return '../' . ltrim($path, '/');
}

/** Ilustrasi CSS bawaan saat belum ada gambar. $kind: pack | brand | ui | print */
function ill(string $kind, array $vars = []): string
{
    $style = '';
    foreach ($vars as $k => $v) {
        $style .= '--' . $k . ':' . e((string)$v) . ';';
    }
    $leaf = '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M32 6c14 10 18 24 10 38-4 7-10 12-10 14 0-2-6-7-10-14C14 30 18 16 32 6Z" fill="currentColor"/><path d="M32 18v40M32 34l-8-7M32 42l9-8" stroke="var(--paper)" stroke-width="3.4" stroke-linecap="round" fill="none"/></svg>';
    return match ($kind) {
        'pack'  => '<div class="ill ill--pack" style="' . $style . '"><span class="ill-pouch"><i></i><b>' . e($vars['brand-name'] ?? 'Brand') . '</b></span><span class="ill-box"><i></i></span><span class="ill-shadow"></span></div>',
        'brand' => '<div class="ill ill--brand" style="' . $style . '"><span class="t1">' . $leaf . '<b>' . e($vars['brand-name'] ?? 'Brand') . '</b></span><span class="t2">' . $leaf . '</span><span class="t3">' . $leaf . '</span><span class="t4"><i></i><i></i><i></i><i></i></span></div>',
        'print' => '<div class="ill ill--print" style="' . $style . '"><span class="p1"><b>Fest</b><i></i></span><span class="p2"><b>Sale</b><i></i></span><span class="p3"></span></div>',
        default => '<div class="ill ill--ui" style="' . $style . '"><span class="ph"><i class="a"></i><i class="b"></i><i class="c"></i><i class="d"></i></span><span class="ph ph--2"><i class="a"></i><i class="b"></i><i class="c"></i></span></div>',
    };
}

/** Jenis ilustrasi dari data proyek */
function ill_for(array $pr): string
{
    $m = $pr['mock'] ?? '';
    if ($m === 'pack') {
        return ill('pack', ['c1' => $pr['pack']['c1'] ?? '#ff4d1f', 'c2' => $pr['pack']['c2'] ?? '#ffd25a', 'brand-name' => $pr['pack']['brand'] ?? $pr['client']]);
    }
    if ($m === 'brand') {
        return ill('brand', ['c1' => $pr['brand']['c1'] ?? '#1c1c1c', 'c2' => $pr['brand']['c2'] ?? '#ff4d1f', 'brand-name' => $pr['brand']['name'] ?? $pr['client']]);
    }
    return ill('ui');
}

$p       = $c['person'];
$v       = $c['v2'];
$full    = $p['first'] . ' ' . $p['last'];
$cutout  = profile_cutout();
$photo   = profile_photo();
$gallery = images_in('assets/img/gallery');
$stats   = $c['about']['stats'];
$wa      = !empty($p['whatsapp']) ? 'https://wa.me/' . preg_replace('/\D+/', '', $p['whatsapp']) : 'mailto:' . $p['email'];
$cats    = array_values(array_unique(array_map(fn($x) => $x['category'] ?? 'Other', $c['projects'])));

$serviceIll = ['brand', 'pack', 'print', 'print', 'ui'];
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($c['meta']['title']) ?></title>
    <meta name="description" content="<?= e($c['meta']['description']) ?>">
    <meta name="theme-color" content="#f3f1ee">
    <link rel="icon" href="<?= u('assets/img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@500;600;700;800&family=Geist:wght@300..700&family=Caveat:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/v2.css?v=<?= filemtime(__DIR__ . '/assets/v2.css') ?>">
    <script>document.documentElement.classList.replace('no-js', 'js');</script>
</head>
<body>
<a class="skip" href="#main">Skip to content</a>

<!-- ============ NAV ============ -->
<header class="nav" data-nav>
    <nav class="nav__bar" aria-label="Main">
        <ul class="nav__group">
            <li><a href="#top" class="is-active">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#services">Services</a></li>
        </ul>
        <a class="nav__logo" href="#top" aria-label="<?= e($full) ?>">
            <span class="nav__mark"><?= e($p['first'][0]) ?></span>
            <span><?= e($p['first']) ?></span>
        </a>
        <ul class="nav__group nav__group--end">
            <li><a href="#experience">Resume</a></li>
            <li><a href="#portfolio">Project</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
        <button class="nav__burger" type="button" aria-expanded="false" aria-controls="mmenu" data-burger><span></span><span></span><span class="sr">Menu</span></button>
    </nav>
    <div class="mmenu" id="mmenu" data-mmenu hidden>
        <?php foreach ([['top', 'Home'], ['about', 'About'], ['services', 'Services'], ['experience', 'Resume'], ['portfolio', 'Project'], ['contact', 'Contact']] as $i => [$id, $label]): ?>
            <a href="#<?= $id ?>" style="--i:<?= $i ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
    </div>
</header>

<main id="main">

<!-- ============ HERO ============ -->
<section class="hero" id="top">
    <div class="hero__head">
        <span class="hello" data-in>
            <?= e($v['hello']) ?>
            <svg class="hello__spark" viewBox="0 0 30 24" aria-hidden="true"><path d="M4 18 9 6M14 16l4-13M20 18l8-8" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
        </span>
        <h1 class="hero__title">
            <span class="line" data-in style="--d:1"><span>I’m <em><?= e($p['first']) ?>,</em></span></span>
            <span class="line" data-in style="--d:2"><span><?= e($p['role']) ?></span></span>
        </h1>
    </div>

    <div class="hero__stage">
        <aside class="hero__quote" data-in style="--d:4">
            <svg class="qmark" viewBox="0 0 40 30" aria-hidden="true"><path d="M0 30V17C0 7 5 1 15 0l1 5c-5 1-8 5-8 10h8v15Zm24 0V17c0-10 5-16 15-17l1 5c-5 1-8 5-8 10h8v15Z" fill="currentColor"/></svg>
            <p><?= e($v['quote']) ?></p>
            <div class="hero__num"><strong><span data-count-to="<?= (int)$stats[3]['value'] ?>">0</span><?= e($stats[3]['suffix']) ?></strong><span>Clients served</span></div>
        </aside>

        <div class="hero__visual">
            <div class="hero__circle" aria-hidden="true"></div>
            <svg class="hero__arrow" viewBox="0 0 90 60" aria-hidden="true"><path d="M6 8c2 24 22 40 64 40" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/><path d="M60 38l12 10-14 6" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <?php if ($cutout): ?>
                <img class="hero__person" src="<?= e(u($cutout)) ?>" alt="<?= e($full) ?>">
            <?php elseif ($photo): ?>
                <img class="hero__photo" src="<?= e(u($photo)) ?>" alt="<?= e($full) ?>">
            <?php else: ?>
                <div class="hero__products" aria-hidden="true">
                    <?= ill('pack', ['c1' => '#1c1c1c', 'c2' => '#ff4d1f', 'brand-name' => $p['first']]) ?>
                    <span class="hero__badge hero__badge--logo">
                        <svg viewBox="0 0 64 64"><path d="M32 6c14 10 18 24 10 38-4 7-10 12-10 14 0-2-6-7-10-14C14 30 18 16 32 6Z" fill="currentColor"/></svg>
                    </span>
                    <span class="hero__badge hero__badge--swatch"><i></i><i></i><i></i></span>
                    <span class="hero__badge hero__badge--type">Aa</span>
                </div>
            <?php endif; ?>
            <div class="hero__cta" data-in style="--d:5">
                <a class="btn btn--accent" href="#portfolio">Portfolio <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M5 11 11 5M6 5h5v5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                <a class="btn btn--plain" href="#contact">Hire Me</a>
            </div>
        </div>

        <aside class="hero__rating" data-in style="--d:4">
            <div class="stars" aria-label="5 out of 5 stars"><?php for ($s = 0; $s < 5; $s++): ?><svg viewBox="0 0 20 20" aria-hidden="true"><path d="m10 1.5 2.6 5.6 6 .7-4.5 4.1 1.2 6L10 15l-5.3 2.9 1.2-6L1.4 7.8l6-.7Z" fill="currentColor"/></svg><?php endfor; ?></div>
            <strong><?= (int)$stats[0]['value'] ?> Years</strong>
            <span>Experience</span>
        </aside>
    </div>
</section>

<!-- ============ SERVICES ============ -->
<section class="services" id="services">
    <div class="dark-card">
        <header class="sec-head sec-head--split">
            <h2 class="reveal">My <em>Services</em></h2>
            <p class="reveal">From the first sketch to print-ready files: everything a brand needs to look its best on the shelf and on the screen.</p>
        </header>
        <div class="rail" data-rail>
            <?php foreach ($c['services'] as $i => $s): ?>
                <article class="svc reveal <?= $i === 1 ? 'is-hot' : '' ?>" style="--d:<?= $i ?>">
                    <h3><?= e($s['title']) ?></h3>
                    <div class="svc__media">
                        <span class="svc__stack" aria-hidden="true"></span>
                        <?= ill($serviceIll[$i] ?? 'print', $serviceIll[$i] === 'pack' ? ['c1' => '#ff4d1f', 'c2' => '#ffd25a', 'brand-name' => 'Brand'] : ['c1' => '#1c1c1c', 'c2' => '#ff4d1f', 'brand-name' => 'Brand']) ?>
                        <p class="svc__desc"><?= e($s['desc']) ?></p>
                        <a class="notch-btn" href="#contact" aria-label="Order <?= e($s['title']) ?>"><svg viewBox="0 0 16 16"><path d="M4.5 11.5 11.5 4.5M6 4.5h5.5V10" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="rail-nav">
            <button type="button" class="round" data-rail-prev aria-label="Previous">←</button>
            <button type="button" class="round round--accent" data-rail-next aria-label="Next">→</button>
        </div>
    </div>
</section>

<!-- ============ EXPERIENCE ============ -->
<section class="experience" id="experience">
    <header class="sec-head sec-head--center"><h2 class="reveal">My Work <em>Experience</em></h2></header>
    <ol class="timeline" data-timeline>
        <span class="timeline__line" aria-hidden="true"><span data-timeline-fill></span></span>
        <?php foreach ($c['experience'] as $i => $x): ?>
            <li class="timeline__item reveal" style="--d:<?= $i ?>">
                <div class="timeline__where"><h3><?= e($x['place']) ?>, <?= e($x['where']) ?></h3><span><?= e($x['period']) ?></span></div>
                <span class="timeline__dot <?= $i % 2 ? 'is-dark' : '' ?>" aria-hidden="true"></span>
                <div class="timeline__what"><h3><?= e($x['role']) ?></h3><p><?= e($x['desc']) ?></p></div>
            </li>
        <?php endforeach; ?>
    </ol>
</section>

<!-- ============ WHY HIRE ME ============ -->
<section class="hire" id="about">
    <div class="hire__card">
        <div class="hire__visual">
            <div class="hire__circle"></div>
            <svg class="hire__dots" viewBox="0 0 120 80" aria-hidden="true"><?php for ($r = 0; $r < 5; $r++): for ($k = 0; $k < 8; $k++): ?><circle cx="<?= 8 + $k * 14 + ($r % 2) * 7 ?>" cy="<?= 8 + $r * 15 ?>" r="2.2" fill="currentColor"/><?php endfor; endfor; ?></svg>
            <?php if ($cutout): ?>
                <img class="hire__person" src="<?= e(u($cutout)) ?>" alt="">
            <?php elseif ($photo): ?>
                <img class="hire__photo" src="<?= e(u($photo)) ?>" alt="">
            <?php else: ?>
                <div class="hire__mono" aria-hidden="true"><?= e($p['first'][0] . $p['last'][0]) ?></div>
            <?php endif; ?>
        </div>
        <div class="hire__text">
            <h2 class="reveal"><?= str_replace('Hire Me', '<em>Hire Me</em>', e($v['hire_title'])) ?></h2>
            <p class="reveal"><?= e($v['hire_text']) ?></p>
            <ul class="hire__stats">
                <?php foreach (array_slice($stats, 1, 3) as $s): ?>
                    <li class="reveal"><strong><span data-count-to="<?= (int)$s['value'] ?>">0</span><?= e($s['suffix']) ?></strong><span><?= e($s['label']) ?></span></li>
                <?php endforeach; ?>
            </ul>
            <ul class="hire__tools reveal"><?php foreach ($c['about']['tools'] as $t): ?><li><?= e($t) ?></li><?php endforeach; ?></ul>
            <a class="btn btn--outline reveal" href="#contact">Hire Me</a>
        </div>
    </div>
</section>

<!-- ============ PORTFOLIO ============ -->
<section class="portfolio" id="portfolio">
    <header class="sec-head sec-head--split sec-head--end">
        <h2 class="reveal">Let’s Have a Look at<br>my <em>Portfolio</em></h2>
        <div class="filters reveal" role="tablist" aria-label="Filter projects">
            <button type="button" class="is-on" data-filter="all" role="tab" aria-selected="true">All</button>
            <?php foreach ($cats as $cat): ?>
                <button type="button" data-filter="<?= e($cat) ?>" role="tab" aria-selected="false"><?= e($cat) ?></button>
            <?php endforeach; ?>
        </div>
    </header>

    <div class="pf" data-pf>
        <div class="pf__track" data-pf-track>
            <?php foreach ($c['projects'] as $i => $pr): $imgs = project_images($pr); ?>
                <article class="pf__card" data-cat="<?= e($pr['category'] ?? 'Other') ?>">
                    <div class="pf__media">
                        <?php if ($imgs): ?>
                            <img src="<?= e(u($imgs[0])) ?>" alt="<?= e($pr['title']) ?>" loading="lazy">
                            <?php if (isset($imgs[1])): ?><img class="pf__media-2" src="<?= e(u($imgs[1])) ?>" alt="" loading="lazy"><?php endif; ?>
                        <?php else: ?>
                            <?= ill_for($pr) ?>
                        <?php endif; ?>
                        <span class="pf__year"><?= e($pr['year']) ?></span>
                    </div>
                    <ul class="pf__tags"><?php foreach ($pr['tags'] as $t): ?><li><?= e($t) ?></li><?php endforeach; ?></ul>
                    <h3><?= e($pr['title']) ?> <span>— <?= e($pr['client']) ?></span>
                        <?php if (!empty($pr['href'])): ?><a class="mini-arrow" href="<?= e($pr['href']) ?>" target="_blank" rel="noopener" aria-label="Open <?= e($pr['title']) ?>">↗</a><?php else: ?><span class="mini-arrow" aria-hidden="true">↗</span><?php endif; ?>
                    </h3>
                    <p><?= e($pr['summary']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
        <button type="button" class="round pf__prev" data-pf-prev aria-label="Previous project">←</button>
        <button type="button" class="round round--accent pf__next" data-pf-next aria-label="Next project">→</button>
    </div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<section class="voices">
    <div class="dark-card dark-card--voices">
        <header class="sec-head sec-head--center">
            <h2 class="reveal">Testimonials that<br>Speak to <em>My Results</em>
                <svg class="spark" viewBox="0 0 30 24" aria-hidden="true"><path d="M4 18 9 6M14 16l4-13M20 18l8-8" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
            </h2>
            <p class="reveal">What clients say after their designs hit the shelf.</p>
        </header>
        <div class="tst" data-tst>
            <div class="tst__track" data-tst-track>
                <?php foreach ($c['testimonials'] as $i => $t): $ini = implode('', array_map(fn($x) => mb_substr($x, 0, 1), array_slice(explode(' ', $t['name']), 0, 2))); ?>
                    <figure class="tst__card" data-tst-card>
                        <div class="tst__stars"><?php for ($s = 0; $s < 5; $s++): ?><svg viewBox="0 0 20 20" aria-hidden="true"><path d="m10 1.5 2.6 5.6 6 .7-4.5 4.1 1.2 6L10 15l-5.3 2.9 1.2-6L1.4 7.8l6-.7Z" fill="currentColor"/></svg><?php endfor; ?><span>5.0</span></div>
                        <blockquote><?= e($t['quote']) ?></blockquote>
                        <figcaption><span class="av av--<?= $i % 4 ?>"><?= e($ini) ?></span><span><b><?= e($t['name']) ?></b><?= e($t['role']) ?></span></figcaption>
                        <svg class="tst__q" viewBox="0 0 40 30" aria-hidden="true"><path d="M0 30V17C0 7 5 1 15 0l1 5c-5 1-8 5-8 10h8v15Zm24 0V17c0-10 5-16 15-17l1 5c-5 1-8 5-8 10h8v15Z" fill="currentColor"/></svg>
                    </figure>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="rail-nav rail-nav--center">
            <button type="button" class="round" data-tst-prev aria-label="Previous testimonial">←</button>
            <button type="button" class="round round--accent" data-tst-next aria-label="Next testimonial">→</button>
        </div>
    </div>
</section>

<!-- ============ CTA ============ -->
<section class="cta" id="contact">
    <h2 class="reveal"><?= e($v['cta_title']) ?><br><em><?= e($v['cta_accent']) ?></em></h2>
    <form class="cta__form reveal" data-wa-form data-wa="<?= e($wa) ?>">
        <label class="sr" for="cta-idea">Your project idea</label>
        <svg class="cta__icon" viewBox="0 0 20 20" aria-hidden="true"><path d="M3 5.5A2.5 2.5 0 0 1 5.5 3h9A2.5 2.5 0 0 1 17 5.5v6a2.5 2.5 0 0 1-2.5 2.5H9l-4 3v-3A2 2 0 0 1 3 12z" fill="currentColor"/></svg>
        <input id="cta-idea" name="idea" type="text" placeholder="Tell me about your brand or product…" autocomplete="off" maxlength="300">
        <button class="btn btn--accent" type="submit">Let’s talk</button>
    </form>
    <ul class="cta__checks reveal">
        <li><?= (int)$stats[3]['value'] . e($stats[3]['suffix']) ?> happy clients</li>
        <li><?= (int)$stats[2]['value'] . e($stats[2]['suffix']) ?> packaging projects</li>
        <li>Print-ready files</li>
    </ul>
    <p class="cta__alt reveal">or email <a href="mailto:<?= e($p['email']) ?>"><?= e($p['email']) ?></a></p>
</section>

<!-- ============ RIBBON ============ -->
<div class="ribbons" aria-hidden="true">
    <?php foreach (['ribbon--a', 'ribbon--b'] as $cls): ?>
        <div class="ribbon <?= $cls ?>"><div class="ribbon__track">
            <?php for ($r = 0; $r < 4; $r++): foreach ($v['ribbon'] as $w): ?><span><?= e($w) ?></span><i>✦</i><?php endforeach; endfor; ?>
        </div></div>
    <?php endforeach; ?>
</div>

<!-- ============ ARCHIVE ============ -->
<section class="archive" id="archive">
    <header class="sec-head sec-head--split sec-head--end">
        <h2 class="reveal">From my<br><em>design archive</em></h2>
        <p class="reveal"><?= e($c['archive']['intro']) ?></p>
    </header>
    <?php if ($gallery): ?>
        <ul class="archive__grid">
            <?php foreach ($gallery as $k => $src): $label = label_from_file($src); ?>
                <li class="archive__item reveal" style="--d:<?= $k % 3 ?>">
                    <button type="button" data-lightbox data-src="<?= e(u($src)) ?>" data-caption="<?= e($label) ?>">
                        <img src="<?= e(u($src)) ?>" alt="<?= e($label) ?>" loading="lazy">
                        <span class="notch-btn notch-btn--sm" aria-hidden="true"><svg viewBox="0 0 16 16"><path d="M4.5 11.5 11.5 4.5M6 4.5h5.5V10" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    </button>
                    <span class="archive__cap"><?= e($label) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="archive__empty reveal">
            <div class="archive__ghosts" aria-hidden="true"><span></span><span></span><span></span></div>
            <p><b>Galeri masih kosong.</b> Taruh gambar logo, label, poster, dll. di <code>assets/img/gallery/</code> lalu refresh. Pesan ini hilang setelah ada gambar.</p>
        </div>
    <?php endif; ?>
</section>

</main>

<!-- ============ FOOTER ============ -->
<footer class="footer">
    <div class="footer__top">
        <div>
            <a class="nav__logo nav__logo--footer" href="#top"><span class="nav__mark"><?= e($p['first'][0]) ?></span><span><?= e($full) ?></span></a>
            <p><?= e($p['role']) ?> based in <?= e($p['location']) ?>.</p>
        </div>
        <div class="footer__cols">
            <div><h3>Menu</h3><ul><li><a href="#services">Services</a></li><li><a href="#experience">Resume</a></li><li><a href="#portfolio">Project</a></li><li><a href="#archive">Archive</a></li></ul></div>
            <div><h3>Social</h3><ul><?php foreach ($c['social'] as $s): ?><li><a href="<?= e($s['href']) ?>" target="_blank" rel="noopener"><?= e($s['label']) ?></a></li><?php endforeach; ?></ul></div>
            <div><h3>Contact</h3><ul><li><a href="mailto:<?= e($p['email']) ?>"><?= e($p['email']) ?></a></li><li><a href="<?= e($wa) ?>" target="_blank" rel="noopener"><?= e($p['phone']) ?></a></li><li><?= e($p['city']) ?></li></ul></div>
        </div>
    </div>
    <p class="footer__word" aria-hidden="true"><?= e($p['first']) ?><em>.</em></p>
    <div class="footer__bar"><span>© <?= date('Y') ?> <?= e($full) ?></span><a href="#top">Back to top ↑</a></div>
</footer>

<a class="wa-float" href="<?= e($wa) ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.2-.4.7-1.4.1-.2 0-.3 0-.5l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.8 11.9 11.9 0 0 0 4.6 4c1.7.7 2.4.8 3.2.7.5-.1 1.5-.6 1.8-1.2.2-.6.2-1.1.1-1.2l-.4-.2Z"/></svg>
</a>

<div class="lightbox" data-lb hidden>
    <button class="lightbox__close round" type="button" data-lb-close aria-label="Close">×</button>
    <figure><img alt="" data-lb-img><figcaption data-lb-cap></figcaption></figure>
</div>

<script src="assets/v2.js?v=<?= filemtime(__DIR__ . '/assets/v2.js') ?>" defer></script>
</body>
</html>
