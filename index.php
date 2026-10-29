<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Gerakan Kebaikan Dimulai dari Kita';
$activePage = 'home';

$stats = $pdo->query("SELECT
    (SELECT COUNT(*) FROM events WHERE status='published' AND event_date >= CURDATE()) AS events_count,
    (SELECT COUNT(*) FROM registrations WHERE status IN ('diterima','hadir')) AS volunteers_count,
    (SELECT COUNT(*) FROM events WHERE status='published') AS total_events")->fetch();

$stmt = $pdo->prepare("SELECT e.*, (SELECT COUNT(*) FROM registrations r WHERE r.event_id=e.id AND r.status IN ('menunggu','diterima','hadir')) AS registered_count
    FROM events e WHERE e.status='published' AND e.event_date >= CURDATE()
    ORDER BY e.event_date ASC LIMIT 3");
$stmt->execute();
$events = $stmt->fetchAll();

$categoryLabels = [
    'lingkungan' => 'Lingkungan',
    'pendidikan' => 'Pendidikan',
    'sosial' => 'Sosial',
    'kesehatan' => 'Kesehatan',
    'kemanusiaan' => 'Kemanusiaan',
    'lainnya' => 'Lainnya'
];
$categoryIcons = ['lingkungan'=>'✳','pendidikan'=>'✎','sosial'=>'♡','kesehatan'=>'✚','kemanusiaan'=>'☼','lainnya'=>'✦'];

require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="hero-orb orb-one"></div><div class="hero-orb orb-two"></div>
    <div class="container hero-grid">
        <div class="hero-copy">
            <div class="eyebrow eyebrow-light"><span class="eyebrow-dot"></span> BERSAMA, DAMPAK JADI NYATA</div>
            <h1>Kebaikan kecil.<br><span>Dampak yang besar.</span></h1>
            <p class="hero-lead">Temukan kegiatan sosial di sekitarmu, bergabung dengan orang-orang baik, dan jadilah bagian dari perubahan yang ingin kamu lihat.</p>
            <div class="hero-actions"><a class="btn btn-primary btn-light" href="#kegiatan">Temukan kegiatan <span>↗</span></a><a class="text-link text-link-light" href="#tentang">Kenali AksiBersama <span>↓</span></a></div>
            <div class="hero-proof"><div class="avatar-stack"><span>R</span><span>A</span><span>N</span><span>+</span></div><span>Setiap aksi berarti. Mulai dari kamu.</span></div>
        </div>
        <div class="hero-visual">
            <div class="visual-label"><span class="pulse"></span> GERAKAN HARI INI</div>
            <div class="visual-photo" role="img" aria-label="Relawan bekerja bersama dalam kegiatan sosial">
                <div class="photo-overlay"></div>
                <div class="photo-caption"><span class="caption-kicker">BERGERAK BERSAMA</span><strong>Untuk lingkungan<br>yang lebih baik.</strong></div>
            </div>
            <div class="floating-card floating-card-top"><span class="floating-icon">✳</span><span><strong>Mulai dari sekitar</strong><small>Perubahan dimulai di komunitasmu</small></span></div>
            <div class="floating-card floating-card-bottom"><span class="mini-check">✓</span><span><strong>Satu aksi hari ini</strong><small>Bisa berarti bagi banyak orang</small></span></div>
        </div>
    </div>
    <div class="hero-bottom-line"><div class="container hero-bottom-inner"><span>WAKTU UNTUK PEDULI</span><span class="line"></span><span>WAKTU UNTUK BERGERAK</span><span class="line"></span><span>WAKTU UNTUK BERBAGI</span></div></div>
</section>

<section class="stats-section">
    <div class="container stats-grid">
        <div class="stats-intro"><span class="eyebrow">KEBAIKAN YANG TERHUBUNG</span><p>Ruang bertemu bagi siapa pun yang ingin berkontribusi.</p></div>
        <div class="stat-item"><strong><?= number_format((int)$stats['events_count']) ?><span>+</span></strong><span>Kegiatan tersedia</span></div>
        <div class="stat-item"><strong><?= number_format((int)$stats['volunteers_count']) ?><span>+</span></strong><span>Relawan diterima</span></div>
        <div class="stat-item"><strong><?= number_format((int)$stats['total_events']) ?><span>+</span></strong><span>Inisiatif terdaftar</span></div>
    </div>
</section>

<section class="section section-events" id="kegiatan">
    <div class="container">
        <div class="section-heading">
            <div><span class="eyebrow">PILIH AKSIMU</span><h2>Temukan kegiatan<br>yang berarti bagimu.</h2></div>
            <p>Setiap orang punya cara berbeda untuk peduli. Temukan kegiatan yang sesuai dengan minat dan waktumu.</p>
        </div>
        <div class="category-row">
            <span class="category-chip active-chip">Semua kegiatan</span>
            <span class="category-chip">Lingkungan</span><span class="category-chip">Pendidikan</span><span class="category-chip">Sosial</span><span class="category-chip">Kesehatan</span>
        </div>
        <?php if ($events): ?>
        <div class="event-grid">
            <?php foreach ($events as $i => $event): ?>
                <article class="event-card">
                    <a class="event-image event-image-<?= ($i % 3) + 1 ?>" href="<?= e(url('event.php?id=' . $event['id'])) ?>" aria-label="Lihat <?= e($event['title']) ?>">
                        <span class="event-category"><span><?= e($categoryIcons[$event['category']] ?? '✦') ?></span><?= e($categoryLabels[$event['category']] ?? 'Lainnya') ?></span>
                        <?php if (!empty($event['image_url'])): ?><img src="<?= e($event['image_url']) ?>" alt="" loading="lazy"><?php else: ?><span class="image-placeholder-mark"><?= e($categoryIcons[$event['category']] ?? '✦') ?></span><?php endif; ?>
                        <span class="image-arrow">↗</span>
                    </a>
                    <div class="event-card-body">
                        <div class="event-meta"><span>◷ <?= e(format_date($event['event_date'])) ?></span><span>⌖ <?= e($event['location']) ?></span></div>
                        <h3><a href="<?= e(url('event.php?id=' . $event['id'])) ?>"><?= e($event['title']) ?></a></h3>
                        <p><?= e(excerpt($event['description'], 115)) ?></p>
                        <div class="event-card-bottom"><span class="capacity"><span class="capacity-dot"></span><?= (int)$event['registered_count'] ?> relawan terdaftar</span><a class="arrow-link" href="<?= e(url('event.php?id=' . $event['id'])) ?>">Lihat detail <span>→</span></a></div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><div class="empty-icon">✳</div><h3>Kegiatan baru sedang disiapkan</h3><p>Belum ada kegiatan mendatang yang dipublikasikan. Silakan kembali lagi nanti.</p><?php if (is_admin()): ?><a class="btn btn-primary" href="<?= e(url('admin/events.php')) ?>">Kelola kegiatan</a><?php endif; ?></div>
        <?php endif; ?>
        <div class="section-endnote"><span class="endnote-line"></span><span>Tempat yang baik dimulai dari orang-orang yang peduli.</span><span class="endnote-line"></span></div>
    </div>
</section>

<section class="mission-section" id="tentang">
    <div class="container mission-grid">
        <div class="mission-art">
            <div class="mission-photo"></div><div class="mission-stamp"><span>✳</span><strong>AKSI NYATA</strong><small>DIMULAI BERSAMA</small></div>
            <div class="mission-note"><span class="note-icon">♡</span><span><strong>Berangkat dari kepedulian</strong><small>Tumbuh melalui kolaborasi</small></span></div>
        </div>
        <div class="mission-copy"><span class="eyebrow">MENGAPA AKSIBERSAMA?</span><h2>Karena perubahan<br>tidak harus dimulai<br>dengan hal besar.</h2><p>Kami percaya setiap orang punya sesuatu untuk dibagikan. AksiBersama hadir sebagai ruang yang mempertemukan niat baik dengan kesempatan untuk bertindak.</p>
            <div class="value-list"><div><span class="value-number">01</span><span><strong>Mudah untuk terlibat</strong><small>Temukan kegiatan dan daftar sebagai relawan dengan langkah sederhana.</small></span></div><div><span class="value-number">02</span><span><strong>Bertumbuh bersama komunitas</strong><small>Bangun koneksi dan lakukan hal bermakna bersama orang lain.</small></span></div><div><span class="value-number">03</span><span><strong>Setiap kontribusi berarti</strong><small>Waktu, tenaga, dan perhatianmu dapat membawa dampak positif.</small></span></div></div>
            <a class="btn btn-dark" href="#kegiatan">Mulai ambil bagian <span>↗</span></a>
        </div>
    </div>
</section>

<section class="cta-section"><div class="container cta-inner"><div><span class="eyebrow eyebrow-light">SATU LANGKAH BISA BERARTI</span><h2>Ada kebaikan yang<br>menunggu untuk kamu mulai.</h2></div><a class="btn btn-primary btn-light" href="#kegiatan">Cari kegiatan <span>↗</span></a><div class="cta-decoration">✳</div></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
