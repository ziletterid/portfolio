<?php /* Footer /v3/ */ if (!isset($c)) { http_response_code(404); exit; } ?>
</main>

<!-- ============ FOOTER ============ -->
<footer class="ftr">
    <div class="wrap">
        <div class="ftr__top">
            <h2>Let’s <em>Connect</em> there</h2>
            <a class="pillbtn" href="<?= $L ?>contact"><span>Contact Me</span><i><?= arrow() ?></i></a>
        </div>
        <div class="ftr__grid">
            <div class="ftr__brand">
                <a class="logo" href="<?= $L ?>home"><span class="logo__mark"><?= e($initials[0]) ?></span><span><?= e($p['first']) ?><i>.</i></span></a>
                <p><?= e($c['hero']['intro']) ?></p>
                <div class="socials socials--accent">
                    <?php foreach ($c['social'] as $s): ?>
                        <a href="<?= e($s['href']) ?>" aria-label="<?= e($s['label']) ?>" <?= $s['href'] !== '#' ? 'target="_blank" rel="noopener"' : '' ?>><?= social_icon($s['label']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <nav aria-label="Footer">
                <h3>Navigation</h3>
                <?php foreach ($nav as [$id, $label]): ?><a href="<?= $L . $id ?>"><?= e($label) ?></a><?php endforeach; ?>
            </nav>
            <div>
                <h3>Contact</h3>
                <a href="mailto:<?= e($p['email']) ?>"><?= e($p['email']) ?></a>
                <span><?= e($p['phone']) ?></span>
                <span><?= e($p['location']) ?></span>
            </div>
            <form class="ftr__wa" data-wa="<?= e(!empty($p['whatsapp']) ? $wa : '') ?>" data-mail="<?= e($p['email']) ?>">
                <h3>Quick message</h3>
                <p>Type a short message, it opens WhatsApp.</p>
                <div><input name="q" maxlength="300" placeholder="Hi, I need a logo for…" aria-label="Quick message"><button type="submit" aria-label="Send"><?= arrow() ?></button></div>
            </form>
        </div>
        <div class="ftr__bot">
            <span>© <?= date('Y') ?> <em><?= e($full) ?></em>. All rights reserved.</span>
            <span><?= e($p['role']) ?> · <?= e($p['city']) ?></span>
        </div>
    </div>
</footer>

<a class="totop" href="#" aria-label="Back to top" data-totop>↑</a>

<div class="lb" data-lb hidden role="dialog" aria-modal="true" aria-label="Image viewer">
    <button class="lb__x" type="button" aria-label="Close" data-lb-close>✕</button>
    <button class="lb__nav lb__nav--prev" type="button" aria-label="Previous image" data-lb-prev>←</button>
    <figure><img alt="" data-lb-img><figcaption data-lb-cap></figcaption></figure>
    <button class="lb__nav lb__nav--next" type="button" aria-label="Next image" data-lb-next>→</button>
</div>

<script src="assets/v3.js?v=<?= filemtime(__DIR__ . '/../assets/v3.js') ?>" defer></script>
</body>
</html>
