<div class="mock mock-orbit">
    <div class="browser">
        <div class="browser__bar"><i></i><i></i><i></i><span class="browser__url">app.orbitbank.co/cashflow</span></div>
        <div class="ob">
            <aside class="ob__side">
                <b class="ob__logo"></b>
                <span class="on"></span><span></span><span></span><span></span><span></span>
                <span class="ob__avatar"></span>
            </aside>
            <div class="ob__main">
                <div class="ob__top"><div><small>Good morning, Sarah</small><b>Cash flow</b></div><span class="ob__btn">+ New invoice</span></div>
                <div class="ob__row">
                    <div class="ob__card ob__card--hero">
                        <small>Cash balance</small><strong>Rp 2.48B</strong><span class="up">▲ 12.4% vs Oct</span>
                        <svg viewBox="0 0 120 36" preserveAspectRatio="none"><path d="M0 30 C15 28 20 20 32 22 S52 12 64 15 84 6 96 9 112 2 120 4" fill="none" stroke="currentColor" stroke-width="2"/><path d="M0 30 C15 28 20 20 32 22 S52 12 64 15 84 6 96 9 112 2 120 4 V36 H0Z" fill="currentColor" opacity=".18"/></svg>
                    </div>
                    <div class="ob__card"><small>Incoming</small><strong>840M</strong><span class="bar-mini"><i style="--w:72%"></i></span></div>
                    <div class="ob__card"><small>Outgoing</small><strong>512M</strong><span class="bar-mini bar-mini--b"><i style="--w:44%"></i></span></div>
                </div>
                <div class="ob__card ob__chart">
                    <header><b>Last 12 months</b><span><i class="lg lg--a"></i>In <i class="lg lg--b"></i>Out</span></header>
                    <div class="ob__bars">
                        <?php $in = [42,55,48,62,58,70,66,74,69,82,78,90]; $outv = [30,34,40,36,44,41,48,45,50,47,52,49];
                        foreach ($in as $k => $v): ?><span style="--a:<?= $v ?>%;--b:<?= $outv[$k] ?>%"><i></i><i></i></span><?php endforeach; ?>
                    </div>
                </div>
                <div class="ob__card ob__list">
                    <div><span class="av av--1"></span><b>Kopi Senja</b><em>Rp 24.500.000</em><span class="st st--ok">Paid</span></div>
                    <div><span class="av av--2"></span><b>Atlas Mobility</b><em>Rp 118.000.000</em><span class="st st--due">Due 3d</span></div>
                    <div><span class="av av--3"></span><b>Fieldnote</b><em>Rp 9.750.000</em><span class="st st--ok">Paid</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
