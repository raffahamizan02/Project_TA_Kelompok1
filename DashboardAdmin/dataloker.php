<?php
require_once "koneksi.php";

$halaman_aktif = 'dataloker';
$nama_admin    = $_SESSION['nama_lengkap'] ?? 'Admin TU';

$filter_status = trim($_GET['status'] ?? '');
$semua_loker   = ambilDaftarLoker($koneksi, $mock_loker);

$loker_tampil    = [];
$total_kapasitas = 0;
$total_terisi    = 0;

foreach ($semua_loker as $l) {
    $total_kapasitas += ($l['kapasitas'] ?? 5);
    $total_terisi    += ($l['terisi'] ?? 0);

    if ($filter_status === '' || $l['status'] === $filter_status) {
        $loker_tampil[] = $l;
    }
}

$slot_tersedia = $total_kapasitas - $total_terisi;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Loker - LokerKu</title>
    <link rel="stylesheet" href="styleAdmin.css">
</head>
<body>

<div class="layout">
    <?php include "sidebar.php"; ?>

    <main class="content">
        <div class="topbar">
            <div>
                <p class="welcome">Panel Tata Usaha</p>
                <h1>Kapasitas & Status Loker</h1>
            </div>
            <div class="profile">
                <div class="profile-icon">TU</div>
                <div>
                    <strong><?= $nama_admin ?></strong>
                    <small>Tata Usaha</small>
                </div>
            </div>
        </div>

        <div class="statistics">
            <div class="stat-card">
                <div class="stat-title">Total Unit</div>
                <div class="stat-value"><?= count($semua_loker) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Kapasitas Total</div>
                <div class="stat-value"><?= $total_kapasitas ?></div>
            </div>
            <div class="stat-card highlight">
                <div class="stat-title">Slot Terisi</div>
                <div class="stat-value"><?= $total_terisi ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Slot Tersedia</div>
                <div class="stat-value"><?= $slot_tersedia ?></div>
            </div>
        </div>

        <div class="card">
            <form method="GET" action="dataloker.php" class="filter-bar">
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Status Loker</option>
                    <option value="Kosong" <?= ($filter_status === 'Kosong') ? 'selected' : '' ?>>Kosong</option>
                    <option value="Terisi" <?= ($filter_status === 'Terisi') ? 'selected' : '' ?>>Terisi</option>
                    <option value="Menunggu Verifikasi" <?= ($filter_status === 'Menunggu Verifikasi') ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                    <option value="Penuh" <?= ($filter_status === 'Penuh') ? 'selected' : '' ?>>Penuh</option>
                </select>

                <?php if ($filter_status !== ''): ?>
                    <a href="dataloker.php" class="btn btn-secondary">Reset</a>
                <?php endif; ?>
            </form>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 35px; text-align: center;">No</th>
                            <th>Nomor Loker</th>
                            <th>Lokasi</th>
                            <th>Kapasitas</th>
                            <th>Terisi</th>
                            <th style="min-width: 130px;">Keterisian</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($loker_tampil)): ?>
                            <?php $no = 1; foreach ($loker_tampil as $l): ?>
                                <?php 
                                    $terisi    = $l['terisi'] ?? 0;
                                    $kapasitas = $l['kapasitas'] ?? 5;
                                    $persen    = ($kapasitas > 0) ? round(($terisi / $kapasitas) * 100) : 0;

                                    $badge_class = 'badge-terisi';
                                    $bar_class   = '';

                                    if ($l['status'] === 'Kosong') {
                                        $badge_class = 'badge-kosong';
                                        $bar_class   = 'empty';
                                    } elseif ($l['status'] === 'Penuh' || $terisi >= $kapasitas) {
                                        $badge_class = 'badge-penuh';
                                        $bar_class   = 'full';
                                    } elseif ($l['status'] === 'Menunggu Verifikasi') {
                                        $badge_class = 'badge-menunggu';
                                    }
                                ?>
                                <tr>
                                    <td style="text-align: center; color: #9E988E;"><?= $no++ ?></td>
                                    <td><span class="badge badge-kode"><?= $l['nomor_loker'] ?></span></td>
                                    <td><?= $l['lokasi'] ?? 'Gedung A' ?></td>
                                    <td><?= $kapasitas ?> Slot</td>
                                    <td><?= $terisi ?> Barang</td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div class="locker-bar" style="flex: 1;">
                                                <div class="locker-bar-fill <?= $bar_class ?>" style="width: <?= $persen ?>%;"></div>
                                            </div>
                                            <span style="font-size: 11px; color: #6E6A63; width: 32px; text-align: right;"><?= $persen ?>%</span>
                                        </div>
                                    </td>
                                    <td><span class="badge <?= $badge_class ?>"><?= $l['status'] ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: #9E988E; padding: 25px;">
                                    Tidak ada data loker.
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