<?php

$host     = "localhost";
$username = "app_kel1";
$password = "D@t@6@53K3l1";
$database = "db_kel1";

$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>