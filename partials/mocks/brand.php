<?php $br = $pr['brand'] ?? ['name' => $pr['client'], 'tagline' => $pr['category'] ?? '', 'c1' => '#160a6e', 'c2' => '#8a7bff', 'c3' => '#f4f3fb', 'c4' => '#ff8fb7']; ?>
<?php $mark = '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M32 6c14 10 18 24 10 38-4 7-10 12-10 14 0-2-6-7-10-14C14 30 18 16 32 6Z" fill="currentColor"/><path d="M32 18v40M32 34l-8-7M32 42l9-8" stroke="var(--bg)" stroke-width="3.2" stroke-linecap="round" fill="none"/></svg>'; ?>
<div class="mock mock-brand" style="--c1:<?= e($br['c1']) ?>;--c2:<?= e($br['c2']) ?>;--c3:<?= e($br['c3']) ?>;--c4:<?= e($br['c4']) ?>">
    <div class="br-tile br-tile--main" style="--bg:var(--c3)">
        <span class="br-logo"><?= $mark ?></span>
        <b><?= e($br['name']) ?></b>
        <small><?= e($br['tagline']) ?></small>
    </div>
    <div class="br-tile br-tile--dark" style="--bg:var(--c1)"><span class="br-logo"><?= $mark ?></span></div>
    <div class="br-tile br-tile--accent" style="--bg:var(--c2)"><span class="br-logo"><?= $mark ?></span></div>
    <div class="br-tile br-tile--card" style="--bg:var(--c1)">
        <span class="br-logo br-logo--sm"><?= $mark ?></span>
        <span class="br-card-lines"><b><?= e($br['name']) ?></b><i></i><i></i></span>
    </div>
    <div class="br-tile br-tile--swatch">
        <span style="--s:var(--c1)"><i><?= e($br['c1']) ?></i></span>
        <span style="--s:var(--c2)"><i><?= e($br['c2']) ?></i></span>
        <span style="--s:var(--c3)"><i><?= e($br['c3']) ?></i></span>
        <span style="--s:var(--c4)"><i><?= e($br['c4']) ?></i></span>
    </div>
    <div class="br-tile br-tile--type" style="--bg:#fff"><b>Aa</b><small>Geist · Medium<br>Display &amp; Body</small></div>
</div>
