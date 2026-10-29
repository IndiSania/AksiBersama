<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$eventId = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare("SELECT e.*, (SELECT COUNT(*) FROM registrations r WHERE r.event_id=e.id AND r.status IN ('menunggu','diterima','hadir')) AS registered_count FROM events e WHERE e.id=? AND e.status='published' LIMIT 1");
$stmt->execute([$eventId]);
$event = $stmt->fetch();
if (!$event) { http_response_code(404); exit('Kegiatan tidak tersedia.'); }

$errors = [];
$name = $email = $phone = $motivation = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $motivation = trim((string)($_POST['motivation'] ?? ''));

    if (mb_strlen($name) < 3 || mb_strlen($name) > 100) $errors[] = 'Nama harus terdiri dari 3–100 karakter.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) $errors[] = 'Masukkan alamat email yang valid.';
    if ($phone === '' || mb_strlen($phone) > 30) $errors[] = 'Masukkan nomor telepon yang dapat dihubungi.';
    if (mb_strlen($motivation) > 1000) $errors[] = 'Motivasi maksimal 1.000 karakter.';
    if ((int)$event['registered_count'] >= (int)$event['capacity']) $errors[] = 'Maaf, kuota kegiatan telah terpenuhi.';
    if (strtotime($event['event_date']) < strtotime(date('Y-m-d'))) $errors[] = 'Pendaftaran untuk kegiatan ini sudah ditutup.';

    if (!$errors) {
        try {
            $insert = $pdo->prepare("INSERT INTO registrations (event_id, name, email, phone, motivation, status) VALUES (?, ?, ?, ?, ?, 'menunggu')");
            $insert->execute([$event['id'], $name, $email, $phone, $motivation]);
            $_SESSION['registration_success'] = ['name' => $name, 'event' => $event['title']];
            redirect('registration-success.php');
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') $errors[] = 'Email ini sudah terdaftar untuk kegiatan tersebut.';
            else $errors[] = 'Pendaftaran belum berhasil diproses. Silakan coba kembali.';
        }
    }
}

$pageTitle = 'Pendaftaran Relawan';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero page-hero-small"><div class="container"><a class="back-link" href="<?= e(url('event.php?id=' . $event['id'])) ?>">← Kembali ke detail kegiatan</a><span class="eyebrow">JADI BAGIAN DARI PERUBAHAN</span><h1>Daftar sebagai relawan.</h1><p>Isi informasi di bawah dengan benar. Pendaftaranmu akan ditinjau oleh pengelola kegiatan.</p></div></section>
<section class="section form-section"><div class="container registration-layout">
    <div class="form-card">
        <div class="form-card-heading"><span class="form-step">01</span><div><h2>Informasi relawan</h2><p>Pastikan informasi kontakmu aktif.</p></div></div>
        <?php if ($errors): ?><div class="alert alert-error"><strong>Periksa kembali:</strong><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" action="<?= e(url('register.php?event_id=' . $event['id'])) ?>" class="form-stack">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="event_id" value="<?= (int)$event['id'] ?>">
            <label>Nama lengkap <span>*</span><input type="text" name="name" value="<?= e($name) ?>" maxlength="100" placeholder="Masukkan nama lengkap" required autocomplete="name"></label>
            <div class="form-row"><label>Email aktif <span>*</span><input type="email" name="email" value="<?= e($email) ?>" maxlength="150" placeholder="nama@email.com" required autocomplete="email"></label><label>Nomor telepon <span>*</span><input type="tel" name="phone" value="<?= e($phone) ?>" maxlength="30" placeholder="08xxxxxxxxxx" required autocomplete="tel"></label></div>
            <label>Alasan ingin bergabung <small>(opsional)</small><textarea name="motivation" rows="4" maxlength="1000" placeholder="Ceritakan secara singkat alasanmu ingin ikut berkontribusi..."><?= e($motivation) ?></textarea></label>
            <label class="consent-check"><input type="checkbox" required><span>Saya memastikan informasi yang diberikan benar dan bersedia dihubungi terkait kegiatan ini.</span></label>
            <button class="btn btn-primary btn-block" type="submit">Kirim pendaftaran <span>↗</span></button>
            <p class="form-privacy">Informasi pendaftaran digunakan oleh pengelola untuk mengatur partisipasi kegiatan.</p>
        </form>
    </div>
    <aside class="event-summary"><span class="summary-label">KAMU AKAN BERGABUNG DI</span><div class="summary-art">✳</div><h3><?= e($event['title']) ?></h3><div class="summary-meta"><span>◷</span><?= e(format_date($event['event_date'])) ?></div><div class="summary-meta"><span>⌖</span><?= e($event['location']) ?></div><div class="summary-divider"></div><p>Terima kasih sudah meluangkan waktu untuk membuat perubahan bersama.</p><a href="<?= e(url('event.php?id=' . $event['id'])) ?>" class="text-link">Lihat detail kegiatan →</a></aside>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
