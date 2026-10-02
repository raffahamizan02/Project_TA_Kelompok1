<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

$nama_lengkap = clean_input($_POST['reg_nama'] ?? '');
$username     = clean_input($_POST['reg_username'] ?? '');
$password     = trim($_POST['reg_password'] ?? '');

if (empty($nama_lengkap) || empty($username) || empty($password)) {
    set_flash('error', 'Semua field pendaftaran wajib diisi!');
    redirect('login.php');
}

if (strlen($password) < 6) {
    set_flash('error', 'Password minimal 6 karakter!');
    redirect('login.php');
}

$stmt = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE username = ? AND role = 'siswa' LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    set_flash('error', "Username / NISN '$username' sudah terdaftar!");
    redirect('login.php');
}
mysqli_stmt_close($stmt);

$pass_hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = mysqli_prepare($koneksi, "INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, 'siswa')");
mysqli_stmt_bind_param($stmt, "sss", $username, $pass_hash, $nama_lengkap);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    set_flash('success', 'Registrasi berhasil! Silakan login.');
    $_SESSION['form_username'] = $username;
} else {
    mysqli_stmt_close($stmt);
    set_flash('error', 'Gagal mendaftar: ' . mysqli_error($koneksi));
}

redirect('login.php');
?>