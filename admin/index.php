<?php
declare(strict_types=1);

require __DIR__ . '/lib.php';

admin_session();
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header('Cache-Control: no-store');

$c     = site_content();
$name  = $c['person']['first'] ?? 'Admin';
$error = '';
$note  = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $do = $_POST['do'] ?? '';
    if (!csrf_ok($_POST['csrf'] ?? null)) {
        $error = 'Sesi kedaluwarsa. Coba lagi.';
    } elseif ($do === 'setup' && !has_password()) {
        $pw  = (string)($_POST['password'] ?? '');
        $pw2 = (string)($_POST['password2'] ?? '');
        if (!is_local_request()) {
            $error = 'Password pertama hanya bisa dibuat dari komputer sendiri (localhost).';
        } elseif (mb_strlen($pw) < 8) {
            $error = 'Password minimal 8 karakter.';
        } elseif ($pw !== $pw2) {
            $error = 'Kedua password tidak sama.';
        } else {
            set_password($pw);
            login_user();
            header('Location: ./');
            exit;
        }
    } elseif ($do === 'login' && has_password()) {
        if (login_blocked()) {
            $error = 'Terlalu banyak percobaan. Tunggu 15 menit.';
        } elseif (check_password((string)($_POST['password'] ?? ''))) {
            login_user();
            header('Location: ./');
            exit;
        } else {
            login_failed();
            $error = 'Password salah.';
        }
    } elseif ($do === 'logout') {
        logout_user();
        header('Location: ./?out=1');
        exit;
    }
}
if (isset($_GET['out'])) {
    $note = 'Kamu sudah keluar.';
}

$mode = !has_password() ? 'setup' : (is_logged_in() ? 'app' : 'login');
$csrf = $_SESSION['csrf'];
$v    = fn(string $f) => filemtime(__DIR__ . '/' . $f);

