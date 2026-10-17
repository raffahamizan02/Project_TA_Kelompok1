<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include "connectBarang.php";

$id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;

if ($id <= 0) {
    header("Location: indexBarang.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi_ambil'])) {
    
    $queryAmbil = "UPDATE barang SET 
                        status = 'Sudah diambil', 
                        waktu_pengambilan = NOW() 
                   WHERE id_barang = '$id'";
    
    if (mysqli_query($koneksi, $queryAmbil)) {
        $qLoker = mysqli_query($koneksi, "SELECT id_loker FROM barang WHERE id_barang = '$id'");
        $dtLoker = mysqli_fetch_assoc($qLoker);
        $id_loker = $dtLoker['id_loker'] ?? 0;
        $cekSisa = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM barang WHERE id_loker = '$id_loker' AND status = 'Tersimpan'");
        $sisa = mysqli_fetch_assoc($cekSisa)['total'] ?? 0;

        if ($sisa == 0) {
            mysqli_query($koneksi, "UPDATE loker SET status = 'Kosong' WHERE id_loker = '$id_loker'");
        }

        header("Location: indexBarang.php?pesan=sukses_ambil");
        exit();
    }
}

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
    <title>Verifikasi Barang</title>
    <link rel="stylesheet" href="styleBarang.css?v=<?php echo time(); ?>">
    <style>
        <?php include __DIR__ . '/styleBarang.css'; ?>
    </style>
</head>
<body>

<div class="container">

    <div class="header-box">
        <div>
            <h2>Verifikasi & Pengambilan Barang</h2>
            <p>Cocokkan bukti foto barang dengan kondisi fisik sebelum diserahkan ke siswa</p>
        </div>
        <a href="indexBarang.php" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card">
        <div class="detail-box">

            
            <div class="detail-foto">
                <p style="font-weight: bold; margin-bottom: 8px; font-size: 13px;">Bukti Foto Penitipan:</p>
                <?php if ($ada_foto): ?>
                    <img src="<?php echo $path_foto; ?>" alt="Bukti Foto">
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
                        <th>Waktu Penitipan</th>
                        <td><?php echo date('d/m/Y H:i', strtotime($barang['waktu_penitipan'])); ?> WIB</td>
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
                    <?php if (!empty($barang['waktu_pengambilan'])): ?>
                    <tr>
                        <th>Waktu Diambil</th>
                        <td><?php echo date('d/m/Y H:i', strtotime($barang['waktu_pengambilan'])); ?> WIB</td>
                    </tr>
                    <?php endif; ?>
                </table>

                <div style="margin-top: 20px; padding: 14px; background-color: #FAF8F5; border: 1px solid #E8E3D9; border-radius: 4px;">
                    <?php if ($barang['status'] == 'Tersimpan'): ?>
                        <p style="font-size: 13px; margin-bottom: 10px; color: #6E6A63;">
                            Konfirmasi bahwa barang fisik di loker sudah sesuai dengan foto dan telah diserahkan kepada siswa:
                        </p>
                        <form action="verificationBarang.php" method="POST" onsubmit="return confirm('Apakah Anda yakin barang ini sudah diambil oleh siswa?')">
                            <input type="hidden" name="id" value="<?php echo $barang['id_barang']; ?>">
                            <button type="submit" name="konfirmasi_ambil" value="1" class="btn btn-success">
                                Tandai Barang Sudah Diambil
                            </button>
                            <a href="indexBarang.php" class="btn btn-secondary">Batal</a>
                        </form>
                    <?php else: ?>
                        <p style="color: #1E1D1B; font-weight: bold; font-size: 13px;">
                            Barang ini sudah diambil pada <?php echo date('d/m/Y H:i', strtotime($barang['waktu_pengambilan'])); ?> WIB.
                        </p>
                        <div style="margin-top: 10px;">
                            <a href="indexBarang.php" class="btn btn-secondary">Kembali</a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </div>

</div>

</body>
</html>
