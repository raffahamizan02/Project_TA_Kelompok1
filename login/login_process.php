<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

$username = clean_input($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$role     = clean_input($_POST['role'] ?? 'siswa');

$_SESSION['form_role']     = $role;
$_SESSION['form_username'] = $username;

if (empty($username) || empty($password)) {
    set_flash('error', 'Username dan Password wajib diisi!');
    redirect('login.php');
}

$stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE username = ? AND role = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ss", $username, $role);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    set_flash('error', "Akun " . ucfirst($role) . " dengan username tersebut tidak ditemukan!");
    redirect('login.php');
}

$valid = password_verify($password, $user['password'])
      || md5($password) === $user['password']
      || $password === $user['password'];

if (!$valid) {
    set_flash('error', 'Password yang Anda masukkan salah!');
    redirect('login.php');
}

$_SESSION['id_user']      = $user['id_user'];
$_SESSION['username']     = $user['username'];
$_SESSION['nama_lengkap'] = $user['nama_lengkap'];
$_SESSION['role']         = $user['role'];

unset($_SESSION['form_role'], $_SESSION['form_username']);

set_flash('success', 'Selamat datang, ' . $user['nama_lengkap'] . '!');
redirect('../dashboardSiswa/dashboard.php');
?>