/** Ikon garis kecil untuk menu samping. */
function icon(string $n): string
{
    $d = [
        'home'   => '<path d="M3 9.5 10 4l7 5.5V16a1 1 0 0 1-1 1h-3.5v-4.5h-5V17H4a1 1 0 0 1-1-1Z"/>',
        'user'   => '<circle cx="10" cy="7" r="3.2"/><path d="M3.8 17c.8-3.2 3.3-5 6.2-5s5.4 1.8 6.2 5"/>',
        'hero'   => '<rect x="3" y="3.5" width="14" height="13" rx="2"/><path d="M6 8h8M6 11h5"/>',
        'folder' => '<path d="M3 6a1.5 1.5 0 0 1 1.5-1.5h3.2l1.6 1.8h6.2A1.5 1.5 0 0 1 17 7.8v6.7a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 3 14.5Z"/>',
        'grid'   => '<rect x="3" y="3" width="6" height="6" rx="1.2"/><rect x="11" y="3" width="6" height="6" rx="1.2"/><rect x="3" y="11" width="6" height="6" rx="1.2"/><rect x="11" y="11" width="6" height="6" rx="1.2"/>',
        'pen'    => '<path d="M4 16l.8-3.4L13.4 4a1.4 1.4 0 0 1 2 0l.6.6a1.4 1.4 0 0 1 0 2L7.4 15.2Z"/><path d="M11.8 5.6l2.6 2.6"/>',
        'spark'  => '<path d="M10 3v4M10 13v4M3 10h4M13 10h4M5.2 5.2l2.6 2.6M12.2 12.2l2.6 2.6M14.8 5.2l-2.6 2.6M7.8 12.2l-2.6 2.6"/>',
        'quote'  => '<path d="M4 15.5V11c0-3 1.4-5 4-5.5M11 15.5V11c0-3 1.4-5 4-5.5"/><path d="M4 11h3.5v4.5H4zM11 11h3.5v4.5H11z"/>',
        'mail'   => '<rect x="3" y="4.5" width="14" height="11" rx="1.6"/><path d="m3.5 5.5 6.5 5 6.5-5"/>',
        'half'   => '<circle cx="10" cy="10" r="6.5"/><path d="M10 3.5v13a6.5 6.5 0 0 0 0-13Z" fill="currentColor"/>',
    ][$n] ?? '';
    return '<svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Dashboard — <?= e($name) ?></title>
    <link rel="icon" href="../assets/img/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css?v=<?= $v('admin.css') ?>">
</head>
<body class="mode-<?= $mode ?>">

<?php if ($mode !== 'app'): ?>
    <main class="auth">
        <form class="auth__card" method="post" autocomplete="off">
            <span class="auth__mark"><?= e($name[0]) ?></span>
            <?php if ($mode === 'setup'): ?>
                <h1>Buat password admin</h1>
                <p>Dashboard ini untuk mengelola isi portfolio: teks, proyek, dan gambar. Buat password dulu. Kamu akan memakainya setiap kali masuk.</p>
                <?php if (!is_local_request()): ?>
                    <p class="auth__warn">Password pertama hanya bisa dibuat dari komputer tempat website dijalankan (localhost).</p>
                <?php endif; ?>
                <label>Password baru<input type="password" name="password" minlength="8" required autocomplete="new-password"></label>
                <label>Ulangi password<input type="password" name="password2" minlength="8" required autocomplete="new-password"></label>
                <input type="hidden" name="do" value="setup">
            <?php else: ?>
                <h1>Masuk</h1>
                <p>Kelola portfolio <?= e($name) ?>.</p>
                <label>Password<input type="password" name="password" required autofocus autocomplete="current-password"></label>
                <input type="hidden" name="do" value="login">
            <?php endif; ?>
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <?php if ($error): ?><p class="auth__err" role="alert"><?= e($error) ?></p><?php endif; ?>
            <?php if ($note): ?><p class="auth__note"><?= e($note) ?></p><?php endif; ?>
            <button class="btn btn--accent" type="submit"><?= $mode === 'setup' ? 'Simpan & masuk' : 'Masuk' ?></button>
            <a class="auth__back" href="../">← Kembali ke website</a>
        </form>
    </main>
<?php else: ?>
    <div class="shell" data-app data-csrf="<?= e($csrf) ?>">
        <aside class="side">
            <div class="side__brand"><span class="auth__mark auth__mark--sm"><?= e($name[0]) ?></span><div><b>Dashboard</b><small>Portfolio <?= e($name) ?></small></div></div>
            <nav class="side__nav" data-nav>
                <a href="#beranda" data-route="beranda"><?= icon('home') ?>Beranda</a>
                <span class="side__label">Isi website</span>
                <a href="#profil" data-route="profil"><?= icon('user') ?>Profil &amp; kontak</a>
                <a href="#hero" data-route="hero"><?= icon('hero') ?>Layar pembuka</a>
                <a href="#proyek" data-route="proyek"><?= icon('folder') ?>Proyek</a>
                <a href="#galeri" data-route="galeri"><?= icon('grid') ?>Galeri arsip</a>
                <a href="#about" data-route="about"><?= icon('pen') ?>Tentang saya</a>
                <a href="#layanan" data-route="layanan"><?= icon('spark') ?>Layanan &amp; proses</a>
                <a href="#testimoni" data-route="testimoni"><?= icon('quote') ?>Testimoni</a>
                <span class="side__label">Lainnya</span>
                <a href="#pesan" data-route="pesan"><?= icon('mail') ?>Pesan masuk <em data-msg-count hidden></em></a>
                <a href="#v2" data-route="v2"><?= icon('half') ?>Versi oranye</a>
                <a href="#v3" data-route="v3"><?= icon('spark') ?>Versi 3</a>
            </nav>
            <div class="side__end">
                <a class="btn btn--accent" href="../" target="_blank" rel="noopener">Lihat website ↗</a>
                <form method="post"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><input type="hidden" name="do" value="logout"><button class="btn btn--ghost" type="submit">Keluar</button></form>
            </div>
        </aside>

        <main class="main" data-view aria-live="polite">
            <p class="muted">Memuat…</p>
        </main>
    </div>

    <div class="toast" data-toast role="status" aria-live="polite"></div>

    <template id="tpl-dropzone">
        <label class="drop">
            <input type="file" accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml" multiple hidden>
            <span class="drop__icon" aria-hidden="true">↑</span>
            <span class="drop__text"><b>Seret gambar ke sini</b> atau klik untuk memilih</span>
            <span class="drop__hint">JPG, PNG, WebP, GIF, SVG · maks 15 MB · gambar besar otomatis dikecilkan</span>
            <span class="drop__bar"><i></i></span>
        </label>
    </template>

    <script src="admin.js?v=<?= $v('admin.js') ?>" defer></script>
<?php endif; ?>
</body>
</html>
