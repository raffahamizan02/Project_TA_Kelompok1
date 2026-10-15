<?php
include "connectBarang.php";

$kata_kunci = "";
$kondisi_cari = "";

if (isset($_GET['cari']) && !empty(trim($_GET['cari']))) {
    $kata_kunci = mysqli_real_escape_string($koneksi, trim($_GET['cari']));
    $kondisi_cari = " AND (b.nama_barang LIKE '%$kata_kunci%' 
                       OR b.kode_barang LIKE '%$kata_kunci%' 
                       OR u.nama_lengkap LIKE '%$kata_kunci%' 
                       OR l.nomor_loker LIKE '%$kata_kunci%') ";
}

$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($koneksi, $_GET['status']) : '';
$kondisi_status = "";
if (!empty($filter_status)) {
    $kondisi_status = " AND b.status = '$filter_status' ";
}

$querySql = "SELECT b.*, 
                    u.nama_lengkap AS nama_siswa, 
                    u.username AS nis_siswa, 
                    l.nomor_loker, 
                    l.lokasi
             FROM barang b
             LEFT JOIN users u ON b.id_siswa = u.id_user
             LEFT JOIN loker l ON b.id_loker = l.id_loker
             WHERE 1=1 
             $kondisi_cari 
             $kondisi_status
             ORDER BY b.id_barang DESC";

$hasilData = mysqli_query($koneksi, $querySql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang - Penitipan Loker</title>
    <link rel="stylesheet" href="styleBarang.css">
</head>
<body>

<div class="container">
    <div class="header-box">
        <div>
            <h2>Data Barang Titipan</h2>
            <p>Daftar seluruh barang yang dititipkan siswa di loker sekolah</p>
        </div>
        <div>
            <a href="createBarang.php" class="btn btn-primary">+ Tambah Barang</a>
        </div>
    </div>

\    <?php if (isset($_GET['pesan'])): ?>
        <?php if ($_GET['pesan'] == 'sukses_tambah'): ?>
            <div class="alert alert-success">Data barang berhasil ditambahkan ke loker.</div>
        <?php elseif ($_GET['pesan'] == 'sukses_edit'): ?>
            <div class="alert alert-success">Data barang berhasil diperbarui.</div>
        <?php elseif ($_GET['pesan'] == 'sukses_hapus'): ?>
            <div class="alert alert-info">Data barang berhasil dihapus.</div>
        <?php elseif ($_GET['pesan'] == 'sukses_ambil'): ?>
            <div class="alert alert-success">Status barang berhasil diubah menjadi Sudah diambil.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card">
        <div class="toolbar">
            <form method="GET" action="indexBarang.php" class="search-form">
                <?php if (!empty($filter_status)): ?>
                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($filter_status); ?>">
                <?php endif; ?>
                <input type="text" name="cari" class="search-input" placeholder="Cari nama barang, kode, atau siswa..." value="<?php echo htmlspecialchars($kata_kunci); ?>">
                <button type="submit" class="btn btn-secondary">Cari</button>
                <?php if (!empty($kata_kunci) || !empty($filter_status)): ?>
                    <a href="indexBarang.php" class="btn btn-secondary btn-sm" style="line-height: 22px;">Reset</a>
                <?php endif; ?>
            </form>

            <div style="font-size: 13px;">
                Filter Status: 
                <a href="indexBarang.php" style="margin: 0 4px; color: #0F233A; <?php echo empty($filter_status) ? 'font-weight: bold;' : ''; ?>">Semua</a> |
                <a href="indexBarang.php?status=Tersimpan" style="margin: 0 4px; color: #15803D; <?php echo ($filter_status == 'Tersimpan') ? 'font-weight: bold;' : ''; ?>">Tersimpan</a> |
                <a href="indexBarang.php?status=Sudah diambil" style="margin: 0 4px; color: #64748B; <?php echo ($filter_status == 'Sudah diambil') ? 'font-weight: bold;' : ''; ?>">Sudah Diambil</a>
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 35px; text-align: center;">No</th>
                        <th style="width: 60px;">Foto</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Siswa</th>
                        <th>Loker</th>
                        <th>Status</th>
                        <th>Waktu Penitipan</th>
                        <th>Waktu Pengambilan</th>
                        <th style="text-align: center; width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($hasilData) > 0): ?>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($hasilData)): 
                            $path_foto = 'uploads/' . $row['foto_barang'];
                            $ada_foto = (!empty($row['foto_barang']) && file_exists(__DIR__ . '/' . $path_foto));
                        ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $no++; ?></td>
                            <td>
                                <?php if ($ada_foto): ?>
                                    <a href="<?php echo $path_foto; ?>" target="_blank">
                                        <img src="<?php echo $path_foto; ?>" alt="Foto" class="thumb-img">
                                    </a>
                                <?php else: ?>
                                    <span style="font-size: 11px; color: #94A3B8;">No Foto</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-kode"><?php echo htmlspecialchars($row['kode_barang']); ?></span>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['nama_barang']); ?></strong>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['nama_siswa'] ?? 'Siswa #' . $row['id_siswa']); ?>
                                <br><small style="color: #64748B;"><?php echo htmlspecialchars($row['nis_siswa'] ?? ''); ?></small>
                            </td>
                            <td>
                                Loker <?php echo htmlspecialchars($row['nomor_loker'] ?? '-'); ?>
                                <br><small style="color: #64748B;"><?php echo htmlspecialchars($row['lokasi'] ?? ''); ?></small>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'Tersimpan'): ?>
                                    <span class="badge badge-tersimpan">Tersimpan</span>
                                <?php else: ?>
                                    <span class="badge badge-diambil">Sudah diambil</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo date('d/m/Y H:i', strtotime($row['waktu_penitipan'])); ?>
                            </td>
                            <td>
                                <?php 
                                if (!empty($row['waktu_pengambilan'])) {
                                    echo date('d/m/Y H:i', strtotime($row['waktu_pengambilan']));
                                } else {
                                    echo '<span style="color: #94A3B8;">-</span>';
                                }
                                ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="detailBarang.php?id=<?php echo $row['id_barang']; ?>" class="btn btn-sm btn-secondary">Detail</a>
                                <a href="verificationBarang.php?id=<?php echo $row['id_barang']; ?>" class="btn btn-sm btn-primary">Verifikasi</a>
                                <a href="updateBarang.php?id=<?php echo $row['id_barang']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="deleteBarang.php?id=<?php echo $row['id_barang']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus barang <?php echo htmlspecialchars($row['kode_barang']); ?>?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 30px; color: #64748B;">
                                Belum ada data barang. Silakan klik tombol <strong>+ Tambah Barang</strong>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

</body>
</html>