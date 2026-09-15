<?php
$host   = "localhost";
$user   = "root";
$pass   = "";
$db     = "db_barang";
$port   = 3307;

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if ($conn) {
    echo "Koneksi Database Berhasil";
} else {
    echo "Koneksi Database Gagal" . mysqli_connect_error();
}

?>