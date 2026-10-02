<?php
session_start();

$host     = "localhost";
$username = "root";
$password = "";
$database = "db_barang";
$port     = 3307;

$koneksi = mysqli_connect($host, $username, $password, $database, $port);

if (!$koneksi) {
    die("Koneksi ke database gagal");
} else {
    echo("Koneksi Berhasil");
}

if (!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("location: login/login.php?p=Silakan login terlebih dahulu!");
    exit();
}

$role = $_SESSION['role'];
?>