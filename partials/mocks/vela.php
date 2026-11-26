<div class="mock mock-vela">
    <div class="phone phone--vela phone--back">
        <div class="phone__screen">
            <div class="ui-status"><span>7:12</span><span class="ui-status__icons"><i></i><i></i><i></i></span></div>
            <div class="ve-head"><small>Last night</small><b>7h 42m</b></div>
            <div class="ve-hypno">
                <?php $stages = [1,2,3,3,2,1,2,3,2,4,2,1,2,3,3,2,4,4,2,1,2,2,4,1]; foreach ($stages as $s): ?><i class="s<?= $s ?>"></i><?php endforeach; ?>
            </div>
            <div class="ve-legend"><span><i class="s3"></i>Deep</span><span><i class="s2"></i>Core</span><span><i class="s4"></i>REM</span></div>
            <div class="ve-tip"><small>Today</small>A 20-minute walk before 10:00 will help you fall asleep 18 min earlier.</div>
        </div>
    </div>
    <div class="phone phone--vela phone--front">
        <div class="phone__screen">
            <div class="ui-status"><span>7:12</span><span class="ui-status__icons"><i></i><i></i><i></i></span></div>
            <div class="ve-head"><small>Tuesday, 24 Nov</small><b>Good morning</b></div>
            <div class="ve-rings">
                <svg viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="52" class="t"/><circle cx="60" cy="60" r="52" class="r r1" pathLength="100"/>
                    <circle cx="60" cy="60" r="40" class="t"/><circle cx="60" cy="60" r="40" class="r r2" pathLength="100"/>
                    <circle cx="60" cy="60" r="28" class="t"/><circle cx="60" cy="60" r="28" class="r r3" pathLength="100"/>
                </svg>
                <span><b>86</b>Recovery</span>
            </div>
            <ul class="ve-stats">
                <li><i class="c1"></i>Sleep<b>92</b></li>
                <li><i class="c2"></i>HRV<b>64 ms</b></li>
                <li><i class="c3"></i>Strain<b>Low</b></li>
            </ul>
        </div>
    </div>
</div>
