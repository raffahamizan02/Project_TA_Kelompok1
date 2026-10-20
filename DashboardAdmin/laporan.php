<?php
require_once "koneksi.php";

$halaman_aktif = 'laporan';
$nama_admin    = $_SESSION['nama_lengkap'] ?? 'Admin TU';

$filter_aksi    = trim($_GET['aksi'] ?? '');
$filter_tanggal = trim($_GET['tanggal'] ?? '');

$daftar_log = ambilLogAktivitas($koneksi, $mock_log, $filter_aksi, $filter_tanggal);

if (($_GET['export'] ?? '') === 'csv') {
    $nama_file = 'laporan_aktivitas_' . date('Ymd') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $nama_file . '"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID Log', 'Admin', 'Aksi', 'Barang / Objek', 'Keterangan', 'Waktu']);

    $no = 1;
    foreach ($daftar_log as $row) {
        fputcsv($output, [
            $row['id_log'] ?? $no++,
            $row['nama_admin'] ?? 'Admin TU',
            $row['aksi'],
            $row['barang'],
            $row['keterangan'],
            $row['waktu']
        ]);
    }

    fclose($output);
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Aktivitas - LokerKu</title>
    <link rel="stylesheet" href="styleAdmin.css">
</head>
<body>

<div class="layout">
    <?php include "sidebar.php"; ?>

    <main class="content">
        <div class="topbar">
            <div>
                <p class="welcome">Panel Tata Usaha</p>
                <h1>Laporan Log Aktivitas</h1>
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
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 14px;">
                <form method="GET" action="laporan.php" class="filter-bar" style="margin-bottom: 0;">
                    <select name="aksi" class="form-control">
                        <option value="">Semua Aksi</option>
                        <option value="Tambah" <?= ($filter_aksi === 'Tambah') ? 'selected' : '' ?>>Tambah</option>
                        <option value="Ubah" <?= ($filter_aksi === 'Ubah') ? 'selected' : '' ?>>Ubah</option>
                        <option value="Hapus" <?= ($filter_aksi === 'Hapus') ? 'selected' : '' ?>>Hapus</option>
                    </select>

                    <input type="date" name="tanggal" class="form-control" value="<?= $filter_tanggal ?>">

                    <button type="submit" class="btn btn-primary">Filter</button>
                    <?php if ($filter_aksi !== '' || $filter_tanggal !== ''): ?>
                        <a href="laporan.php" class="btn btn-secondary">Reset</a>
                    <?php endif; ?>
                </form>

                <a href="laporan.php?export=csv&aksi=<?= $filter_aksi ?>&tanggal=<?= $filter_tanggal ?>" class="btn btn-secondary">
                    Ekspor CSV
                </a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID Log</th>
                            <th>Admin</th>
                            <th>Aksi</th>
                            <th>Barang / Objek</th>
                            <th>Keterangan</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($daftar_log)): ?>
                            <?php foreach ($daftar_log as $lg): ?>
                                <?php 
                                    $badge_aksi = 'badge-terisi';
                                    if ($lg['aksi'] === 'Tambah') $badge_aksi = 'badge-tersimpan';
                                    elseif ($lg['aksi'] === 'Hapus') $badge_aksi = 'badge-penuh';
                                ?>
                                <tr>
                                    <td><span class="badge badge-kode">#<?= $lg['id_log'] ?></span></td>
                                    <td><strong><?= $lg['nama_admin'] ?? 'Admin TU' ?></strong></td>
                                    <td><span class="badge <?= $badge_aksi ?>"><?= $lg['aksi'] ?></span></td>
                                    <td><strong><?= $lg['barang'] ?></strong></td>
                                    <td style="color: #6E6A63;"><?= $lg['keterangan'] ?></td>
                                    <td style="color: #9E988E; font-size: 12px; white-space: nowrap;"><?= $lg['waktu'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #9E988E; padding: 25px;">
                                    Tidak ada catatan log aktivitas.
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