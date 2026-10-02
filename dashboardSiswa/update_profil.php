<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/functions.php';

if (!is_logged_in()) redirect('../login/login.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('dashboard.php');

$id_user    = $_SESSION['id_user'];
$nama_baru  = clean_input($_POST['nama_lengkap'] ?? '');
$email_baru = clean_input($_POST['email'] ?? '');
$no_hp_baru = clean_input($_POST['no_hp'] ?? '');
$pass_baru  = trim($_POST['password_baru'] ?? '');
$gedung_url = $_POST['gedung_asal'] ?? 'Gedung A';

if (empty($nama_baru)) {
    set_flash('error', 'Nama tidak boleh kosong!');
    redirect('dashboard.php?gedung=' . urlencode($gedung_url));
}

$stmt = mysqli_prepare($koneksi, "SELECT foto FROM users WHERE id_user = ?");
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$old = mysqli_fetch_assoc($res);
$foto_lama = $old['foto'] ?? '';
mysqli_stmt_close($stmt);

$nama_foto = $foto_lama;
if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($ext, $allowed)) {
        set_flash('error', 'Format foto harus JPG, PNG, GIF, atau WEBP!');
        redirect('dashboard.php?gedung=' . urlencode($gedung_url));
    }
    
    if ($_FILES['foto_profil']['size'] > 2 * 1024 * 1024) {
        set_flash('error', 'Ukuran foto maksimal 2MB!');
        redirect('dashboard.php?gedung=' . urlencode($gedung_url));
    }
    
    $upload_dir = __DIR__ . '/uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $nama_foto = 'profile_' . $id_user . '_' . time() . '.' . $ext;
    $target = $upload_dir . $nama_foto;
    
    if (!move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target)) {
        set_flash('error', 'Gagal upload foto.');
        redirect('dashboard.php?gedung=' . urlencode($gedung_url));
    }
    
    if (!empty($foto_lama) && file_exists($upload_dir . $foto_lama)) {
        unlink($upload_dir . $foto_lama);
    }
}

if (!empty($pass_baru)) {
    $hash = password_hash($pass_baru, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($koneksi, "UPDATE users SET nama_lengkap=?, email=?, no_hp=?, password=?, foto=? WHERE id_user=?");
    mysqli_stmt_bind_param($stmt, "sssssi", $nama_baru, $email_baru, $no_hp_baru, $hash, $nama_foto, $id_user);
} else {
    $stmt = mysqli_prepare($koneksi, "UPDATE users SET nama_lengkap=?, email=?, no_hp=?, foto=? WHERE id_user=?");
    mysqli_stmt_bind_param($stmt, "ssssi", $nama_baru, $email_baru, $no_hp_baru, $nama_foto, $id_user);
}

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    $_SESSION['nama_lengkap'] = $nama_baru;
    set_flash('success', 'Profil berhasil diperbarui!');
} else {
    mysqli_stmt_close($stmt);
    set_flash('error', 'Gagal update profil: ' . mysqli_error($koneksi));
}

redirect('dashboard.php?gedung=' . urlencode($gedung_url));
?>