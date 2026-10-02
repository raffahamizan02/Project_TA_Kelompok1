<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/functions.php';

if (!is_logged_in()) {
    redirect('../login/login.php');
}

/* SESSION TIMEOUT: 30 menit = 1800 detik */
$timeout_duration = 1800;
if (isset($_SESSION['last_activity'])) {
    $elapsed = time() - $_SESSION['last_activity'];
    if ($elapsed > $timeout_duration) {
        session_unset();
        session_destroy();
        session_start();
        set_flash('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        redirect('../login/login.php');
    }
}
$_SESSION['last_activity'] = time();

$id_user      = $_SESSION['id_user'];
$role         = $_SESSION['role'] ?? 'siswa';
$nama_lengkap = $_SESSION['nama_lengkap'] ?? 'User';

$stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE id_user = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data_user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data_user) {
    session_destroy();
    redirect('../login/login.php');
}

$nama_lengkap = $data_user['nama_lengkap'];
$email_user   = $data_user['email'] ?? '';
$no_hp_user   = $data_user['no_hp'] ?? '';
$foto_user    = $data_user['foto'] ?? '';
$inisial      = get_inisial($nama_lengkap);
?>