<?php
require_once __DIR__ . '/auth_admin.php';
header('Content-Type: application/json; charset=utf-8');
$q=trim($_GET['q']??'');$status=trim($_GET['status']??'');
$sql="SELECT b.id_barang,b.id_user,b.nama_barang,b.no_loker,b.tgl_simpan,b.status,COALESCE(u.nama_lengkap,'Tidak diketahui') pemilik FROM barang b LEFT JOIN users u ON u.id_user=b.id_user WHERE 1=1";
$params=[];$types='';
if($q!==''){ $sql.=" AND (b.nama_barang LIKE ? OR u.nama_lengkap LIKE ? OR CAST(b.no_loker AS CHAR) LIKE ?)";$like="%$q%";$params=[$like,$like,$like];$types='sss';}
if(in_array($status,['Tersimpan','Diambil'],true)){ $sql.=" AND b.status=?";$params[]=$status;$types.='s';}
$sql.=" ORDER BY b.id_barang DESC";
$stmt=$koneksi->prepare($sql);if($types!=='')$stmt->bind_param($types,...$params);$stmt->execute();$res=$stmt->get_result();$data=[];
while($r=$res->fetch_assoc())$data[]=$r;
echo json_encode(['success'=>true,'count'=>count($data),'data'=>$data],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
?>