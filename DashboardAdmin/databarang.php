<?php
require_once "koneksi.php";

$halaman_aktif = 'databarang';
$nama_admin    = $_SESSION['nama_lengkap'] ?? 'Admin TU';

$cari   = trim($_GET['cari'] ?? '');
$status = trim($_GET['status'] ?? '');

$daftar_barang = ambilDaftarBarang($koneksi, $mock_barang, $cari, $status);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang - LokerKu</title>
    <link rel="stylesheet" href="styleAdmin.css">
</head>
<body>

<div class="layout">
    <?php include "sidebar.php"; ?>

    <main class="content">
        <div class="topbar">
            <div>
                <p class="welcome">Panel Tata Usaha</p>
                <h1>Data Barang Titipan</h1>
            </div>
            <div class="profile">
                <div class="profile-icon">TU</div>
                <div>
                    <strong><?= $nama_admin ?></strong>
                    <small>Tata Usaha</small>
                </div>
            </div>
        </div>

        <div class="card">
            <!-- Filter & Aksi Cepat -->
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 14px;">
                <form method="GET" action="databarang.php" class="filter-bar" style="margin-bottom: 0;">
                    <input type="text" name="cari" class="form-control" placeholder="Cari barang, kode, siswa..." value="<?= htmlspecialchars($cari) ?>" style="min-width: 220px;">

                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="Tersimpan" <?= ($status === 'Tersimpan') ? 'selected' : '' ?>>Tersimpan</option>
                        <option value="Sudah diambil" <?= ($status === 'Sudah diambil') ? 'selected' : '' ?>>Sudah diambil</option>
                    </select>

                    <button type="submit" class="btn btn-primary">Filter</button>
                    <?php if ($cari !== '' || $status !== ''): ?>
                        <a href="databarang.php" class="btn btn-secondary">Reset</a>
                    <?php endif; ?>
                </form>

                <div style="display: flex; gap: 8px;">
                    <a href="../DataBarang/createBarang.php" class="btn btn-primary">+ Tambah Barang</a>
                    <a href="../DataBarang/indexBarang.php" class="btn btn-secondary">Halaman Penuh</a>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 35px; text-align: center;">No</th>
                            <th style="width: 45px;">Foto</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Nama Siswa</th>
                            <th>Loker</th>
                            <th>Waktu Penitipan</th>
                            <th>Status</th>
                            <th style="text-align: center; width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($daftar_barang)): ?>
                            <?php $no = 1; foreach ($daftar_barang as $b): ?>
                                <?php
                                    $foto = $b['foto_barang'] ?? '';
                                    $path_foto = '';
                                    if ($foto !== '') {
                                        if (file_exists(__DIR__ . "/../DataBarang/uploads/$foto")) $path_foto = "../DataBarang/uploads/$foto";
                                        elseif (file_exists(__DIR__ . "/uploads/$foto")) $path_foto = "uploads/$foto";
                                        elseif (file_exists(__DIR__ . "/../uploads/$foto")) $path_foto = "../uploads/$foto";
                                    }
                                ?>
                                <tr>
                                    <td style="text-align: center; color: #9E988E;"><?= $no++ ?></td>
                                    <td>
                                        <div class="thumb-foto">
                                            <?php if ($path_foto !== ''): ?>
                                                <img src="<?= $path_foto ?>" alt="Foto">
                                            <?php else: ?>
                                                <span>-</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-kode"><?= htmlspecialchars($b['kode_barang']) ?></span></td>
                                    <td><strong><?= htmlspecialchars($b['nama_barang']) ?></strong></td>
                                    <td>
                                        <?= htmlspecialchars($b['nama_siswa']) ?>
                                        <?php if (!empty($b['nis_siswa'])): ?>
                                            <div style="font-size: 11px; color: #9E988E;"><?= htmlspecialchars($b['nis_siswa']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge badge-terisi"><?= htmlspecialchars($b['nomor_loker']) ?></span></td>
                                    <td style="color: #6E6A63; font-size: 12px;"><?= htmlspecialchars($b['waktu_penitipan']) ?></td>
                                    <td>
                                        <?php if ($b['status'] === 'Tersimpan'): ?>
                                            <span class="badge badge-tersimpan">Tersimpan</span>
                                        <?php else: ?>
                                            <span class="badge badge-diambil">Sudah diambil</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center; white-space: nowrap;">
                                        <a href="../DataBarang/verificationBarang.php?id=<?= $b['id_barang'] ?>" class="btn btn-sm btn-primary">Verifikasi</a>
                                        <a href="../DataBarang/updateBarang.php?id=<?= $b['id_barang'] ?>" class="btn btn-sm btn-secondary">Edit</a>
                                        <a href="../DataBarang/deleteBarang.php?id=<?= $b['id_barang'] ?>" class="btn btn-sm" style="background: #F9E2DF; color: #B8473E;" onclick="return confirm('Hapus barang ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align: center; color: #9E988E; padding: 25px;">
                                    Data barang tidak ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>