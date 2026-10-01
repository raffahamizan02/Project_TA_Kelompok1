<?php
$host="localhost";$user="root";$pass="";$db="db_ta";$port=3307;
$koneksi=mysqli_connect($host,$user,$pass,$db,$port);
if(!$koneksi){http_response_code(500);die("Koneksi database gagal: ".mysqli_connect_error());}
mysqli_set_charset($koneksi,"utf8mb4");
?>