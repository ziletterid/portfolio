<section class="voices" data-theme="dark" aria-labelledby="voices-title">
    <header class="section-head section-head--center">
        <span class="eyebrow">(06) Kind words</span>
        <h2 class="display reveal-words" id="voices-title"><?= words('What clients are saying') ?></h2>
    </header>
    <?php $rows = [array_slice($c['testimonials'], 0, 3), array_slice($c['testimonials'], 3)]; ?>
    <?php foreach ($rows as $r => $row): ?>
        <div class="marquee marquee--voices <?= $r ? 'marquee--reverse' : '' ?>" data-marquee>
            <div class="marquee__track">
                <?php for ($dup = 0; $dup < 2; $dup++): ?>
                    <ul class="marquee__group" <?= $dup ? 'aria-hidden="true"' : '' ?>>
                        <?php foreach ($row as $k => $t): $ini = implode('', array_map(fn($x) => $x[0], explode(' ', $t['name']))); ?>
                            <li class="quote">
                                <blockquote>“<?= e($t['quote']) ?>”</blockquote>
                                <footer><span class="quote__av quote__av--<?= ($r * 3 + $k) % 6 ?>"><?= e($ini) ?></span><span><b><?= e($t['name']) ?></b><?= e($t['role']) ?></span></footer>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endfor; ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>
