<?php
declare(strict_types=1);

$isHome = true;
require __DIR__ . '/_parts/bootstrap.php';
require __DIR__ . '/_parts/head.php';
?>
<main id="main">

<!-- ============ HERO ============ -->
<section class="hero" id="home">
    <div class="hero__grid" aria-hidden="true"></div>
    <div class="wrap hero__in">
        <div class="hero__head">
            <span class="hello reveal"><?= e($v['hello']) ?><?= spark('hello__spark') ?></span>
            <h1 class="hero__title reveal">I’m <em><?= e($full) ?></em><span class="hero__dot">.</span></h1>
            <p class="hero__sub reveal"><?= e($v['tagline']) ?></p>
            <a class="badge-round" href="#portfolio" aria-label="See my work">
                <svg viewBox="0 0 120 120" aria-hidden="true"><defs><path id="circ" d="M60 60m-44 0a44 44 0 1 1 88 0a44 44 0 1 1-88 0"/></defs><text><textPath href="#circ">· <?= e(strtoupper($p['role'])) ?> · SEE MY WORK </textPath></text></svg>
                <span><?= arrow() ?></span>
            </a>
        </div>

        <div class="hero__stage">
            <aside class="hero__left reveal">
                <svg class="quote-mark" viewBox="0 0 32 24" aria-hidden="true"><path d="M0 24V14C0 6 4 1.5 12 0l1.5 3.4C8.8 5 6.8 8 6.6 11H13v13Zm19 0V14c0-8 4-12.5 12-14l1.5 3.4c-4.7 1.6-6.7 4.6-6.9 7.6H32v13Z" fill="currentColor"/></svg>
                <p><?= e($v['quote']) ?></p>
                <div class="rating">
                    <b><?= e($v['rating']) ?></b>
                    <span class="stars" aria-label="<?= e($v['rating']) ?> out of 5"><?= str_repeat('<i>★</i>', 5) ?></span>
                </div>
                <small><?= e(($stats[1]['value'] ?? 50) . ($stats[1]['suffix'] ?? '+') . ' ' . ($stats[1]['label'] ?? 'brands designed')) ?></small>
            </aside>

            <div class="hero__photo" data-duo>
                <span class="hero__arc" aria-hidden="true"></span>
                <span class="selframe" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><span class="selframe__tag">Graphic designer</span><svg class="selframe__cursor" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M3 2l17 8.2-7.3 1.9L9.6 20z"/></svg></span>
                <?php if ($cutout): ?>
                    <img class="hero__img hero__img--cut" src="<?= e(u($cutout)) ?>" alt="<?= e($full) ?>" fetchpriority="high">
                    <?php if ($cutoutC): ?><img class="hero__img hero__img--cut hero__img--color" src="<?= e(u($cutoutC)) ?>" alt="" aria-hidden="true" loading="lazy"><?php endif; ?>
                <?php elseif ($photo): ?>
                    <img class="hero__img hero__img--round" src="<?= e(u($photo)) ?>" alt="<?= e($full) ?>" fetchpriority="high">
                <?php else: ?>
                    <div class="hero__ph" aria-hidden="true"><span class="art-pouch"><i></i><b><?= e($p['first']) ?></b><small>Design studio</small></span><span class="hero__ph-logo"><?= e($initials) ?></span></div>
                <?php endif; ?>
                <div class="hero__btns">
                    <a class="btn btn--accent" href="#portfolio">Portfolio <?= arrow() ?></a>
                    <a class="btn btn--plain" href="<?= e($waText('Hi ' . $p['first'] . ', I would like to hire you for a project.')) ?>" target="_blank" rel="noopener">Hire Me</a>
                </div>
            </div>

            <aside class="hero__right reveal">
                <div class="exp"><b><span data-count-to="<?= (int)$years['value'] ?>">0</span><?= e($years['suffix']) ?></b><small><?= e($years['label']) ?></small></div>
                <ul class="floatchips">
                    <?php foreach (array_slice($ribbon, 0, 5) as $i => $r): ?><li class="<?= $i % 2 ? 'is-dark' : '' ?>" style="--d:<?= $i ?>"><?= e($r) ?></li><?php endforeach; ?>
                </ul>
                <div class="hero__social">
                    <?php foreach ($c['social'] as $s): ?>
                        <a href="<?= e($s['href']) ?>" aria-label="<?= e($s['label']) ?>" <?= $s['href'] !== '#' ? 'target="_blank" rel="noopener"' : '' ?>><?= social_icon($s['label']) ?></a>
                    <?php endforeach; ?>
                </div>
            </aside>
        </div>
    </div>
</section>

<?= $ribbonHtml() ?>

