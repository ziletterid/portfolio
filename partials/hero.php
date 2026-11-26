<?php $h = $c['hero']; $f = $c['frame']; ?>
<section class="frame" id="top" data-frame>
    <div class="frame__sticky">

        <!-- Poster: muncul di belakang saat hero mengecil jadi layar monitor -->
        <div class="frame__poster" aria-hidden="true">
            <div class="frame__labels"><span><?= e($f['left']) ?></span><span><?= e($f['right']) ?></span></div>
            <span class="pill pill--outline"><?= e($f['pill']) ?></span>
            <p class="frame__title"><?= $f['title'] ?></p>
        </div>
        <div class="frame__stand" aria-hidden="true"><span class="frame__neck"></span><span class="frame__base"></span></div>
        <div class="frame__desk" aria-hidden="true"></div>

        <div class="hero" data-theme="dark" data-hero>
            <canvas class="hero__gl" data-gl aria-hidden="true"></canvas>
            <div class="hero__fallback" aria-hidden="true"><span class="arc"></span><span class="orb"></span></div>
            <div class="hero__vignette" aria-hidden="true"></div>

            <div class="hero__grid">
                <h1 class="hero__title">
                    <span class="sr"><?= e($full) ?>, <?= e($p['role']) ?>. </span>
                    <?php foreach ($h['lines'] as $i => $line): ?>
                        <span class="hero__line" style="--li:<?= $i ?>"><span><?= e($line) ?></span></span>
                    <?php endforeach; ?>
                    <span class="hero__line hero__line--rotate" style="--li:<?= count($h['lines']) ?>">
                        <span class="rotator" data-rotator data-words='<?= e(json_encode($h['rotate'])) ?>'><?= e($h['rotate'][0]) ?></span>
                    </span>
                </h1>

                <div class="hero__side">
                    <p class="hero__intro"><?= e($h['intro']) ?></p>
                    <div class="hero__status"><span class="dot"></span><?= e($p['status']) ?></div>
                </div>

                <div class="hero__thumbs" aria-label="Recent projects">
                    <?php foreach (array_slice($c['projects'], 0, 3) as $i => $pr): $img = project_images($pr)[0] ?? null; ?>
                        <a href="#case-<?= $i + 1 ?>" class="thumb thumb--<?= e($pr['mock'] ?? 'pack') ?>" aria-label="<?= e($pr['title']) ?>"
                           style="<?= isset($pr['pack']) ? '--c1:' . e($pr['pack']['c1']) . ';--c2:' . e($pr['pack']['c2']) : (isset($pr['brand']) ? '--c1:' . e($pr['brand']['c1']) . ';--c2:' . e($pr['brand']['c2']) : '') ?>">
                            <?php if ($img): ?><img src="<?= e($img) ?>" alt="" loading="eager"><?php else: ?><span class="thumb__shape"></span><?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                    <span class="hero__thumbs-label">Recent work<br><b><?= str_pad((string)count($c['projects']), 2, '0', STR_PAD_LEFT) ?> cases</b></span>
                </div>

                <?php $lead = $c['projects'][0]; $leadImg = project_images($lead)[0] ?? null; ?>
                <a class="hero__card" href="#case-1" data-magnetic data-cursor="Open">
                    <span class="hero__card-media">
                        <?php if ($leadImg): ?><img src="<?= e($leadImg) ?>" alt=""><?php else: ?><span class="mini-pack" style="--c1:<?= e($lead['pack']['c1'] ?? '#3b22f0') ?>;--c2:<?= e($lead['pack']['c2'] ?? '#d6d1ff') ?>"></span><?php endif; ?>
                    </span>
                    <span class="hero__card-body">
                        <small>Latest case · <?= e($lead['category'] ?? '') ?></small>
                        <strong><?= e($lead['title']) ?></strong>
                        <span><?= e($lead['year']) ?> · <?= e($lead['client']) ?> <i aria-hidden="true">→</i></span>
                    </span>
                </a>

                <div class="hero__scroll" aria-hidden="true"><span></span>Scroll to explore</div>
            </div>
        </div>
    </div>
</section>
