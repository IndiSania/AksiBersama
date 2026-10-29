<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(404); exit('Kegiatan tidak ditemukan.'); }
$stmt = $pdo->prepare("SELECT e.*, (SELECT COUNT(*) FROM registrations r WHERE r.event_id=e.id AND r.status IN ('menunggu','diterima','hadir')) AS registered_count FROM events e WHERE e.id=? AND e.status='published' LIMIT 1");
$stmt->execute([$id]);
$event = $stmt->fetch();
if (!$event) { http_response_code(404); exit('Kegiatan tidak ditemukan atau belum dipublikasikan.'); }

$categoryLabels = ['lingkungan'=>'Lingkungan','pendidikan'=>'Pendidikan','sosial'=>'Sosial','kesehatan'=>'Kesehatan','kemanusiaan'=>'Kemanusiaan','lainnya'=>'Lainnya'];
$pageTitle = $event['title'];
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><a class="back-link" href="<?= e(url('index.php#kegiatan')) ?>">← Kembali ke kegiatan</a><span class="eyebrow"><?= e($categoryLabels[$event['category']] ?? 'Kegiatan') ?></span><h1><?= e($event['title']) ?></h1><p><?= e(excerpt($event['description'], 190)) ?></p></div></section>
<section class="section detail-section"><div class="container detail-grid">
    <div class="detail-main">
        <div class="detail-cover detail-cover-<?= (int)$event['id'] % 3 + 1 ?>"><?php if ($event['image_url']): ?><img src="<?= e($event['image_url']) ?>" alt="<?= e($event['title']) ?>"><?php else: ?><span>✳</span><?php endif; ?></div>
        <span class="eyebrow">TENTANG KEGIATAN</span><h2>Bergerak bersama, memberi arti.</h2><div class="detail-description"><?= nl2br(e($event['description'])) ?></div>
        <?php if ($event['requirements']): ?><div class="requirements"><h3>Yang perlu disiapkan</h3><p><?= nl2br(e($event['requirements'])) ?></p></div><?php endif; ?>
    </div>
    <aside class="registration-panel">
        <span class="panel-kicker">INFORMASI KEGIATAN</span>
        <div class="info-row"><span class="info-icon">◷</span><span><small>Tanggal kegiatan</small><strong><?= e(format_date($event['event_date'])) ?></strong></span></div>
        <div class="info-row"><span class="info-icon">⌖</span><span><small>Lokasi</small><strong><?= e($event['location']) ?></strong></span></div>
        <div class="info-row"><span class="info-icon">♧</span><span><small>Kuota relawan</small><strong><?= (int)$event['registered_count'] ?> / <?= (int)$event['capacity'] ?> orang</strong></span></div>
        <div class="capacity-track"><span style="width:<?= min(100, (int)$event['capacity'] > 0 ? round((int)$event['registered_count'] / (int)$event['capacity'] * 100) : 0) ?>%"></span></div>
        <?php if (strtotime($event['event_date']) < strtotime(date('Y-m-d'))): ?>
            <div class="alert alert-info">Pendaftaran kegiatan ini sudah ditutup.</div>
        <?php elseif ((int)$event['registered_count'] >= (int)$event['capacity']): ?>
            <div class="alert alert-info">Kuota relawan telah terpenuhi.</div>
        <?php else: ?>
            <a class="btn btn-primary btn-block" href="<?= e(url('register.php?event_id=' . $event['id'])) ?>">Daftar sebagai relawan <span>↗</span></a>
            <p class="panel-footnote">Pendaftaran gratis. Admin akan meninjau pendaftaranmu.</p>
        <?php endif; ?>
    </aside>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
