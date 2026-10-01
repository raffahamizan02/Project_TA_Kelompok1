<?php
require_once __DIR__ . '/auth_admin.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: ../DashboardAdmin/databarang.php');exit();}
verify_csrf();$id=(int)($_POST['id_barang']??0);
$stmt=$koneksi->prepare("DELETE FROM barang WHERE id_barang=?");$stmt->bind_param('i',$id);
if($stmt->execute()&&$stmt->affected_rows>0)header('Location: ../DashboardAdmin/databarang.php?success=Barang berhasil dihapus');
else header('Location: ../DashboardAdmin/databarang.php?error=Barang tidak ditemukan atau gagal dihapus');
exit(); ?>