<section class="work" id="work" data-theme="dark">
    <header class="section-head">
        <span class="eyebrow">(01) Selected work</span>
        <h2 class="display reveal-words"><?= words('Work that sells on the shelf') ?></h2>
        <p class="section-head__aside">Packaging, logos and brand identities, plus a few screens. Each one made for a real business with a real shelf to win.</p>
    </header>

    <div class="stack" data-stack>
        <?php foreach ($c['projects'] as $i => $pr): $n = str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT); ?>
            <article class="case case--<?= e($pr['mock']) ?>" id="case-<?= $i + 1 ?>" style="--i:<?= $i ?>" data-case>
                <div class="case__inner">
                    <header class="case__head">
                        <span class="case__no"><?= $n ?></span>
                        <div class="case__name">
                            <small>Client — <?= e($pr['client']) ?></small>
                            <h3><?= e($pr['title']) ?></h3>
                        </div>
                        <ul class="case__tags">
                            <?php foreach ($pr['tags'] ?? [] as $t): ?><li><?= e($t) ?></li><?php endforeach; ?>
                        </ul>
                        <?php if (!empty($pr['href'])): ?>
                            <a class="btn btn--ghost" href="<?= e($pr['href']) ?>" target="_blank" rel="noopener" data-magnetic>
                                <span>View project</span>
                                <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M5 11 11 5M6 5h5v5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        <?php else: ?>
                            <span class="case__cat"><?= e($pr['category'] ?? '') ?></span>
                        <?php endif; ?>
                    </header>

                    <div class="case__body">
                        <div class="case__info">
                            <p><?= e($pr['summary']) ?></p>
                            <dl class="case__meta">
                                <?php if (($pr['role'] ?? '') !== ''): ?><div><dt>Role</dt><dd><?= e($pr['role']) ?></dd></div><?php endif; ?>
                                <?php if (($pr['year'] ?? '') !== ''): ?><div><dt>Year</dt><dd><?= e($pr['year']) ?></dd></div><?php endif; ?>
                            </dl>
                            <div class="case__metrics">
                                <?php foreach ($pr['metrics'] ?? [] as $m): ?>
                                    <div><strong><?= e($m[0]) ?></strong><span><?= e($m[1]) ?></span></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php $imgs = project_images($pr); ?>
                        <?php if ($imgs): ?>
                            <div class="case__gallery case__gallery--<?= min(3, count($imgs)) ?>">
                                <?php foreach (array_slice($imgs, 0, 3) as $k => $src): ?>
                                    <button type="button" class="case__img" data-lightbox="<?= e($pr['slug']) ?>" data-src="<?= e($src) ?>" data-cursor="Zoom">
                                        <img src="<?= e($src) ?>" alt="<?= e($pr['title']) ?> — image <?= $k + 1 ?>" loading="lazy" decoding="async">
                                    </button>
                                <?php endforeach; ?>
                                <?php foreach (array_slice($imgs, 3) as $src): ?>
                                    <span hidden data-lightbox-extra="<?= e($pr['slug']) ?>" data-src="<?= e($src) ?>"></span>
                                <?php endforeach; ?>
                                <?php if (count($imgs) > 3): ?><span class="case__more">+<?= count($imgs) - 3 ?></span><?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="case__visual" data-tilt>
                                <?php include __DIR__ . '/mocks/' . basename($pr['mock'] ?? 'pack') . '.php'; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
