<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
$success = $_SESSION['registration_success'] ?? null;
unset($_SESSION['registration_success']);
if (!$success) redirect('index.php');
$pageTitle = 'Pendaftaran Terkirim';
require __DIR__ . '/includes/header.php';
?>
<section class="success-section"><div class="success-card"><div class="success-symbol">✓</div><span class="eyebrow">PENDAFTARAN TERKIRIM</span><h1>Terima kasih, <?= e($success['name']) ?>.</h1><p>Pendaftaranmu untuk kegiatan <strong><?= e($success['event']) ?></strong> sudah kami terima. Status awal pendaftaran adalah menunggu peninjauan pengelola.</p><div class="success-next"><span>01</span><p><strong>Pengelola meninjau pendaftaran</strong><small>Admin akan memeriksa data yang kamu kirimkan.</small></p></div><div class="success-next"><span>02</span><p><strong>Tunggu informasi selanjutnya</strong><small>Pastikan nomor telepon dan emailmu aktif.</small></p></div><a class="btn btn-primary btn-block" href="<?= e(url('index.php')) ?>">Kembali ke beranda <span>↗</span></a></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
