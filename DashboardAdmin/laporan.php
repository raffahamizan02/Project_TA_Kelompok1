<?php
require_once "koneksi.php";

$halaman_aktif = 'laporan';
$nama_admin    = $_SESSION['nama_lengkap'] ?? 'Admin TU';

$tab = $_GET['tab'] ?? 'rekap';

// Filter untuk Tab Rekap Barang
$tgl_awal  = trim($_GET['tgl_awal'] ?? '');
$tgl_akhir = trim($_GET['tgl_akhir'] ?? '');
$status    = trim($_GET['status'] ?? '');
$kategori  = trim($_GET['kategori'] ?? '');

// Filter untuk Tab Log Aktivitas
$filter_aksi    = trim($_GET['aksi'] ?? '');
$filter_tanggal = trim($_GET['tanggal'] ?? '');

// Ambil data sesuai tab yang aktif
$rekap_barang = ambilRekapBarang($koneksi, $mock_barang, $tgl_awal, $tgl_akhir, $status, $kategori);
$daftar_log   = ambilLogAktivitas($koneksi, $mock_log, $filter_aksi, $filter_tanggal);

// ==========================================
// EKSPOR CSV REKAP DATA BARANG (WEEK 6)
// ==========================================
if (($_GET['export'] ?? '') === 'csv') {
    if ($tab === 'rekap') {
        $nama_file = 'rekap_data_barang_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nama_file . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['No', 'Kode Barang', 'Nama Siswa', 'NIS', 'Nama Barang', 'Nomor Loker', 'Lokasi', 'Waktu Penitipan', 'Waktu Pengambilan', 'Durasi Titip', 'Status']);

        $no = 1;
        foreach ($rekap_barang['data'] as $row) {
            fputcsv($output, [
                $no++,
                $row['kode_barang'],
                $row['nama_siswa'],
                $row['nis_siswa'] ?? '',
                $row['nama_barang'],
                $row['nomor_loker'],
                $row['lokasi'] ?? 'Gedung A',
                $row['waktu_penitipan'],
                $row['waktu_pengambilan'] ?? '-',
                $row['durasi'] ?? '-',
                $row['status']
            ]);
        }
        fclose($output);
        exit();
    } else {
        // Ekspor Log Aktivitas
        $nama_file = 'laporan_log_aktivitas_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
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
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Data Barang & Statistik - LokerKu</title>
    <link rel="stylesheet" href="styleAdmin.css?v=<?= file_exists('styleAdmin.css') ? filemtime('styleAdmin.css') : time() ?>">
    <style>
        .nav-tabs {
            display: inline-flex;
            align-items: center;
            background-color: #FAF8F5;
            border: 1px solid #D8D2C7;
            border-radius: 8px;
            padding: 4px;
            gap: 4px;
            margin-bottom: 20px;
        }
        .tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            color: #6E6A63;
            background-color: transparent;
            border: 1px solid transparent;
            transition: all 0.15s ease-in-out;
        }
        .tab-btn:hover {
            color: #181715;
            background-color: #EFECE4;
        }
        .tab-btn.active {
            background-color: #FFFFFF;
            color: #181715;
            border-color: #D8D2C7;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            font-weight: bold;
        }
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-top: 12px;
        }
        .category-item {
            background-color: #FAF8F5;
            border: 1px solid #E8E3D9;
            border-radius: 6px;
            padding: 10px 14px;
        }
        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin-bottom: 8px;
        }
        .category-header strong {
            color: #181715;
        }
        .category-header span {
            color: #6E6A63;
            font-size: 12px;
        }
        .category-bar {
            width: 100%;
            height: 7px;
            background-color: #EFECE4;
            border-radius: 4px;
            overflow: hidden;
        }
        .category-fill {
            height: 100%;
            background-color: #E04F16;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<div class="layout">
    <?php include "sidebar.php"; ?>

    <main class="content">
        <!-- Topbar -->
        <div class="topbar">
            <div>
                <p class="welcome">Laporan & Rekapitulasi</p>
                <h1>Rekap Data Barang & Statistik</h1>
            </div>
            <div class="profile">
                <div class="profile-icon">TU</div>
                <div>
                    <strong><?= $nama_admin ?></strong>
                    <small>Tata Usaha</small>
                </div>
            </div>
        </div>

        <!-- Navigasi Tab: Rekap Barang vs Log Aktivitas -->
        <div class="nav-tabs no-print">
            <a href="laporan.php?tab=rekap" class="tab-btn <?= ($tab === 'rekap') ? 'active' : '' ?>">
                Rekap Data Barang
            </a>
            <a href="laporan.php?tab=log" class="tab-btn <?= ($tab === 'log') ? 'active' : '' ?>">
                Log Aktivitas Sistem
            </a>
        </div>

        <!-- Header khusus saat dicetak (Print View) -->
        <div class="print-header" style="display: none;">
            <h2 style="font-size: 18px; margin-bottom: 4px;">LAPORAN REKAPITULASI PENITIPAN BARANG LOKER SEKOLAH</h2>
            <p style="font-size: 12px; color: #555;">Dicetak pada: <?= date('d/m/Y H:i') ?> WIB | Oleh: <?= htmlspecialchars($nama_admin) ?></p>
        </div>

        <?php if ($tab === 'rekap'): ?>
            <!-- ========================================== -->
            <!-- TAB 1: REKAP DATA BARANG DAN STATISTIK     -->
            <!-- ========================================== -->

            <!-- 1. Statistik Ringkasan Periode Terpilih -->
            <div class="statistics">
                <div class="stat-card">
                    <div class="stat-title">Total Rekap</div>
                    <div class="stat-value"><?= $rekap_barang['total'] ?></div>
                </div>
                <div class="stat-card highlight">
                    <div class="stat-title">Masih Tersimpan</div>
                    <div class="stat-value"><?= $rekap_barang['tersimpan'] ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Sudah Diambil</div>
                    <div class="stat-value"><?= $rekap_barang['diambil'] ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Tingkat Diambil</div>
                    <div class="stat-value"><?= $rekap_barang['persen_diambil'] ?>%</div>
                </div>
            </div>

            <!-- 2. Statistik Kategori Barang Terpopuler -->
            <?php if (!empty($rekap_barang['kategori_populer'])): ?>
                <div class="card no-print">
                    <div class="card-header">
                        <h3>Statistik Jenis Barang</h3>
                        <span class="badge badge-kode">Frekuensi Terbanyak</span>
                    </div>

                    <div class="category-grid">
                        <?php 
                            $max_kat = max(array_values($rekap_barang['kategori_populer']));
                            if ($max_kat <= 0) $max_kat = 1;
                        ?>
                        <?php foreach ($rekap_barang['kategori_populer'] as $nama_kat => $jml_kat): ?>
                            <?php $p_kat = round(($jml_kat / $max_kat) * 100); ?>
                            <div class="category-item">
                                <div class="category-header">
                                    <strong><?= htmlspecialchars($nama_kat) ?></strong>
                                    <span><?= $jml_kat ?> barang</span>
                                </div>
                                <div class="category-bar">
                                    <div class="category-fill" style="width: <?= $p_kat ?>%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 3. Filter Rekap & Tabel -->
            <div class="card">
                <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 14px;">
                    <!-- Form Filter Tanggal & Status -->
                    <form method="GET" action="laporan.php" class="filter-bar" style="margin-bottom: 0;">
                        <input type="hidden" name="tab" value="rekap">

                        <input type="date" name="tgl_awal" class="form-control" value="<?= htmlspecialchars($tgl_awal) ?>" title="Tanggal Mulai">
                        <span style="color: #6E6A63;">s/d</span>
                        <input type="date" name="tgl_akhir" class="form-control" value="<?= htmlspecialchars($tgl_akhir) ?>" title="Tanggal Akhir">

                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="Tersimpan" <?= ($status === 'Tersimpan') ? 'selected' : '' ?>>Tersimpan</option>
                            <option value="Sudah diambil" <?= ($status === 'Sudah diambil') ? 'selected' : '' ?>>Sudah diambil</option>
                        </select>

                        <select name="kategori" class="form-control">
                            <option value="">Semua Kategori</option>
                            <?php foreach (['Tas', 'Helm', 'Jaket', 'Laptop', 'Sepatu', 'Buku', 'Botol'] as $opt_k): ?>
                                <option value="<?= $opt_k ?>" <?= ($kategori === $opt_k) ? 'selected' : '' ?>><?= $opt_k ?></option>
                            <?php endforeach; ?>
                        </select>

                        <button type="submit" class="btn btn-primary">Filter</button>
                        <?php if ($tgl_awal !== '' || $tgl_akhir !== '' || $status !== '' || $kategori !== ''): ?>
                            <a href="laporan.php?tab=rekap" class="btn btn-secondary">Reset</a>
                        <?php endif; ?>
                    </form>

                    <!-- Tombol Aksi Ekspor CSV & Cetak -->
                    <div style="display: flex; gap: 8px;">
                        <a href="laporan.php?tab=rekap&export=csv&tgl_awal=<?= urlencode($tgl_awal) ?>&tgl_akhir=<?= urlencode($tgl_akhir) ?>&status=<?= urlencode($status) ?>&kategori=<?= urlencode($kategori) ?>" class="btn btn-secondary">
                            Ekspor CSV
                        </a>
                        <button type="button" onclick="window.print()" class="btn btn-primary">
                            Cetak Laporan
                        </button>
                    </div>
                </div>

                <!-- Tabel Rekap Data Barang -->
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 35px; text-align: center;">No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Siswa</th>
                                <th>Loker</th>
                                <th>Waktu Titip</th>
                                <th>Waktu Ambil</th>
                                <th>Durasi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rekap_barang['data'])): ?>
                                <?php $no = 1; foreach ($rekap_barang['data'] as $b): ?>
                                    <tr>
                                        <td style="text-align: center; color: #9E988E;"><?= $no++ ?></td>
                                        <td><span class="badge badge-kode"><?= htmlspecialchars($b['kode_barang']) ?></span></td>
                                        <td><strong><?= htmlspecialchars($b['nama_barang']) ?></strong></td>
                                        <td>
                                            <?= htmlspecialchars($b['nama_siswa']) ?>
                                            <?php if (!empty($b['nis_siswa'])): ?>
                                                <div style="font-size: 11px; color: #9E988E;"><?= htmlspecialchars($b['nis_siswa']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-terisi"><?= htmlspecialchars($b['nomor_loker']) ?></span>
                                            <div style="font-size: 10px; color: #6E6A63;"><?= htmlspecialchars($b['lokasi'] ?? '') ?></div>
                                        </td>
                                        <td style="color: #6E6A63; font-size: 12px;"><?= htmlspecialchars($b['waktu_penitipan']) ?></td>
                                        <td style="color: #6E6A63; font-size: 12px;"><?= !empty($b['waktu_pengambilan']) ? htmlspecialchars($b['waktu_pengambilan']) : '-' ?></td>
                                        <td style="color: #6E6A63; font-size: 12px;"><?= htmlspecialchars($b['durasi'] ?? '-') ?></td>
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
                                    <td colspan="9" style="text-align: center; color: #9E988E; padding: 25px;">
                                        Tidak ada data barang pada periode filter yang dipilih.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            <!-- ========================================== -->
            <!-- TAB 2: LOG AKTIVITAS SISTEM                -->
            <!-- ========================================== -->
            <div class="card">
                <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 14px;">
                    <form method="GET" action="laporan.php" class="filter-bar" style="margin-bottom: 0;">
                        <input type="hidden" name="tab" value="log">

                        <select name="aksi" class="form-control">
                            <option value="">Semua Aksi</option>
                            <option value="Tambah" <?= ($filter_aksi === 'Tambah') ? 'selected' : '' ?>>Tambah</option>
                            <option value="Ubah" <?= ($filter_aksi === 'Ubah') ? 'selected' : '' ?>>Ubah</option>
                            <option value="Hapus" <?= ($filter_aksi === 'Hapus') ? 'selected' : '' ?>>Hapus</option>
                        </select>

                        <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($filter_tanggal) ?>">

                        <button type="submit" class="btn btn-primary">Filter</button>
                        <?php if ($filter_aksi !== '' || $filter_tanggal !== ''): ?>
                            <a href="laporan.php?tab=log" class="btn btn-secondary">Reset</a>
                        <?php endif; ?>
                    </form>

                    <a href="laporan.php?tab=log&export=csv&aksi=<?= urlencode($filter_aksi) ?>&tanggal=<?= urlencode($filter_tanggal) ?>" class="btn btn-secondary">
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
                                    <tr>
                                        <td><span class="badge badge-kode">#<?= htmlspecialchars($lg['id_log']) ?></span></td>
                                        <td><strong><?= htmlspecialchars($lg['nama_admin'] ?? 'Admin TU') ?></strong></td>
                                        <td>
                                            <?php 
                                                $badge_aksi = 'badge-terisi';
                                                if ($lg['aksi'] === 'Tambah') $badge_aksi = 'badge-tersimpan';
                                                elseif ($lg['aksi'] === 'Hapus') $badge_aksi = 'badge-penuh';
                                            ?>
                                            <span class="badge <?= $badge_aksi ?>"><?= htmlspecialchars($lg['aksi']) ?></span>
                                        </td>
                                        <td><strong><?= htmlspecialchars($lg['barang']) ?></strong></td>
                                        <td style="color: #6E6A63;"><?= htmlspecialchars($lg['keterangan']) ?></td>
                                        <td style="color: #9E988E; font-size: 12px; white-space: nowrap;"><?= htmlspecialchars($lg['waktu']) ?></td>
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
        <?php endif; ?>

    </main>
</div>

</body>
</html>