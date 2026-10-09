<?php 
$hostname = "localhost";
$username = "root";
$password = "";
$db = "db_kel1";

$koneksi = mysqli_connect($hostname, $username, $password);
if ($koneksi){
    $pilih_db = mysqli_select_db($koneksi, $db);
    if($pilih_db){
    }
} else {
    echo "Koneksi gagal, diperiksa lagi!";
}
?>  