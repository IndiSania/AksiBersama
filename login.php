<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
if (is_admin()) redirect('admin/index.php');

$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Masukkan email dan kata sandi yang valid.';
    } else {
        $stmt = $pdo->prepare("SELECT id, name, email, password_hash FROM admins WHERE email=? LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int)$admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            flash('success', 'Selamat datang kembali, ' . $admin['name'] . '.');
            redirect('admin/index.php');
        }
        $error = 'Email atau kata sandi tidak sesuai.';
    }
}
$pageTitle = 'Masuk Admin';
require __DIR__ . '/includes/header.php';
?>
<section class="login-section"><div class="login-visual"><div class="login-visual-content"><span class="eyebrow eyebrow-light">RUANG PENGELOLA</span><h1>Rawat niat baik.<br>Kelola dampak.</h1><p>Kelola kegiatan, tinjau pendaftaran relawan, dan bantu setiap inisiatif berjalan dengan baik.</p><div class="login-visual-bottom"><span>✳</span><span>AKSIBERSAMA · ADMIN PORTAL</span></div></div></div>
<div class="login-form-side"><div class="login-form-box"><a class="back-link" href="<?= e(url('index.php')) ?>">← Kembali ke situs</a><span class="eyebrow">SELAMAT DATANG KEMBALI</span><h2>Masuk ke dashboard.</h2><p class="login-subtitle">Gunakan akun admin untuk mengelola kegiatan.</p>
<?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="form-stack"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Email admin<input type="email" name="email" value="<?= e($email) ?>" placeholder="admin@aksibersama.test" required autocomplete="username"></label><label>Kata sandi<input type="password" name="password" placeholder="Masukkan kata sandi" required autocomplete="current-password"></label><button type="submit" class="btn btn-primary btn-block">Masuk dashboard <span>↗</span></button></form><div class="login-security"><span>◈</span><p>Area khusus pengelola. Jangan bagikan kredensial akun kepada orang lain.</p></div></div></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
