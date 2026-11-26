<?php $a = $c['about']; ?>
<section class="about" id="about" data-theme="dark">
    <div class="about__glow" aria-hidden="true"></div>
    <span class="eyebrow">(03) About</span>

    <p class="about__statement" data-fill>
        <?php foreach (preg_split('/\s+/', $a['statement']) as $w): ?><span><?= e($w) ?></span> <?php endforeach; ?>
    </p>

    <div class="about__grid">
        <aside class="idcard" data-tilt>
            <div class="idcard__top"><?php include __DIR__ . '/logo.php'; ?><span>ID · 0824</span></div>
            <?php $photo = profile_photo(); ?>
            <div class="idcard__avatar<?= $photo ? ' has-photo' : '' ?>">
                <?php if ($photo): ?><img src="<?= e($photo) ?>" alt="<?= e($full) ?>"><?php else: ?><span><?= e($p['first'][0] . $p['last'][0]) ?></span><?php endif; ?>
            </div>
            <div class="idcard__name"><b><?= e($full) ?></b><span><?= e($p['role']) ?></span></div>
            <dl class="idcard__meta">
                <div><dt>Based in</dt><dd><?= e($p['city']) ?></dd></div>
                <div><dt>Status</dt><dd class="ok"><span class="dot"></span>Available</dd></div>
            </dl>
            <div class="idcard__code" aria-hidden="true"><?php for ($k = 0; $k < 34; $k++): ?><i style="--w:<?= [1,2,1,3,1,2][$k % 6] ?>"></i><?php endfor; ?></div>
        </aside>

        <div class="about__text">
            <p class="reveal"><?= e($a['body']) ?></p>
            <ul class="stats">
                <?php foreach ($a['stats'] as $s): ?>
                    <li class="reveal">
                        <strong><span data-count-to="<?= (int)$s['value'] ?>">0</span><?= e($s['suffix']) ?></strong>
                        <span><?= e($s['label']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="tools reveal">
                <span class="tools__label">Daily toolkit</span>
                <ul><?php foreach ($a['tools'] as $t): ?><li><?= e($t) ?></li><?php endforeach; ?></ul>
            </div>
        </div>
    </div>
</section>
