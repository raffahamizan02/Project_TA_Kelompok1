<?php
require_once __DIR__.'/auth.php';
$dari=trim($_GET['dari']??'');$sampai=trim($_GET['sampai']??'');$status=trim($_GET['status']??'');
$sql="SELECT b.id_barang,COALESCE(u.nama_lengkap,'Tidak diketahui') pemilik,b.nama_barang,b.no_loker,b.tgl_simpan,b.status FROM barang b LEFT JOIN users u ON u.id_user=b.id_user WHERE 1=1";$params=[];$types='';
if($dari!==''&&preg_match('/^\d{4}-\d{2}-\d{2}$/',$dari)){$sql.=" AND b.tgl_simpan>=?";$params[]=$dari.' 00:00:00';$types.='s';}
if($sampai!==''&&preg_match('/^\d{4}-\d{2}-\d{2}$/',$sampai)){$sql.=" AND b.tgl_simpan<=?";$params[]=$sampai.' 23:59:59';$types.='s';}
if(in_array($status,['Tersimpan','Diambil'],true)){$sql.=" AND b.status=?";$params[]=$status;$types.='s';}$sql.=" ORDER BY b.tgl_simpan DESC,b.id_barang DESC";
$stmt=$koneksi->prepare($sql);if($types!=='')$stmt->bind_param($types,...$params);$stmt->execute();$result=$stmt->get_result();
header('Content-Type:text/csv; charset=utf-8');header('Content-Disposition:attachment; filename="laporan-barang-loker-'.date('Ymd-His').'.csv"');
$out=fopen('php://output','w');fwrite($out,"\xEF\xBB\xBF");fputcsv($out,['ID Barang','Pemilik','Nama Barang','No. Loker','Tanggal Simpan','Status']);
while($row=$result->fetch_assoc())fputcsv($out,[$row['id_barang'],$row['pemilik'],$row['nama_barang'],$row['no_loker'],$row['tgl_simpan'],$row['status']]);fclose($out);exit();
?>