<!-- ============ PORTFOLIO (per kategori) ============ -->
<section class="sec" id="portfolio">
    <div class="wrap">
        <header class="shead reveal">
            <span class="eyebrow">My Portfolio</span>
            <h2>Let’s Have a Look at <em>My Portfolio</em><?= spark('shead__spark') ?></h2>
            <p>Pick a category to see the work behind it. Every project opens into its own page with the full story and images.</p>
        </header>
        <ol class="acc" data-acc>
            <?php foreach ($c['services'] as $i => $s): ?>
                <li class="acc__item reveal <?= $i === 1 ? 'is-open' : '' ?>">
                    <button class="acc__btn" type="button" aria-expanded="<?= $i === 1 ? 'true' : 'false' ?>">
                        <span class="acc__no"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?>.</span>
                        <span class="acc__title"><?= e($s['title']) ?></span>
                        <span class="acc__ico"><?= arrow() ?></span>
                    </button>
                    <?php $sc = service_category($s, $cats); $sp = projects_in($c['projects'], $sc); $n = count($sp); ?>
                    <div class="acc__panel">
                        <div class="acc__body">
                            <div class="acc__text">
                                <ul class="tags"><?php foreach ($s['chips'] ?? [] as $ch): ?><li><?= e($ch) ?></li><?php endforeach; ?></ul>
                                <p><?= e($s['desc']) ?></p>
                                <div class="acc__actions">
                                    <?php if ($n): ?>
                                        <a class="btn btn--accent" href="projects.php?cat=<?= rawurlencode($sc) ?>">See <?= $n ?> <?= e($sc) ?> project<?= $n > 1 ? 's' : '' ?> <?= arrow() ?></a>
                                    <?php endif; ?>
                                    <a class="link" href="<?= e($waText('Hi ' . $p['first'] . ', I am interested in ' . $s['title'] . '.')) ?>" target="_blank" rel="noopener">Order this design <?= arrow() ?></a>
                                </div>
                            </div>
                            <?php if ($n): ?>
                                <a class="sprev sprev--<?= min(3, $n) ?>" href="projects.php?cat=<?= rawurlencode($sc) ?>" aria-label="Open all <?= e($sc) ?> projects">
                                    <?php foreach (array_slice($sp, 0, 3) as $x): ?><span class="sprev__item"><?= project_art($x, project_images($x)) ?></span><?php endforeach; ?>
                                    <span class="sprev__badge"><?= $n ?> project<?= $n > 1 ? 's' : '' ?> <?= arrow() ?></span>
                                </a>
                            <?php else: ?>
                                <div class="sprev sprev--empty"><?= service_art($i) ?><span class="sprev__badge sprev__badge--soon">New work coming soon</span></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
        <div class="center reveal"><a class="pillbtn" href="projects.php"><span>View all <?= count($c['projects']) ?> projects</span><i><?= arrow() ?></i></a></div>
    </div>
</section>

<!-- ============ ABOUT ============ -->
<section class="sec sec--soft" id="about">
    <div class="wrap about">
        <div class="about__media reveal" data-duo>
            <span class="about__ring" aria-hidden="true"></span>
            <span class="selframe" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><span class="selframe__tag"><?= e($p['first']) ?>.png</span></span>
            <?php $aboutImg = $cutout ?: $photo; $aboutC = $cutout ? $cutoutC : $photoC; ?>
            <?php if ($aboutImg): ?>
                <img class="<?= $cutout ? 'about__cut' : '' ?>" src="<?= e(u($aboutImg)) ?>" alt="<?= e($full) ?>" loading="lazy">
                <?php if ($aboutC): ?><img class="about__color <?= $cutout ? 'about__cut' : '' ?>" src="<?= e(u($aboutC)) ?>" alt="" aria-hidden="true" loading="lazy"><?php endif; ?>
                <span class="about__grain" aria-hidden="true"></span>
            <?php else: ?>
                <div class="about__ph" aria-hidden="true"><b><?= e($initials) ?></b></div>
            <?php endif; ?>
            <div class="about__exp"><b><span data-count-to="<?= (int)($stats[1]['value'] ?? 50) ?>">0</span><?= e($stats[1]['suffix'] ?? '+') ?></b><small><?= e($stats[1]['label'] ?? 'Projects') ?></small></div>
            <div class="about__name"><span class="dot"></span><?= e($p['status']) ?></div>
        </div>
        <div class="about__text">
            <span class="eyebrow reveal">About Me</span>
            <h2 class="reveal">Who is <em><?= e($full) ?></em>?</h2>
            <p class="lead reveal"><?= e($c['about']['statement']) ?></p>
            <p class="reveal"><?= e($c['about']['body']) ?></p>
            <dl class="kpis reveal">
                <?php foreach (array_slice($stats, 0, 3) as $st): ?>
                    <div><dt><span data-count-to="<?= (int)$st['value'] ?>">0</span><?= e($st['suffix']) ?></dt><dd><?= e($st['label']) ?></dd></div>
                <?php endforeach; ?>
            </dl>
            <div class="about__foot reveal">
                <span class="sign"><?= e($full) ?></span>
                <a class="contact-chip" href="mailto:<?= e($p['email']) ?>"><i><?= social_icon('mail') ?></i><span><small>Email</small><?= e($p['email']) ?></span></a>
                <?php if (!empty($p['whatsapp'])): ?>
                    <a class="contact-chip" href="<?= e($wa) ?>" target="_blank" rel="noopener"><i><?= social_icon('whatsapp') ?></i><span><small>WhatsApp</small><?= e($p['phone']) ?></span></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ============ TOOLS ============ -->
