<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$adminTitle = $adminTitle ?? 'Dashboard';
$adminActive = $adminActive ?? 'dashboard';
$flashMessage = get_flash();
?>
<!doctype html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= e($adminTitle) ?> | AksiBersama Admin</title><link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>"></head><body class="admin-body">
<aside class="admin-sidebar"><a class="brand" href="<?= e(url('index.php')) ?>"><span class="brand-mark"><svg viewBox="0 0 32 32"><path d="M16 27S4.5 20.2 4.5 11.8A6.3 6.3 0 0 1 16 8.6a6.3 6.3 0 0 1 11.5 3.2C27.5 20.2 16 27 16 27Z" fill="currentColor"/></svg></span><span>Aksi<span class="brand-light">Bersama</span></span></a><div class="sidebar-label">MENU UTAMA</div><nav class="sidebar-nav"><a class="<?= $adminActive==='dashboard'?'sidebar-active':'' ?>" href="<?= e(url('admin/index.php')) ?>"><span>▦</span>Ringkasan</a><a class="<?= $adminActive==='events'?'sidebar-active':'' ?>" href="<?= e(url('admin/events.php')) ?>"><span>✳</span>Kelola kegiatan</a><a class="<?= $adminActive==='registrations'?'sidebar-active':'' ?>" href="<?= e(url('admin/registrations.php')) ?>"><span>♧</span>Pendaftaran relawan</a></nav><div class="sidebar-bottom"><div class="admin-profile"><span class="profile-avatar"><?= e(strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1))) ?></span><span><strong><?= e($_SESSION['admin_name'] ?? 'Admin') ?></strong><small>Administrator</small></span></div><a class="sidebar-logout" href="<?= e(url('logout.php')) ?>">↗ Keluar</a><a class="back-site-link" href="<?= e(url('index.php')) ?>">← Lihat situs publik</a></div></aside>
<div class="admin-main"><header class="admin-topbar"><div><span class="admin-top-kicker">AKSIBERSAMA / ADMIN</span><h1><?= e($adminTitle) ?></h1></div><div class="topbar-date"><?= e(format_date(date('Y-m-d'))) ?></div></header>
<?php if ($flashMessage): ?><div class="alert alert-<?= e($flashMessage['type']) ?>"><?= e($flashMessage['message']) ?></div><?php endif; ?>
