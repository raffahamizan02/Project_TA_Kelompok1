<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/functions.php';

// Pastikan request yang masuk adalah metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

// Ambil data dari form
$username = clean_input($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$role     = clean_input($_POST['role'] ?? 'siswa');

// Validasi role hanya boleh 'siswa' atau 'admin'
if (!in_array($role, ['siswa', 'admin'])) {
    $role = 'siswa';
}

// Simpan username untuk repopulate form kalau error
$_SESSION['form_username'] = $username;

// Tentukan halaman login untuk redirect kalau error
$login_page = ($role === 'admin') ? 'admin_login.php' : 'login.php';

if (empty($username) || empty($password)) {
    set_flash('error', 'Username dan Password wajib diisi!');
    redirect($login_page);
}

// Cek user berdasarkan username & role
$stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE username = ? AND role = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ss", $username, $role);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Jika user tidak ditemukan
if (!$user) {
    set_flash('error', "Akun " . ucfirst($role) . " dengan username tersebut tidak ditemukan!");
    redirect($login_page);
}

// Verifikasi password (mendukung hash, md5, atau plain text)
$valid = password_verify($password, $user['password'])
      || md5($password) === $user['password']
      || $password === $user['password'];

// Jika password salah
if (!$valid) {
    set_flash('error', 'Password yang Anda masukkan salah!');
    redirect($login_page);
}

// ==========================================
// JIKA LOGIN BERHASIL, SET SESSION
// ==========================================
$_SESSION['id_user']      = $user['id_user'];
$_SESSION['username']     = $user['username'];
$_SESSION['nama_lengkap'] = $user['nama_lengkap'];
$_SESSION['role']         = $user['role'];

// Hapus form_username setelah sukses login
unset($_SESSION['form_username']);

set_flash('success', 'Selamat datang, ' . $user['nama_lengkap'] . '!');

// ==========================================
// REDIRECT SESUAI ROLE
// ==========================================
if ($user['role'] === 'admin') {
    // PERHATIKAN BARIS INI: Diarahkan ke beranda.php
    redirect('../DashboardAdmin/beranda.php');
} else {
    // Siswa diarahkan ke dashboard siswa
    redirect('../dashboardSiswa/dashboard.php');
}
?>