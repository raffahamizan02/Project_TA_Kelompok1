<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: ../Login & Dashboard Siswa/login.php');
    exit();
}
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    die('Akses ditolak. Halaman ini hanya untuk admin.');
}
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token']=bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
    $token=$_POST['csrf_token']??'';
    if (!$token || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'],$token)) {
        http_response_code(419); die('Token keamanan tidak valid.');
    }
}
?>