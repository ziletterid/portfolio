<section class="services" id="services" data-theme="light">
    <div class="services__sky" aria-hidden="true"></div>
    <header class="section-head section-head--center">
        <span class="pill pill--outline">(04) Services</span>
        <h2 class="display reveal-words"><?= words('What I can do for your product') ?></h2>
    </header>

    <ol class="svc" data-svc>
        <?php foreach ($c['services'] as $i => $s): $id = 'svc-' . $i; ?>
            <li class="svc__item reveal" style="--d:<?= $i * 70 ?>ms">
                <button class="svc__row" type="button" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="<?= $id ?>">
                    <span class="svc__no">0<?= $i + 1 ?></span>
                    <span class="svc__title"><?= e($s['title']) ?></span>
                    <span class="svc__icon" aria-hidden="true"></span>
                </button>
                <div class="svc__panel" id="<?= $id ?>" role="region">
                    <div>
                        <p><?= e($s['desc']) ?></p>
                        <ul><?php foreach ($s['chips'] as $ch): ?><li><?= e($ch) ?></li><?php endforeach; ?></ul>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ol>
</section>
