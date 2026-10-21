<?php

session_start();
include "koneksi.php";

if (isset($_POST['simpan'])) {

    $nomor_loker = mysqli_real_escape_string(
        $koneksi,
        $_POST['nomor_loker']
    );

    $lokasi = mysqli_real_escape_string(
        $koneksi,
        $_POST['id_gedung']
    );

    // Validasi gedung (hanya boleh Gedung A - D)
    $gedung_valid = ['Gedung A', 'Gedung B', 'Gedung C', 'Gedung D'];

    if (!in_array($_POST['id_gedung'], $gedung_valid, true)) {

        header("Location: loker.php");
        exit();

    }


    // CEK NOMOR LOKER
    $cek = mysqli_query(
        $koneksi,
        "SELECT * FROM loker
         WHERE nomor_loker='$nomor_loker'"
    );


    if (mysqli_num_rows($cek) > 0) {

        echo "<script>
                alert('Kode Loker sudah digunakan!');
                window.location.href='loker.php';
              </script>";

        exit();

    }


    // LOKER BARU SELALU KOSONG
    $status = "Kosong";


    // SIMPAN DATA
    $simpan = mysqli_query(
        $koneksi,
        "INSERT INTO loker
        (nomor_loker, id_gedung, status)
        VALUES
        ('$nomor_loker', '$lokasi', '$status')"
    );


    if ($simpan) {

        header("Location: loker.php?pesan=tambah");
        exit();

    } else {

        echo "Gagal menyimpan data: "
            . mysqli_error($koneksi);

    }

} else {

    // Jika tambah_loker.php dibuka langsung
    header("Location: loker.php");
    exit();

}

?>