<section class="sec" id="tools">
    <div class="wrap">
        <header class="shead reveal">
            <span class="eyebrow">My Favorite Tools</span>
            <h2>Design Tools That <em>Power My Work</em></h2>
        </header>
        <ul class="tools">
            <?php foreach ($c['about']['tools'] as $i => $t): [$ab, $fg, $bg] = tool_badge($t); ?>
                <li class="tool reveal" style="--i:<?= $i ?>">
                    <span class="tool__ico" style="--fg:<?= $fg ?>;--bg:<?= $bg ?>"><?= e($ab) ?></span>
                    <b><?= e($t) ?></b>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<?= $ribbonHtml() ?>

<!-- ============ EDUCATION & EXPERIENCE ============ -->
<section class="sec" id="resume">
    <div class="wrap">
        <header class="shead reveal">
            <span class="eyebrow">Education &amp; Work</span>
            <h2>My <em>Education</em> &amp; <em>Work Experience</em></h2>
        </header>
        <div class="resume">
            <article class="rcard reveal">
                <h3><span class="rcard__ico"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 4 10 5-10 5L2 9Z M6 11.3V16c0 1.2 2.7 3 6 3s6-1.8 6-3v-4.7" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></span>Education</h3>
                <ul>
                    <?php foreach ($c['education'] as $i => $ed): ?>
                        <li><div><b><?= e($ed['place']) ?></b><small><?= e($ed['title']) ?></small></div><span class="period <?= $i === 0 ? 'is-on' : '' ?>"><?= e($ed['period']) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </article>
            <article class="rcard reveal">
                <h3><span class="rcard__ico"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7M3 12.5h18" fill="none" stroke="currentColor" stroke-width="1.7"/></svg></span>Work Experience</h3>
                <ul>
                    <?php foreach ($c['experience'] as $i => $ex): ?>
                        <li><div><b><?= e($ex['place']) ?></b><small><?= e($ex['role']) ?> · <?= e($ex['where']) ?></small></div><span class="period <?= $i === 0 ? 'is-on' : '' ?>"><?= e($ex['period']) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </article>
        </div>
    </div>
</section>

<?= $ribbonHtml() ?>

<!-- ============ CONTACT ============ -->
<section class="sec sec--soft" id="contact">
    <div class="wrap">
        <header class="shead reveal">
            <span class="eyebrow">Contact Me</span>
            <h2>Let’s Talk for <em>Your Next Projects</em><?= spark('shead__spark') ?></h2>
        </header>
        <div class="contact">
            <form class="cform reveal" action="../api/contact.php" method="post" novalidate data-form data-wa="<?= e($wa) ?>" data-w3f="<?= e(trim((string)($v['web3forms_key'] ?? ''))) ?>" data-to="<?= e($p['first']) ?>">
                <div class="cform__grid">
                    <label>Your Name *<input name="name" required maxlength="120" placeholder="Ex. Budi Santoso" autocomplete="name"></label>
                    <label>Email *<input name="email" type="email" required maxlength="160" placeholder="example@gmail.com" autocomplete="email"></label>
                    <label>Phone / WhatsApp<input name="phone" type="tel" maxlength="30" placeholder="0812 …" autocomplete="tel"></label>
                    <label>I’m Interested in *
                        <select name="topics[]" required>
                            <option value="">Select</option>
                            <?php foreach ($c['services'] as $s): ?><option><?= e($s['title']) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <label>Budget Range
                        <select name="budget">
                            <option value="">Select range</option>
                            <option>&lt; Rp 1M</option><option>Rp 1M – 3M</option><option>Rp 3M – 7M</option><option>&gt; Rp 7M</option><option>Not sure yet</option>
                        </select>
                    </label>
                    <label>Deadline
                        <select name="deadline">
                            <option value="">Select</option>
                            <option>As soon as possible</option><option>Within 2 weeks</option><option>Within a month</option><option>Flexible</option>
                        </select>
                    </label>
                    <label class="full">Your Message *<textarea name="message" rows="5" required maxlength="4000" placeholder="Tell me about your product and what you need…"></textarea></label>
                    <label class="hp" aria-hidden="true">Website<input name="website" tabindex="-1" autocomplete="off"></label>
                </div>
                <div class="cform__end">
                    <button class="btn btn--accent" type="submit">Send Message <?= arrow() ?></button>
                    <p class="cform__msg" role="status" aria-live="polite" data-form-msg></p>
                </div>
            </form>
            <aside class="cinfo reveal">
                <div class="cinfo__top">
                    <div><h3>Address</h3><p><?= e($p['location']) ?></p></div>
                    <div><h3>Contact</h3><p>Phone : <?= e($p['phone']) ?><br>Email : <a href="mailto:<?= e($p['email']) ?>"><?= e($p['email']) ?></a></p></div>
                    <div><h3>Time</h3><p><?php foreach ($v['hours'] as $h): ?><span class="hrs"><span><?= e($h[0]) ?></span><span><?= e($h[1]) ?></span></span><?php endforeach; ?></p></div>
                </div>
                <div class="cinfo__bot">
                    <h3>Stay Connected</h3>
                    <div class="socials">
                        <?php foreach ($c['social'] as $s): ?>
                            <a href="<?= e($s['href']) ?>" aria-label="<?= e($s['label']) ?>" <?= $s['href'] !== '#' ? 'target="_blank" rel="noopener"' : '' ?>><?= social_icon($s['label']) ?></a>
                        <?php endforeach; ?>
                        <?php if (!empty($p['whatsapp'])): ?><a href="<?= e($wa) ?>" aria-label="WhatsApp" target="_blank" rel="noopener"><?= social_icon('whatsapp') ?></a><?php endif; ?>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<?php if ($c['testimonials']): ?>
