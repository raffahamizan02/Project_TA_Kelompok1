<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/functions.php';

if (!is_logged_in()) redirect('../login/login.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('dashboard.php');

$id_user      = $_SESSION['id_user'];
$gedung_input = clean_input($_POST['gedung'] ?? '');
$nama_barang  = clean_input($_POST['nama_barang'] ?? '');
$no_loker_in  = (int) ($_POST['no_loker'] ?? 0);
$keterangan   = clean_input($_POST['keterangan'] ?? '');
$redirect_url = 'dashboard.php?gedung=' . urlencode($gedung_input);

if (empty($nama_barang) || empty($gedung_input) || $no_loker_in < 1 || $no_loker_in > 150) {
    set_flash('error', 'Data tidak lengkap atau nomor loker tidak valid!');
    redirect($redirect_url);
}

$str_loker = format_no_loker($gedung_input, $no_loker_in);

$stmt = mysqli_prepare($koneksi, "SELECT id_gedung FROM gedung WHERE nama_gedung = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $gedung_input);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
if ($row = mysqli_fetch_assoc($res)) {
    $id_gedung = $row['id_gedung'];
} else {
    $stmt2 = mysqli_prepare($koneksi, "INSERT INTO gedung (nama_gedung) VALUES (?)");
    mysqli_stmt_bind_param($stmt2, "s", $gedung_input);
    mysqli_stmt_execute($stmt2);
    $id_gedung = mysqli_insert_id($koneksi);
    mysqli_stmt_close($stmt2);
}
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($koneksi, "SELECT id_loker FROM loker WHERE id_gedung = ? AND no_loker = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "is", $id_gedung, $str_loker);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
if ($row = mysqli_fetch_assoc($res)) {
    $id_loker = $row['id_loker'];
} else {
    $stmt2 = mysqli_prepare($koneksi, "INSERT INTO loker (id_gedung, no_loker) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt2, "is", $id_gedung, $str_loker);
    mysqli_stmt_execute($stmt2);
    $id_loker = mysqli_insert_id($koneksi);
    mysqli_stmt_close($stmt2);
}
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($koneksi, "SELECT id_transaksi FROM transaksi_penitipan WHERE id_loker = ? AND status = 'Tersimpan' LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id_loker);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    set_flash('error', "Loker <strong>$str_loker</strong> sedang terpakai! Pilih nomor lain.");
    redirect($redirect_url);
}
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($koneksi, "INSERT INTO barang (id_user, nama_barang, keterangan) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, "iss", $id_user, $nama_barang, $keterangan);
mysqli_stmt_execute($stmt);
$id_barang = mysqli_insert_id($koneksi);
mysqli_stmt_close($stmt);

$tgl_simpan = date('Y-m-d H:i:s');
$status     = 'Tersimpan';
$stmt = mysqli_prepare($koneksi, "INSERT INTO transaksi_penitipan (id_user, id_barang, id_loker, tgl_simpan, status) VALUES (?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "iiiss", $id_user, $id_barang, $id_loker, $tgl_simpan, $status);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    set_flash('success', "Barang <strong>$nama_barang</strong> berhasil disimpan di loker <strong>$str_loker</strong>.");
} else {
    mysqli_stmt_close($stmt);
    set_flash('error', 'Gagal menyimpan transaksi: ' . mysqli_error($koneksi));
}

redirect($redirect_url);
?>