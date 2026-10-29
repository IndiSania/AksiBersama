<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$categories = ['lingkungan'=>'Lingkungan','pendidikan'=>'Pendidikan','sosial'=>'Sosial','kesehatan'=>'Kesehatan','kemanusiaan'=>'Kemanusiaan','lainnya'=>'Lainnya'];
$action = $_GET['action'] ?? 'list';
$errors = [];
$form = ['title'=>'','category'=>'sosial','event_date'=>'','location'=>'','capacity'=>'30','description'=>'','requirements'=>'','image_url'=>'','status'=>'draft'];
$editId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($action === 'edit' && $editId) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id=?");
    $stmt->execute([$editId]);
    $existing = $stmt->fetch();
    if (!$existing) { flash('error','Kegiatan tidak ditemukan.'); redirect('admin/events.php'); }
    $form = array_merge($form, $existing);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $postedAction = $_POST['form_action'] ?? 'save';
    if ($postedAction === 'delete') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM events WHERE id=?");
            $stmt->execute([$id]);
            flash('success','Kegiatan berhasil dihapus.');
        }
        redirect('admin/events.php');
    }
    $editId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
    foreach (array_keys($form) as $key) {
        if (isset($_POST[$key])) $form[$key] = is_string($_POST[$key]) ? trim($_POST[$key]) : $form[$key];
    }
    $form['capacity'] = (string)($_POST['capacity'] ?? '30');
    if (mb_strlen($form['title']) < 5 || mb_strlen($form['title']) > 150) $errors[] = 'Judul kegiatan harus terdiri dari 5–150 karakter.';
    if (!isset($categories[$form['category']])) $errors[] = 'Pilih kategori yang valid.';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $form['event_date']) || !strtotime($form['event_date'])) $errors[] = 'Tanggal kegiatan tidak valid.';
    if (mb_strlen($form['location']) < 3 || mb_strlen($form['location']) > 180) $errors[] = 'Lokasi harus terdiri dari 3–180 karakter.';
    if (!filter_var($form['capacity'], FILTER_VALIDATE_INT) || (int)$form['capacity'] < 1 || (int)$form['capacity'] > 10000) $errors[] = 'Kuota harus berupa angka antara 1 dan 10.000.';
    if (mb_strlen($form['description']) < 20) $errors[] = 'Deskripsi minimal 20 karakter.';
    if (!in_array($form['status'], ['draft','published'], true)) $errors[] = 'Status kegiatan tidak valid.';
    if ($form['image_url'] !== '' && (!filter_var($form['image_url'], FILTER_VALIDATE_URL) || !in_array(strtolower((string)parse_url($form['image_url'], PHP_URL_SCHEME)), ['http','https'], true))) $errors[] = 'URL gambar harus berupa alamat HTTP/HTTPS yang valid.';
    if (!$errors) {
        $data = [$form['title'],$form['category'],$form['event_date'],$form['location'],(int)$form['capacity'],$form['description'],$form['requirements'],$form['image_url'] ?: null,$form['status']];
        if ($editId) {
            $data[] = $editId;
            $stmt = $pdo->prepare("UPDATE events SET title=?,category=?,event_date=?,location=?,capacity=?,description=?,requirements=?,image_url=?,status=? WHERE id=?");
            $stmt->execute($data);
            flash('success','Informasi kegiatan berhasil diperbarui.');
        } else {
            $stmt = $pdo->prepare("INSERT INTO events (title,category,event_date,location,capacity,description,requirements,image_url,status,created_by) VALUES (?,?,?,?,?,?,?,?,?,?)");
            $data[] = $_SESSION['admin_id'];
            $stmt->execute($data);
            flash('success','Kegiatan baru berhasil ditambahkan.');
        }
        redirect('admin/events.php');
    }
    $action = $editId ? 'edit' : 'create';
}

