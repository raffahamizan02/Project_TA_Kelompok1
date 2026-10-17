<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include "connectBarang.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$query = "SELECT b.*, 
                 u.nama_lengkap AS nama_siswa, 
                 u.username AS nis_siswa, 
                 l.nomor_loker, 
                 l.lokasi
          FROM barang b
          LEFT JOIN users u ON b.id_siswa = u.id_user
          LEFT JOIN loker l ON b.id_loker = l.id_loker
          WHERE b.id_barang = '$id' LIMIT 1";

$hasil = mysqli_query($koneksi, $query);
$barang = mysqli_fetch_assoc($hasil);

if (!$barang) {
    header("Location: indexBarang.php");
    exit();
}

$path_foto = 'uploads/' . $barang['foto_barang'];
$ada_foto = (!empty($barang['foto_barang']) && file_exists(__DIR__ . '/' . $path_foto));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Barang - <?php echo htmlspecialchars($barang['kode_barang']); ?></title>
    <link rel="stylesheet" href="styleBarang.css?v=<?php echo time(); ?>">
    <style>
        <?php include __DIR__ . '/styleBarang.css'; ?>
    </style>
</head>
<body>

<div class="container">

    <div class="header-box">
        <div>
            <h2>Detail Barang</h2>
            <p>Informasi lengkap data barang titipan</p>
        </div>
        <a href="indexBarang.php" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card">
        <div class="detail-box">

            
            <div class="detail-foto">
                <?php if ($ada_foto): ?>
                    <img src="<?php echo $path_foto; ?>" alt="Foto Barang">
                <?php else: ?>
                    <p style="text-align: center; padding: 40px 10px; color: #9E988E;">Tidak ada foto</p>
                <?php endif; ?>
            </div>

            
            <div class="detail-info">
                <table>
                    <tr>
                        <th style="width: 150px;">Kode Barang</th>
                        <td><span class="badge-kode"><?php echo htmlspecialchars($barang['kode_barang']); ?></span></td>
                    </tr>
                    <tr>
                        <th>Nama Barang</th>
                        <td><strong><?php echo htmlspecialchars($barang['nama_barang']); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Siswa Pemilik</th>
                        <td><?php echo htmlspecialchars($barang['nama_siswa'] ?? '-'); ?> (NIS: <?php echo htmlspecialchars($barang['nis_siswa'] ?? '-'); ?>)</td>
                    </tr>
                    <tr>
                        <th>Loker</th>
                        <td>Loker <?php echo htmlspecialchars($barang['nomor_loker'] ?? '-'); ?> - <?php echo htmlspecialchars($barang['lokasi'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php if ($barang['status'] == 'Tersimpan'): ?>
                                <span class="badge badge-tersimpan">Tersimpan</span>
                            <?php else: ?>
                                <span class="badge badge-diambil">Sudah diambil</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Waktu Penitipan</th>
                        <td><?php echo date('d/m/Y H:i', strtotime($barang['waktu_penitipan'])); ?> WIB</td>
                    </tr>
                    <tr>
                        <th>Waktu Pengambilan</th>
                        <td>
                            <?php 
                            if (!empty($barang['waktu_pengambilan'])) {
                                echo date('d/m/Y H:i', strtotime($barang['waktu_pengambilan'])) . " WIB";
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>
                    </tr>
                </table>

                <div class="form-actions" style="margin-top: 15px;">
                    <?php if ($barang['status'] == 'Tersimpan'): ?>
                        <a href="verificationBarang.php?id=<?php echo $barang['id_barang']; ?>" class="btn btn-primary">Verifikasi & Ambil</a>
                    <?php endif; ?>
                    <a href="updateBarang.php?id=<?php echo $barang['id_barang']; ?>" class="btn btn-warning">Edit</a>
                    <a href="deleteBarang.php?id=<?php echo $barang['id_barang']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                </div>
            </div>

        </div>
    </div>

</div>

</body>
</html>
