<?php

include "connectBarang.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    
    $query = mysqli_query($koneksi, "SELECT foto_barang, id_loker FROM barang WHERE id_barang = '$id' LIMIT 1");
    $barang = mysqli_fetch_assoc($query);

    if ($barang) {
        
        if (!empty($barang['foto_barang'])) {
            $path_foto = __DIR__ . '/uploads/' . $barang['foto_barang'];
            if (file_exists($path_foto)) {
                unlink($path_foto);
            }
        }

        $id_loker = $barang['id_loker'];

        
        mysqli_query($koneksi, "DELETE FROM barang WHERE id_barang = '$id'");

        
        if (!empty($id_loker)) {
            $qCek = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM barang WHERE id_loker = '$id_loker' AND status = 'Tersimpan'");
            $sisa = mysqli_fetch_assoc($qCek)['total'] ?? 0;
            if ($sisa == 0) {
                mysqli_query($koneksi, "UPDATE loker SET status = 'Kosong' WHERE id_loker = '$id_loker'");
            }
        }
    }
}

header("Location: indexBarang.php?pesan=sukses_hapus");
exit();
?>