<section class="sec" id="testimonials">
    <div class="wrap">
        <header class="shead shead--row reveal">
            <div>
                <span class="eyebrow">Client Testimonials</span>
                <h2>What My <em>Clients Are Saying</em></h2>
            </div>
            <div class="arrows">
                <button type="button" aria-label="Previous" data-prev>←</button>
                <button type="button" aria-label="Next" data-next>→</button>
            </div>
        </header>
        <div class="tests" data-track>
            <?php foreach ($c['testimonials'] as $t): $ini = mb_strtoupper(mb_substr(preg_replace('/^(Pak|Bu|Mas|Mbak)\s+/i', '', $t['name']), 0, 1)); ?>
                <figure class="test">
                    <header>
                        <span class="test__av"><?= e($ini) ?></span>
                        <div><b><?= e($t['name']) ?></b><small><?= e($t['role']) ?></small></div>
                        <svg class="quote-mark" viewBox="0 0 32 24" aria-hidden="true"><path d="M0 24V14C0 6 4 1.5 12 0l1.5 3.4C8.8 5 6.8 8 6.6 11H13v13Zm19 0V14c0-8 4-12.5 12-14l1.5 3.4c-4.7 1.6-6.7 4.6-6.9 7.6H32v13Z" fill="currentColor"/></svg>
                    </header>
                    <span class="stars" aria-label="5 out of 5"><?= str_repeat('<i>★</i>', 5) ?></span>
                    <blockquote><?= e($t['quote']) ?></blockquote>
                </figure>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ FAQ ============ -->
<section class="sec sec--soft" id="faq">
    <div class="wrap">
        <header class="shead reveal">
            <span class="eyebrow">FAQs</span>
            <h2>Questions? <em>Look here</em><?= spark('shead__spark') ?></h2>
        </header>
        <div class="faq">
            <div class="faq__list" data-faq>
                <?php foreach ($c['faq'] as $i => $f): ?>
                    <details class="reveal" <?= $i === 0 ? 'open' : '' ?>>
                        <summary><?= e($f['q']) ?><i aria-hidden="true"></i></summary>
                        <p><?= e($f['a']) ?></p>
                    </details>
                <?php endforeach; ?>
            </div>
            <aside class="faq__card reveal">
                <span class="faq__av"><?php if ($photo): ?><img src="<?= e(u($photo)) ?>" alt="" loading="lazy"><?php else: ?><?= e($initials) ?><?php endif; ?></span>
                <h3>You have different questions?</h3>
                <p>Send me a message and I’ll get back to you, usually within one working day.</p>
                <a class="btn btn--accent" href="<?= e($waText('Hi ' . $p['first'] . ', I have a question.')) ?>" target="_blank" rel="noopener">Ask on WhatsApp <?= arrow() ?></a>
                <div class="faq__help"><i><?= social_icon('mail') ?></i><span><small>Prefer email?</small><a href="mailto:<?= e($p['email']) ?>"><?= e($p['email']) ?></a></span></div>
            </aside>
        </div>
    </div>
</section>

<?= $ribbonHtml() ?>

<?php require __DIR__ . '/_parts/foot.php'; ?>
