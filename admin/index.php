<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
$stats = $pdo->query("SELECT
 (SELECT COUNT(*) FROM events) AS events_total,
 (SELECT COUNT(*) FROM events WHERE status='published') AS events_published,
 (SELECT COUNT(*) FROM registrations) AS registrations_total,
 (SELECT COUNT(*) FROM registrations WHERE status='menunggu') AS registrations_pending,
 (SELECT COUNT(*) FROM registrations WHERE status='diterima') AS registrations_accepted")->fetch();
$recent = $pdo->query("SELECT r.*, e.title AS event_title FROM registrations r JOIN events e ON e.id=r.event_id ORDER BY r.created_at DESC LIMIT 6")->fetchAll();
$adminTitle = 'Ringkasan dashboard'; $adminActive = 'dashboard';
require __DIR__ . '/_layout.php';
?>
<div class="admin-welcome"><div><span class="eyebrow">RINGKASAN AKTIVITAS</span><h2>Halo, <?= e($_SESSION['admin_name'] ?? 'Admin') ?> 👋</h2><p>Berikut gambaran aktivitas AksiBersama saat ini.</p></div><a class="btn btn-primary" href="<?= e(url('admin/events.php?action=create')) ?>">+ Tambah kegiatan</a></div>
<div class="dashboard-stats"><div class="dashboard-stat"><span class="dash-stat-icon icon-green">✳</span><span class="dash-stat-label">Total kegiatan</span><strong><?= (int)$stats['events_total'] ?></strong><small><?= (int)$stats['events_published'] ?> dipublikasikan</small></div><div class="dashboard-stat"><span class="dash-stat-icon icon-amber">♧</span><span class="dash-stat-label">Pendaftar relawan</span><strong><?= (int)$stats['registrations_total'] ?></strong><small>Seluruh pendaftaran</small></div><div class="dashboard-stat"><span class="dash-stat-icon icon-blue">◷</span><span class="dash-stat-label">Menunggu tinjauan</span><strong><?= (int)$stats['registrations_pending'] ?></strong><small>Perlu ditindaklanjuti</small></div><div class="dashboard-stat"><span class="dash-stat-icon icon-purple">✓</span><span class="dash-stat-label">Diterima</span><strong><?= (int)$stats['registrations_accepted'] ?></strong><small>Relawan terkonfirmasi</small></div></div>
<section class="admin-panel"><div class="panel-header"><div><h2>Pendaftaran terbaru</h2><p>Pantau pendaftaran relawan yang masuk.</p></div><a class="text-link" href="<?= e(url('admin/registrations.php')) ?>">Lihat semua →</a></div>
<?php if ($recent): ?><div class="table-wrap"><table class="data-table"><thead><tr><th>Nama relawan</th><th>Kegiatan</th><th>Tanggal daftar</th><th>Status</th><th></th></tr></thead><tbody><?php foreach ($recent as $r): ?><tr><td><div class="person-cell"><span class="table-avatar"><?= e(strtoupper(substr($r['name'],0,1))) ?></span><span><strong><?= e($r['name']) ?></strong><small><?= e($r['email']) ?></small></span></div></td><td><?= e($r['event_title']) ?></td><td><?= e(format_date(substr($r['created_at'],0,10))) ?></td><td><span class="status status-<?= e($r['status']) ?>"><?= e(ucfirst($r['status'])) ?></span></td><td><a class="table-action" href="<?= e(url('admin/registrations.php')) ?>">Kelola</a></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><div class="empty-admin"><span>♧</span><h3>Belum ada pendaftaran</h3><p>Pendaftaran relawan akan muncul di sini.</p></div><?php endif; ?></section>
<div class="admin-tip"><span>✦</span><div><strong>Tips pengelolaan</strong><p>Pastikan informasi kegiatan, tanggal, lokasi, dan kuota selalu diperbarui agar relawan mendapatkan informasi yang tepat.</p></div></div>
<?php require __DIR__ . '/_footer.php'; ?>
