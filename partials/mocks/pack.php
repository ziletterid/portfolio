<?php $pk = $pr['pack'] ?? ['brand' => $pr['client'], 'product' => $pr['title'], 'size' => '', 'c1' => '#3b22f0', 'c2' => '#d6d1ff', 'c3' => '#f4f3fb']; ?>
<div class="mock mock-pack" style="--c1:<?= e($pk['c1']) ?>;--c2:<?= e($pk['c2']) ?>;--c3:<?= e($pk['c3']) ?>">
    <div class="mock-pack__floor"></div>
    <div class="pouch">
        <span class="pouch__seal"></span>
        <span class="pouch__notch"></span>
        <div class="pouch__label">
            <svg class="pouch__mark" viewBox="0 0 48 48" aria-hidden="true"><path d="M6 38 L18 18 L26 28 L32 20 L42 38Z" fill="currentColor"/><circle cx="34" cy="11" r="4" fill="currentColor"/></svg>
            <b><?= e($pk['brand']) ?></b>
            <small><?= e($pk['product']) ?></small>
        </div>
        <span class="pouch__window"></span>
        <span class="pouch__weight"><?= e($pk['size']) ?></span>
        <span class="pouch__shine"></span>
    </div>
    <div class="box">
        <div class="box__front">
            <span class="box__band"></span>
            <b><?= e($pk['brand']) ?></b>
            <small><?= e($pk['product']) ?></small>
        </div>
        <div class="box__side"></div>
        <div class="box__top"></div>
    </div>
    <div class="chip chip--a"><i class="chip__ico chip__ico--pin"></i><span><b>Dieline ready</b>CMYK · 3 mm bleed</span></div>
</div>
