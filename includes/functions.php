<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function redirect(string $path): never {
    header('Location: ' . url($path));
    exit;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        exit('Sesi formulir tidak valid. Silakan kembali dan coba lagi.');
    }
}

function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array {
    if (empty($_SESSION['flash'])) return null;
    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $message;
}

function is_admin(): bool {
    return isset($_SESSION['admin_id']) && is_numeric($_SESSION['admin_id']);
}

function require_admin(): void {
    if (!is_admin()) {
        flash('error', 'Silakan masuk sebagai admin terlebih dahulu.');
        redirect('login.php');
    }
}

function format_date(?string $date): string {
    if (!$date) return '-';
    $months = [1=>'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $time = strtotime($date);
    return date('j', $time) . ' ' . $months[(int)date('n', $time)] . ' ' . date('Y', $time);
}

function excerpt(string $text, int $length = 135): string {
    $text = trim(strip_tags($text));
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length - 1) . '…';
}
