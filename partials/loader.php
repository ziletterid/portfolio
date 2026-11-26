<div class="loader" aria-hidden="true">
    <div class="loader__ring">
        <svg viewBox="0 0 200 200">
            <circle class="loader__track" cx="100" cy="100" r="92"/>
            <circle class="loader__bar" cx="100" cy="100" r="92" pathLength="100"/>
        </svg>
        <span class="loader__count"><span data-count>0</span><small>%</small></span>
    </div>
    <div class="loader__meta">
        <span><?= e($full) ?></span>
        <span><?= e($p['role']) ?></span>
        <span>Portfolio © <?= date('Y') ?></span>
    </div>
</div>