$adminTitle = ($action === 'create' || $action === 'edit') ? ($editId ? 'Edit kegiatan' : 'Tambah kegiatan') : 'Kelola kegiatan';
$adminActive = 'events';
require __DIR__ . '/_layout.php';
?>
<?php if ($action === 'create' || $action === 'edit'): ?>
<div class="admin-form-panel"><div class="panel-header"><div><h2><?= $editId ? 'Perbarui informasi kegiatan' : 'Buat kegiatan baru' ?></h2><p>Lengkapi informasi agar calon relawan memahami kegiatan.</p></div><a class="btn btn-outline" href="<?= e(url('admin/events.php')) ?>">← Kembali</a></div>
<?php if ($errors): ?><div class="alert alert-error"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" class="admin-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="form_action" value="save"><?php if ($editId): ?><input type="hidden" name="id" value="<?= (int)$editId ?>"><?php endif; ?>
<div class="form-row"><label>Nama kegiatan <span>*</span><input name="title" maxlength="150" value="<?= e($form['title']) ?>" required></label><label>Kategori <span>*</span><select name="category" required><?php foreach ($categories as $key=>$label): ?><option value="<?= e($key) ?>" <?= $form['category']===$key?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?></select></label></div>
<div class="form-row"><label>Tanggal kegiatan <span>*</span><input type="date" name="event_date" value="<?= e($form['event_date']) ?>" required></label><label>Lokasi <span>*</span><input name="location" maxlength="180" value="<?= e($form['location']) ?>" required></label></div>
<div class="form-row"><label>Kuota relawan <span>*</span><input type="number" name="capacity" min="1" max="10000" value="<?= e((string)$form['capacity']) ?>" required></label><label>Status publikasi<select name="status"><option value="draft" <?= $form['status']==='draft'?'selected':'' ?>>Draft (belum tampil)</option><option value="published" <?= $form['status']==='published'?'selected':'' ?>>Dipublikasikan</option></select></label></div>
<label>Deskripsi kegiatan <span>*</span><textarea name="description" rows="6" required><?= e($form['description']) ?></textarea></label><label>Persiapan / persyaratan relawan<textarea name="requirements" rows="3"><?= e($form['requirements']) ?></textarea></label><label>URL gambar <small>(opsional, gunakan URL gambar publik)</small><input type="url" name="image_url" value="<?= e($form['image_url']) ?>" placeholder="https://contoh.com/gambar.jpg"></label>
<div class="form-actions"><button class="btn btn-primary" type="submit">Simpan kegiatan <span>↗</span></button><a class="btn btn-outline" href="<?= e(url('admin/events.php')) ?>">Batal</a></div></form></div>
<?php else:
$events = $pdo->query("SELECT e.*, (SELECT COUNT(*) FROM registrations r WHERE r.event_id=e.id) AS registrations_count FROM events e ORDER BY e.created_at DESC")->fetchAll();
?>
<div class="admin-page-actions"><p>Kelola informasi, kuota, dan status publikasi kegiatan sosial.</p><a class="btn btn-primary" href="<?= e(url('admin/events.php?action=create')) ?>">+ Tambah kegiatan</a></div>
<div class="admin-panel"><div class="panel-header"><div><h2>Semua kegiatan</h2><p><?= count($events) ?> kegiatan terdaftar</p></div><div class="search-box"><span>⌕</span><input type="search" placeholder="Cari kegiatan..." data-table-search="#events-table"></div></div>
<?php if ($events): ?><div class="table-wrap"><table class="data-table" id="events-table"><thead><tr><th>Nama kegiatan</th><th>Tanggal</th><th>Kuota</th><th>Pendaftar</th><th>Status</th><th>Aksi</th></tr></thead><tbody><?php foreach ($events as $event): ?><tr><td><div class="event-table-name"><span class="table-event-icon">✳</span><span><strong><?= e($event['title']) ?></strong><small><?= e($categories[$event['category']] ?? 'Lainnya') ?> · <?= e($event['location']) ?></small></span></div></td><td><?= e(format_date($event['event_date'])) ?></td><td><?= (int)$event['capacity'] ?></td><td><?= (int)$event['registrations_count'] ?></td><td><span class="status status-<?= e($event['status']) ?>"><?= $event['status']==='published'?'Dipublikasikan':'Draft' ?></span></td><td><div class="table-actions"><a class="table-action" href="<?= e(url('admin/events.php?action=edit&id=' . $event['id'])) ?>">Edit</a><?php if ($event['status']==='published'): ?><a class="table-action" href="<?= e(url('event.php?id=' . $event['id'])) ?>" target="_blank" rel="noopener">Lihat</a><?php endif; ?><form method="post" onsubmit="return confirm('Hapus kegiatan ini beserta data pendaftarannya?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="form_action" value="delete"><input type="hidden" name="id" value="<?= (int)$event['id'] ?>"><button class="table-action danger-action" type="submit">Hapus</button></form></div></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><div class="empty-admin"><span>✳</span><h3>Belum ada kegiatan</h3><p>Tambahkan kegiatan pertama untuk mulai mengundang relawan.</p><a class="btn btn-primary" href="<?= e(url('admin/events.php?action=create')) ?>">+ Tambah kegiatan</a></div><?php endif; ?></div>
<?php endif; ?>
<?php require __DIR__ . '/_footer.php'; ?>
