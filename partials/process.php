<section class="process" id="process" data-theme="dark" data-process>
    <div class="process__sticky">
        <div class="process__track" data-track>
            <header class="process__intro">
                <span class="eyebrow">(05) Process</span>
                <h2 class="display">From brief<br>to shipped<br><em>in weeks.</em></h2>
                <p>A clear rhythm, weekly demos and no black box. You always know what is next.</p>
            </header>
            <?php foreach ($c['process'] as $i => $st): ?>
                <article class="step" style="--i:<?= $i ?>">
                    <div class="step__top"><span class="step__no">0<?= $i + 1 ?></span><span class="step__time"><?= e($st['time']) ?></span></div>
                    <div class="step__art step__art--<?= $i ?>" aria-hidden="true"><span></span><span></span><span></span></div>
                    <h3><?= e($st['title']) ?></h3>
                    <p><?= e($st['desc']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="process__progress" aria-hidden="true"><span data-progress></span></div>
    </div>
</section>
