<div class="mock mock-lumen">
    <div class="mock-lumen__halo"></div>
    <div class="phone phone--lumen">
        <div class="phone__screen">
            <div class="ui-status"><span>9:41</span><span class="ui-status__icons"><i></i><i></i><i></i></span></div>
            <div class="lu-top"><small>Lumen · Live</small><b>Listening</b></div>
            <div class="lu-orb"><span></span><span></span><span></span></div>
            <div class="lu-wave">
                <?php for ($k = 0; $k < 28; $k++): $hgt = 18 + abs(sin($k * 0.7) * cos($k * 0.23)) * 82; ?><i style="--h:<?= round($hgt) ?>%;--d:<?= $k * 40 ?>ms"></i><?php endfor; ?>
            </div>
            <div class="lu-card">
                <small>FR → EN</small>
                <p class="lu-src">« Où est la gare la plus proche ? »</p>
                <p>“Where is the nearest station?”</p>
            </div>
            <div class="lu-dock"><span></span><span class="on"></span><span></span></div>
        </div>
    </div>
    <div class="chip chip--a"><i class="chip__ico chip__ico--pin"></i><span><b>Scene saved</b>Café Kopi · 19:02</span></div>
    <div class="chip chip--b">
        <svg class="chip__glasses" viewBox="0 0 40 16" aria-hidden="true"><rect x="1" y="2" width="15" height="11" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><rect x="24" y="2" width="15" height="11" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><path d="M16 6q4-3 8 0" fill="none" stroke="currentColor" stroke-width="2"/></svg>
        <span><b>Glass FIT</b>Battery 82%</span>
    </div>
</div>
