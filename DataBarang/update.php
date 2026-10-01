<?php
require_once __DIR__ . '/auth_admin.php';
$id=(int)($_GET['id']??$_POST['id_barang']??0);
if($id<=0){header('Location: ../DashboardAdmin/databarang.php?error=ID barang tidak valid');exit();}
$stmt=$koneksi->prepare("SELECT b.*,COALESCE(u.nama_lengkap,'Tidak diketahui') pemilik FROM barang b LEFT JOIN users u ON u.id_user=b.id_user WHERE b.id_barang=?");
$stmt->bind_param('i',$id);$stmt->execute();$row=$stmt->get_result()->fetch_assoc();
if(!$row){header('Location: ../DashboardAdmin/databarang.php?error=Data barang tidak ditemukan');exit();}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf(); $nama=trim($_POST['nama_barang']??''); $loker=(int)($_POST['no_loker']??0); $status=$_POST['status']??'';
 if($nama==='')$error='Nama barang wajib diisi.';
 elseif($loker<1||$loker>24)$error='Nomor loker harus 1 sampai 24.';
 elseif(!in_array($status,['Tersimpan','Diambil'],true))$error='Status tidak valid.';
 elseif($status==='Tersimpan'){
   $cek=$koneksi->prepare("SELECT id_barang FROM barang WHERE no_loker=? AND status='Tersimpan' AND id_barang<>? LIMIT 1");
   $cek->bind_param('ii',$loker,$id);$cek->execute();$cek->store_result();
   if($cek->num_rows)$error='Loker tersebut sedang terisi oleh barang lain.';$cek->close();
 }
 if($error===''){
   $up=$koneksi->prepare("UPDATE barang SET nama_barang=?,no_loker=?,status=? WHERE id_barang=?");
   $up->bind_param('sisi',$nama,$loker,$status,$id);
   if($up->execute()){header('Location: ../DashboardAdmin/databarang.php?success=Data barang berhasil diperbarui');exit();}
   $error='Data barang gagal diperbarui.';
 }
 $row['nama_barang']=$nama;$row['no_loker']=$loker;$row['status']=$status;
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Barang - LOKIFY</title><link rel="stylesheet" href="../DashboardAdmin/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body class="standalone-page"><main class="form-card">
<div class="form-card-header"><div><span class="eyebrow">DATA BARANG</span><h1>Edit Barang #<?=e($row['id_barang'])?></h1><p>Pemilik: <b><?=e($row['pemilik'])?></b></p></div><a class="btn btn-light" href="../DashboardAdmin/databarang.php"><i class="fa-solid fa-arrow-left"></i> Kembali</a></div>
<?php if($error): ?><div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?=e($error)?></div><?php endif; ?>
<form method="post" class="form-grid"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id_barang" value="<?=e($row['id_barang'])?>">
<div class="form-field field-full"><label>Nama Barang</label><input name="nama_barang" maxlength="100" value="<?=e($row['nama_barang'])?>" required></div>
<div class="form-field"><label>Nomor Loker</label><input name="no_loker" type="number" min="1" max="24" value="<?=e($row['no_loker'])?>" required></div>
<div class="form-field"><label>Status</label><select name="status"><option value="Tersimpan" <?= $row['status']==='Tersimpan'?'selected':'' ?>>Tersimpan</option><option value="Diambil" <?= $row['status']==='Diambil'?'selected':'' ?>>Diambil</option></select></div>
<div class="form-field field-full"><label>Waktu Simpan</label><div class="readonly-field"><i class="fa-solid fa-clock"></i> <?=e(date('d/m/Y H:i',strtotime($row['tgl_simpan'])))?></div></div>
<div class="form-actions field-full"><a class="btn btn-light" href="../DashboardAdmin/databarang.php">Batal</a><button class="btn btn-primary" type="submit"><i class="fa-solid fa-save"></i> Simpan Perubahan</button></div>
</form></main></body></html>