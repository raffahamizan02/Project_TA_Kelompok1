<?php
require_once "koneksi.php";

$halaman_aktif = 'beranda';
$nama_admin    = $_SESSION['nama_lengkap'] ?? 'Admin TU';

$stat           = ambilStatistik($koneksi, $mock_loker, $mock_barang);
$semua_loker    = ambilDaftarLoker($koneksi, $mock_loker);
$barang_terbaru = array_slice(ambilDaftarBarang($koneksi, $mock_barang), 0, 5);
$log_terbaru    = array_slice(ambilLogAktivitas($koneksi, $mock_log), 0, 4);

$loker_penuh_list = [];
foreach ($semua_loker as $l) {
    $terisi    = $l['terisi'] ?? 0;
    $kapasitas = $l['kapasitas'] ?? 5;
    if ($l['status'] === 'Penuh' || $terisi >= $kapasitas) {
        $loker_penuh_list[] = $l['nomor_loker'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - LokerKu</title>
    <link rel="stylesheet" href="styleAdmin.css">
</head>
<body>

<div class="layout">
    <?php include "sidebar.php"; ?>

    <main class="content">
        <div class="topbar">
            <div>
                <p class="welcome">Selamat datang kembali, <?= $nama_admin ?></p>
                <h1>Ringkasan Dashboard</h1>
            </div>
            <div class="profile">
                <div class="profile-icon">TU</div>
                <div>
                    <strong><?= $nama_admin ?></strong>
                    <small>Tata Usaha</small>
                </div>
            </div>
        </div>

        <?php if (!empty($loker_penuh_list)): ?>
            <div class="alert-box alert-penuh">
                <span>Peringatan: Loker <strong><?= implode(', ', $loker_penuh_list) ?></strong> sudah penuh.</span>
                <a href="dataloker.php" class="btn btn-sm btn-secondary">Lihat Loker</a>
            </div>
        <?php endif; ?>

        <div class="statistics">
            <div class="stat-card">
                <div class="stat-title">Total Loker</div>
                <div class="stat-value"><?= $stat['total_loker'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Loker Kosong</div>
                <div class="stat-value"><?= $stat['loker_kosong'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Loker Terisi</div>
                <div class="stat-value"><?= $stat['loker_terisi'] ?></div>
            </div>
            <div class="stat-card <?= ($stat['loker_penuh'] > 0) ? 'alert' : '' ?>">
                <div class="stat-title">Loker Penuh</div>
                <div class="stat-value"><?= $stat['loker_penuh'] ?></div>
            </div>
            <div class="stat-card highlight">
                <div class="stat-title">Barang Tersimpan</div>
                <div class="stat-value"><?= $stat['barang_tersimpan'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Barang Diambil</div>
                <div class="stat-value"><?= $stat['barang_diambil'] ?></div>
            </div>
        </div>

        <div class="row-grid">
            <div class="card">
                <div class="card-header">
                    <h3>Kondisi Loker (Blok C)</h3>
                    <a href="dataloker.php" class="btn btn-sm btn-secondary">Semua Loker</a>
                </div>

                <div class="locker-grid">
                    <?php foreach ($semua_loker as $l): ?>
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
                        <div class="locker-box">
                            <div class="locker-head">
                                <span class="locker-no"><?= $l['nomor_loker'] ?></span>
                                <span class="badge <?= $badge_class ?>"><?= $l['status'] ?></span>
                            </div>
                            <div class="locker-info"><?= $terisi ?> / <?= $kapasitas ?> (<?= $persen ?>%)</div>
                            <div class="locker-bar">
                                <div class="locker-bar-fill <?= $bar_class ?>" style="width: <?= $persen ?>%;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Aktivitas Penitipan Mingguan</h3>
                    <span class="badge badge-kode">Minggu Ini</span>
                </div>

                <div class="chart">
                    <?php foreach ($mock_grafik as $g): ?>
                        <div class="bar-col <?= ($g['hari'] === 'Sabtu') ? 'active' : '' ?>">
                            <div class="bar" style="height: <?= $g['tinggi'] ?>;"></div>
                            <span class="bar-lbl"><?= $g['hari'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top: 16px;">
                    <div style="font-weight: bold; font-size: 12px; margin-bottom: 8px; color: #6E6A63;">Log Aktivitas Terbaru</div>
                    <div class="table-container">
                        <table>
                            <tbody>
                                <?php foreach ($log_terbaru as $lg): ?>
                                    <tr>
                                        <td style="width: 70px;">
                                            <span class="badge <?= ($lg['aksi'] === 'Tambah') ? 'badge-menunggu' : 'badge-terisi' ?>">
                                                <?= $lg['aksi'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= $lg['barang'] ?>
                                            <div style="font-size: 11px; color: #6E6A63;"><?= $lg['keterangan'] ?></div>
                                        </td>
                                        <td style="font-size: 11px; color: #9E988E; width: 65px; text-align: right;">
                                            <?= date('H:i', strtotime($lg['waktu'])) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Barang Titipan Terbaru</h3>
                <a href="databarang.php" class="btn btn-sm btn-primary">Lihat Semua Barang</a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 45px;">Foto</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Siswa</th>
                            <th>Loker</th>
                            <th>Waktu Titip</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($barang_terbaru)): ?>
                            <?php foreach ($barang_terbaru as $b): ?>
                                <?php
                                    $foto = $b['foto_barang'] ?? '';
                                    $path_foto = '';
                                    if ($foto !== '') {
                                        if (file_exists("uploads/$foto")) $path_foto = "uploads/$foto";
                                        elseif (file_exists("../uploads/$foto")) $path_foto = "../uploads/$foto";
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <div class="thumb-foto">
                                            <?php if ($path_foto !== ''): ?>
                                                <img src="<?= $path_foto ?>" alt="Foto">
                                            <?php else: ?>
                                                <span>-</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-kode"><?= $b['kode_barang'] ?></span></td>
                                    <td><strong><?= $b['nama_barang'] ?></strong></td>
                                    <td><?= $b['nama_siswa'] ?></td>
                                    <td><span class="badge badge-terisi"><?= $b['nomor_loker'] ?></span></td>
                                    <td style="color: #6E6A63; font-size: 12px;"><?= $b['waktu_penitipan'] ?></td>
                                    <td>
                                        <?php if ($b['status'] === 'Tersimpan'): ?>
                                            <span class="badge badge-tersimpan">Tersimpan</span>
                                        <?php else: ?>
                                            <span class="badge badge-diambil">Sudah diambil</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: #9E988E; padding: 20px;">
                                    Belum ada data barang.
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