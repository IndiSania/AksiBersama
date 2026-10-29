<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$validStatuses = ['menunggu','diterima','ditolak','hadir'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $status = (string)($_POST['status'] ?? '');
    if ($id && in_array($status, $validStatuses, true)) {
        $stmt = $pdo->prepare("UPDATE registrations SET status=? WHERE id=?");
        $stmt->execute([$status, $id]);
        flash('success','Status pendaftaran berhasil diperbarui.');
    } else flash('error','Data status tidak valid.');
    redirect('admin/registrations.php');
}
$filter = (string)($_GET['status'] ?? 'semua');
if (!in_array($filter, array_merge(['semua'], $validStatuses), true)) $filter = 'semua';
$sql = "SELECT r.*, e.title AS event_title, e.event_date FROM registrations r JOIN events e ON e.id=r.event_id";
$params = [];
if ($filter !== 'semua') { $sql .= " WHERE r.status=?"; $params[] = $filter; }
$sql .= " ORDER BY r.created_at DESC";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $registrations = $stmt->fetchAll();
$adminTitle = 'Pendaftaran relawan'; $adminActive = 'registrations';
require __DIR__ . '/_layout.php';
?>
<div class="admin-page-actions"><p>Tinjau data relawan dan perbarui status pendaftaran mereka.</p><div class="search-box"><span>⌕</span><input type="search" placeholder="Cari relawan..." data-table-search="#registrations-table"></div></div>
<div class="filter-tabs"><?php foreach (['semua'=>'Semua','menunggu'=>'Menunggu','diterima'=>'Diterima','ditolak'=>'Ditolak','hadir'=>'Hadir'] as $key=>$label): ?><a class="<?= $filter===$key?'filter-active':'' ?>" href="<?= e(url('admin/registrations.php?status=' . $key)) ?>"><?= e($label) ?></a><?php endforeach; ?></div>
<div class="admin-panel"><div class="panel-header"><div><h2>Data pendaftaran</h2><p><?= count($registrations) ?> pendaftaran<?= $filter!=='semua'?' · filter '.e($filter):'' ?></p></div></div>
<?php if ($registrations): ?><div class="table-wrap"><table class="data-table" id="registrations-table"><thead><tr><th>Relawan</th><th>Kegiatan</th><th>Tanggal daftar</th><th>Kontak</th><th>Status</th><th>Perbarui status</th></tr></thead><tbody><?php foreach ($registrations as $r): ?><tr><td><div class="person-cell"><span class="table-avatar"><?= e(strtoupper(substr($r['name'],0,1))) ?></span><span><strong><?= e($r['name']) ?></strong><small><?= e($r['motivation'] ?: 'Belum menambahkan motivasi') ?></small></span></div></td><td><strong><?= e($r['event_title']) ?></strong><small class="table-subline"><?= e(format_date($r['event_date'])) ?></small></td><td><?= e(format_date(substr($r['created_at'],0,10))) ?></td><td><span class="contact-lines"><?= e($r['email']) ?><br><?= e($r['phone']) ?></span></td><td><span class="status status-<?= e($r['status']) ?>"><?= e(ucfirst($r['status'])) ?></span></td><td><form method="post" class="status-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><select name="status" aria-label="Status untuk <?= e($r['name']) ?>"><?php foreach ($validStatuses as $s): ?><option value="<?= e($s) ?>" <?= $r['status']===$s?'selected':'' ?>><?= e(ucfirst($s)) ?></option><?php endforeach; ?></select><button type="submit" class="btn btn-small">Simpan</button></form></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><div class="empty-admin"><span>♧</span><h3>Belum ada data pendaftaran</h3><p>Data pendaftaran relawan akan ditampilkan di halaman ini.</p></div><?php endif; ?></div>
<?php require __DIR__ . '/_footer.php'; ?>
