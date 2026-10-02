<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/functions.php';

if (!is_logged_in()) redirect('../login/login.php');

$id_user    = $_SESSION['id_user'];
$role       = $_SESSION['role'];
$id_trans   = (int) ($_GET['id_transaksi'] ?? 0);
$gedung_url = $_GET['gedung'] ?? 'Gedung A';

if ($id_trans <= 0) {
    set_flash('error', 'ID transaksi tidak valid.');
    redirect('dashboard.php?gedung=' . urlencode($gedung_url));
}

$tgl_ambil = date('Y-m-d H:i:s');

if ($role === 'admin') {
    $stmt = mysqli_prepare($koneksi, "UPDATE transaksi_penitipan SET status = 'Sudah Diambil', tgl_ambil = ? WHERE id_transaksi = ?");
    mysqli_stmt_bind_param($stmt, "si", $tgl_ambil, $id_trans);
} else {
    $stmt = mysqli_prepare($koneksi, "UPDATE transaksi_penitipan SET status = 'Sudah Diambil', tgl_ambil = ? WHERE id_transaksi = ? AND id_user = ?");
    mysqli_stmt_bind_param($stmt, "sii", $tgl_ambil, $id_trans, $id_user);
}

if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    set_flash('success', 'Barang berhasil diambil. Terima kasih!');
} else {
    mysqli_stmt_close($stmt);
    set_flash('error', 'Gagal mengambil barang atau barang tidak ditemukan.');
}

redirect('dashboard.php?gedung=' . urlencode($gedung_url));
?>