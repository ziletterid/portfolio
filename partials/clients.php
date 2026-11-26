<section class="clients" data-theme="dark" aria-label="Clients">
    <p class="clients__label"><span>Trusted by teams at</span></p>
    <div class="marquee" data-marquee>
        <div class="marquee__track">
            <?php for ($r = 0; $r < 2; $r++): ?>
                <ul class="marquee__group" <?= $r ? 'aria-hidden="true"' : '' ?>>
                    <?php foreach ($c['clients'] as $i => $name): ?>
                        <li class="client client--<?= $i % 5 ?>"><span class="client__glyph" aria-hidden="true"></span><?= e($name) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endfor; ?>
        </div>
    </div>
</section>
