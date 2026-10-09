<?php
session_start();
include "koneksi.php";

if (isset($_POST['edit'])) {

    // Ambil ID dari form
    $id_loker = (int) $_POST['id_loker'];

    $nomor_loker = mysqli_real_escape_string(
        $koneksi,
        $_POST['nomor_loker']
    );

    $lokasi = mysqli_real_escape_string(
        $koneksi,
        $_POST['lokasi']
    );

    // Validasi gedung (hanya boleh Gedung A - D)
    $gedung_valid = ['Gedung A', 'Gedung B', 'Gedung C', 'Gedung D'];

    if (!in_array($_POST['lokasi'], $gedung_valid, true)) {

        header("Location: index.php");
        exit();

    }

    // Cek apakah nomor loker sudah digunakan
    $cek = mysqli_query(
        $koneksi,
        "SELECT id_loker FROM loker
         WHERE nomor_loker='$nomor_loker'
         AND id_loker != $id_loker"
    );

    if (mysqli_num_rows($cek) > 0) {

        echo "<script>
                alert('Nomor Loker sudah digunakan!');
                window.location.href='index.php';
              </script>";
        exit();

    }

    // Update nomor dan lokasi saja
    // Status tidak diubah oleh admin
    $update = mysqli_query(
        $koneksi,
        "UPDATE loker SET
            nomor_loker='$nomor_loker',
            lokasi='$lokasi'
         WHERE id_loker=$id_loker"
    );

    if ($update) {

        // Kembali ke dashboard
        header("Location: index.php?pesan=edit");
        exit();

    } else {

        echo "Gagal mengubah data: " . mysqli_error($koneksi);

    }

} else {

    // Jika file dibuka langsung
    header("Location: index.php");
    exit();

}
?>