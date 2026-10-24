<?php
require_once "connectBarang.php";

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    $query = mysqli_query($koneksi, "SELECT nama_barang, kode_barang, foto_barang, id_loker FROM barang WHERE id_barang = '$id'");
    $barang = mysqli_fetch_assoc($query);

    if ($barang) {
        $foto = $barang['foto_barang'];
        $id_loker = $barang['id_loker'];
        $nama_hapus = mysqli_real_escape_string($koneksi, $barang['nama_barang'] ?? 'Barang');
        $kode_hapus = $barang['kode_barang'] ?? '';

        if (!empty($foto) && file_exists("uploads/" . $foto)) {
            unlink("uploads/" . $foto);
        }

        @mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_admin, aksi, id_barang_terkait, keterangan, waktu) 
            VALUES (1, 'Hapus', NULL, 'Menghapus barang $nama_hapus ($kode_hapus)', NOW())");

        mysqli_query($koneksi, "DELETE FROM barang WHERE id_barang = '$id'");

        if (!empty($id_loker)) {
            $cekLoker = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM barang WHERE id_loker = '$id_loker' AND status = 'Tersimpan'");
            $dataLoker = mysqli_fetch_assoc($cekLoker);

            if ($dataLoker['total'] == 0) {
                mysqli_query($koneksi, "UPDATE loker SET status = 'Kosong' WHERE id_loker = '$id_loker'");
            }
        }
    }
}

header("Location: indexBarang.php?pesan=sukses_hapus");
exit();
?>