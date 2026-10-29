<?php
$pageTitle = $pageTitle ?? 'Gerakan Kebaikan Dimulai dari Kita';
$activePage = $activePage ?? '';
$flashMessage = get_flash();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#163b35">
    <meta name="description" content="AksiBersama menghubungkan relawan dengan kegiatan sosial yang berdampak bagi masyarakat dan lingkungan.">
    <title><?= e($pageTitle) ?> | <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="<?= e(url('index.php')) ?>" aria-label="AksiBersama beranda">
            <span class="brand-mark"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="M16 27S4.5 20.2 4.5 11.8A6.3 6.3 0 0 1 16 8.6a6.3 6.3 0 0 1 11.5 3.2C27.5 20.2 16 27 16 27Z" fill="currentColor"/><path d="M10 16.5h4l2-4 3 7 2-3h2" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span>Aksi<span class="brand-light">Bersama</span></span>
        </a>
        <button class="nav-toggle" type="button" aria-label="Buka menu" aria-expanded="false"><span></span><span></span><span></span></button>
        <nav class="main-nav" aria-label="Navigasi utama">
            <a class="<?= $activePage === 'home' ? 'active' : '' ?>" href="<?= e(url('index.php')) ?>">Beranda</a>
            <a href="<?= e(url('index.php#kegiatan')) ?>">Kegiatan</a>
            <a href="<?= e(url('index.php#tentang')) ?>">Tentang Kami</a>
            <?php if (is_admin()): ?>
                <a href="<?= e(url('admin/index.php')) ?>">Dashboard</a>
                <a class="nav-cta" href="<?= e(url('logout.php')) ?>">Keluar</a>
            <?php else: ?>
                <a class="nav-cta" href="<?= e(url('login.php')) ?>">Masuk Admin <span aria-hidden="true">↗</span></a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<?php if ($flashMessage): ?>
    <div class="container flash-wrap"><div class="alert alert-<?= e($flashMessage['type']) ?>" role="status"><?= e($flashMessage['message']) ?></div></div>
<?php endif; ?>
<main>
