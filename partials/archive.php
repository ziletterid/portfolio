<?php $gallery = images_in('assets/img/gallery'); $ar = $c['archive']; ?>
<section class="archive" id="archive" data-theme="dark">
    <header class="section-head">
        <span class="eyebrow">(02) Archive</span>
        <h2 class="display reveal-words"><?= words($ar['title']) ?></h2>
        <p class="section-head__aside"><?= e($ar['intro']) ?></p>
    </header>

    <?php if ($gallery): ?>
        <ul class="archive__grid">
            <?php foreach ($gallery as $k => $src): $label = label_from_file($src); ?>
                <li class="archive__item reveal" style="--d:<?= ($k % 4) * 60 ?>ms">
                    <button type="button" data-lightbox="archive" data-src="<?= e($src) ?>" data-caption="<?= e($label) ?>" data-cursor="Zoom">
                        <img src="<?= e($src) ?>" alt="<?= e($label) ?>" loading="lazy" decoding="async">
                        <span class="archive__cap"><?= e($label) ?></span>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="archive__empty">
            <div class="archive__ghosts" aria-hidden="true"><?php for ($k = 0; $k < 8; $k++): ?><span style="--k:<?= $k ?>"></span><?php endfor; ?></div>
            <p><b>Galeri masih kosong.</b> Taruh gambar logo, label, poster, dll. di folder <code>assets/img/gallery/</code> lalu refresh — gambar otomatis tampil di sini. Pesan ini tidak muncul lagi setelah ada gambar.</p>
        </div>
    <?php endif; ?>
</section>

<div class="lightbox" data-lb hidden>
    <button class="lightbox__close" type="button" data-lb-close aria-label="Close">×</button>
    <button class="lightbox__nav lightbox__nav--prev" type="button" data-lb-prev aria-label="Previous">←</button>
    <figure><img alt="" data-lb-img><figcaption data-lb-cap></figcaption></figure>
    <button class="lightbox__nav lightbox__nav--next" type="button" data-lb-next aria-label="Next">→</button>
</div>
