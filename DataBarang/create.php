<?php
require_once __DIR__ . '/auth_admin.php';
$error=''; $oldName=trim($_POST['nama_barang']??''); $oldLoker=(int)($_POST['no_loker']??0);

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    if ($oldName==='') $error='Nama barang wajib diisi.';
    elseif ($oldLoker<1 || $oldLoker>24) $error='Nomor loker harus 1 sampai 24.';
    else {
        $cek=$koneksi->prepare("SELECT id_barang FROM barang WHERE no_loker=? AND status='Tersimpan' LIMIT 1");
        $cek->bind_param('i',$oldLoker); $cek->execute(); $cek->store_result();
        if ($cek->num_rows) $error='Loker tersebut sedang terisi.';
        $cek->close();
        if ($error==='') {
            $status='Tersimpan'; $tgl=date('Y-m-d H:i:s'); $idUser=(int)$_SESSION['id_user'];
            $stmt=$koneksi->prepare("INSERT INTO barang (id_user,nama_barang,no_loker,tgl_simpan,status) VALUES (?,?,?,?,?)");
            $stmt->bind_param('isiss',$idUser,$oldName,$oldLoker,$tgl,$status);
            if ($stmt->execute()) { header('Location: ../DashboardAdmin/databarang.php?success=Barang berhasil ditambahkan'); exit(); }
            $error='Data barang gagal disimpan.';
        }
    }
}
?>
<!doctype html><html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tambah Barang - LOKIFY</title>
<link rel="stylesheet" href="../DashboardAdmin/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head><body class="standalone-page">
<main class="form-card">
<div class="form-card-header"><div><span class="eyebrow">DATA BARANG</span><h1>Tambah Barang</h1><p>Masukkan barang yang disimpan pada loker sekolah.</p></div><a class="btn btn-light" href="../DashboardAdmin/databarang.php"><i class="fa-solid fa-arrow-left"></i> Kembali</a></div>
<?php if($error): ?><div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?=e($error)?></div><?php endif; ?>
<form method="post" class="form-grid">
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<div class="form-field field-full"><label for="nama_barang">Nama Barang</label><input id="nama_barang" name="nama_barang" maxlength="100" value="<?=e($oldName)?>" placeholder="Contoh: Tas, Helm, Laptop" required></div>
<div class="form-field"><label for="no_loker">Nomor Loker</label><input id="no_loker" name="no_loker" type="number" min="1" max="24" value="<?=e($oldLoker)?>" required><small>Loker aktif harus unik.</small></div>
<div class="form-field"><label>Status Awal</label><div class="readonly-field"><i class="fa-solid fa-circle-check"></i> Tersimpan</div></div>
<div class="form-actions field-full"><a class="btn btn-light" href="../DashboardAdmin/databarang.php">Batal</a><button class="btn btn-primary" type="submit"><i class="fa-solid fa-save"></i> Simpan Barang</button></div>
</form></main></body></html>