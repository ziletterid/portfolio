<footer class="footer" data-theme="dark">
    <div class="footer__inner">
        <div class="footer__cols">
            <div class="footer__col footer__col--lead">
                <p>Let's make the next thing<br>people can't stop using.</p>
                <a class="btn btn--light" href="#contact" data-magnetic><span>Start a project</span></a>
            </div>
            <div class="footer__col">
                <h3>Social</h3>
                <ul><?php foreach ($c['social'] as $s): ?><li><a href="<?= e($s['href']) ?>" target="_blank" rel="noopener"><?= e($s['label']) ?></a></li><?php endforeach; ?></ul>
            </div>
            <div class="footer__col">
                <h3>Contact</h3>
                <ul>
                    <li><a href="mailto:<?= e($p['email']) ?>"><?= e($p['email']) ?></a></li>
                    <li><a href="tel:<?= e(preg_replace('/\s+/', '', $p['phone'])) ?>"><?= e($p['phone']) ?></a></li>
                    <li><?= e($p['city']) ?></li>
                </ul>
            </div>
            <div class="footer__col">
                <h3>Menu</h3>
                <ul><?php foreach ($c['nav'] as $item): ?><li><a href="#<?= e($item['id']) ?>"><?= e($item['label']) ?></a></li><?php endforeach; ?></ul>
            </div>
        </div>

        <p class="footer__word" aria-hidden="true"><?= e($p['first']) ?><span><?= e($p['last']) ?></span></p>

        <div class="footer__bar">
            <span>© <?= date('Y') ?> <?= e($full) ?></span>
            <span><span data-clock>--:--</span> in <?= e($p['city']) ?></span>
            <a href="#top" class="footer__top">Back to top ↑</a>
        </div>
    </div>
</footer>
