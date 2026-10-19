<?php
require_once "connectBarang.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_massal'])) {
    $aksi = $_POST['aksi_massal'];
    $ids  = $_POST['id_barang'] ?? [];

    if (empty($ids)) {
        header("Location: indexBarang.php?pesan=tidak_ada_pilihan");
        exit();
    }

    $id_list = implode(',', $ids);
    $jumlah  = count($ids);

    $qData = mysqli_query($koneksi, "SELECT foto_barang, id_loker FROM barang WHERE id_barang IN ($id_list)");
    $loker_terpengaruh = [];

    while ($b = mysqli_fetch_assoc($qData)) {
        if (!empty($b['id_loker'])) {
            $loker_terpengaruh[] = $b['id_loker'];
        }
        if ($aksi === 'hapus' && !empty($b['foto_barang']) && file_exists("uploads/" . $b['foto_barang'])) {
            unlink("uploads/" . $b['foto_barang']);
        }
    }

    if ($aksi === 'verifikasi') {
        mysqli_query($koneksi, "UPDATE barang SET status = 'Sudah diambil', waktu_pengambilan = NOW() WHERE id_barang IN ($id_list) AND status = 'Tersimpan'");
        $pesan = "sukses_verifikasi_massal";
    } elseif ($aksi === 'hapus') {
        mysqli_query($koneksi, "DELETE FROM barang WHERE id_barang IN ($id_list)");
        $pesan = "sukses_hapus_massal";
    }

    foreach ($loker_terpengaruh as $id_lkr) {
        $cekSisa = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM barang WHERE id_loker = '$id_lkr' AND status = 'Tersimpan'");
        $sisa = mysqli_fetch_assoc($cekSisa)['total'];
        if ($sisa == 0) {
            mysqli_query($koneksi, "UPDATE loker SET status = 'Kosong' WHERE id_loker = '$id_lkr'");
        }
    }

    header("Location: indexBarang.php?pesan=$pesan&total=$jumlah");
    exit();
}

$kata_kunci    = trim($_GET['cari'] ?? '');
$filter_status = $_GET['status'] ?? '';
$kondisi       = "WHERE 1=1";

if ($kata_kunci !== '') {
    $cari = mysqli_real_escape_string($koneksi, $kata_kunci);
    $kondisi .= " AND (b.nama_barang LIKE '%$cari%' 
                   OR b.kode_barang LIKE '%$cari%' 
                   OR u.nama_lengkap LIKE '%$cari%' 
                   OR l.nomor_loker LIKE '%$cari%')";
}

if ($filter_status !== '') {
    $status = mysqli_real_escape_string($koneksi, $filter_status);
    $kondisi .= " AND b.status = '$status'";
}

$querySql = "SELECT b.*, u.nama_lengkap AS nama_siswa, u.username AS nis_siswa, l.nomor_loker, l.lokasi
             FROM barang b
             LEFT JOIN users u ON b.id_siswa = u.id_user
             LEFT JOIN loker l ON b.id_loker = l.id_loker
             $kondisi
             ORDER BY b.id_barang DESC";
$hasilData = mysqli_query($koneksi, $querySql);

$c_semua     = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_barang FROM barang"));
$c_tersimpan = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_barang FROM barang WHERE status = 'Tersimpan'"));
$c_diambil   = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_barang FROM barang WHERE status = 'Sudah diambil'"));
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

    <?php if (isset($_GET['pesan'])): ?>
        <div id="notifikasiAlert">
            <?php $p = $_GET['pesan']; $total = $_GET['total'] ?? 0; ?>
            <?php if ($p == 'sukses_tambah'): ?>
                <div class="alert alert-success">Data barang berhasil ditambahkan ke loker.</div>
            <?php elseif ($p == 'sukses_edit'): ?>
                <div class="alert alert-success">Data barang berhasil diperbarui.</div>
            <?php elseif ($p == 'sukses_hapus'): ?>
                <div class="alert alert-info">Data barang berhasil dihapus.</div>
            <?php elseif ($p == 'sukses_ambil'): ?>
                <div class="alert alert-success">Status barang berhasil diubah menjadi Sudah diambil.</div>
            <?php elseif ($p == 'sukses_verifikasi_massal'): ?>
                <div class="alert alert-success">Berhasil memverifikasi <strong><?= $total ?></strong> barang terpilih sudah diambil.</div>
            <?php elseif ($p == 'sukses_hapus_massal'): ?>
                <div class="alert alert-info">Berhasil menghapus <strong><?= $total ?></strong> data barang terpilih beserta fotonya.</div>
            <?php elseif ($p == 'tidak_ada_pilihan'): ?>
                <div class="alert alert-danger">Silakan pilih minimal 1 barang terlebih dahulu dengan mencentang kotak.</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="toolbar">
            <form method="GET" action="indexBarang.php" class="search-form">
                <?php if ($filter_status !== ''): ?>
                    <input type="hidden" name="status" value="<?= $filter_status ?>">
                <?php endif; ?>
                <input type="text" name="cari" class="search-input" placeholder="Cari barang, kode, atau siswa..." value="<?= $kata_kunci ?>">
                <button type="submit" class="btn btn-secondary">Cari</button>
                <?php if ($kata_kunci !== '' || $filter_status !== ''): ?>
                    <a href="indexBarang.php" class="btn btn-secondary btn-sm" style="line-height: 22px;">Reset</a>
                <?php endif; ?>
            </form>

            <?php $paramCari = ($kata_kunci !== '') ? '&cari=' . urlencode($kata_kunci) : ''; ?>
            <div class="filter-tabs">
                <a href="indexBarang.php<?= ($kata_kunci !== '') ? '?cari=' . urlencode($kata_kunci) : '' ?>" 
                   class="filter-tab <?= ($filter_status == '') ? 'active' : '' ?>">
                    Semua <span class="tab-count">(<?= $c_semua ?>)</span>
                </a>
                <a href="indexBarang.php?status=Tersimpan<?= $paramCari ?>" 
                   class="filter-tab <?= ($filter_status == 'Tersimpan') ? 'active' : '' ?>">
                    Tersimpan <span class="tab-count">(<?= $c_tersimpan ?>)</span>
                </a>
                <a href="indexBarang.php?status=Sudah diambil<?= $paramCari ?>" 
                   class="filter-tab <?= ($filter_status == 'Sudah diambil') ? 'active' : '' ?>">
                    Sudah Diambil <span class="tab-count">(<?= $c_diambil ?>)</span>
                </a>
            </div>
        </div>

        <form method="POST" action="indexBarang.php" id="formAksiMassal">
            <div class="bulk-action-bar">
                <div class="bulk-select-area">
                    <label class="label-select-all">
                        <input type="checkbox" id="checkAll" onchange="toggleSelectAll(this)">
                        <span>Pilih Semua Barang</span>
                    </label>
                    <span class="bulk-divider">|</span>
                    <span class="bulk-count">Terpilih: <strong id="jumlahTerpilih" style="color: #C85A17;">0</strong> data</span>
                </div>
                <div class="bulk-buttons">
                    <button type="submit" name="aksi_massal" value="verifikasi" class="btn btn-sm btn-success" onclick="return konfirmasiAksi('verifikasi')">
                        ✓ Tandai Sudah Diambil
                    </button>
                    <button type="submit" name="aksi_massal" value="hapus" class="btn btn-sm btn-danger" onclick="return konfirmasiAksi('hapus')">
                        Hapus Terpilih
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 45px; text-align: center;">Pilih</th>
                            <th style="width: 35px; text-align: center;">No</th>
                            <th style="width: 60px;">Foto</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Siswa</th>
                            <th>Loker</th>
                            <th>Status</th>
                            <th>Waktu Penitipan</th>
                            <th>Waktu Pengambilan</th>
                            <th style="text-align: center; width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($hasilData) > 0): ?>
                            <?php 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($hasilData)): 
                                $path_foto = 'uploads/' . $row['foto_barang'];
                                $ada_foto = (!empty($row['foto_barang']) && file_exists($path_foto));
                            ?>
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="id_barang[]" value="<?= $row['id_barang'] ?>" class="item-checkbox" onchange="hitungTerpilih()">
                                </td>
                                <td style="text-align: center;"><?= $no++ ?></td>
                                <td>
                                    <?php if ($ada_foto): ?>
                                        <a href="<?= $path_foto ?>" target="_blank">
                                            <img src="<?= $path_foto ?>" alt="Foto" class="thumb-img">
                                        </a>
                                    <?php else: ?>
                                        <span style="font-size: 11px; color: #9E988E;">No Foto</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge-kode"><?= $row['kode_barang'] ?></span>
                                </td>
                                <td>
                                    <strong><?= $row['nama_barang'] ?></strong>
                                </td>
                                <td>
                                    <?= $row['nama_siswa'] ?? 'Siswa #' . $row['id_siswa'] ?>
                                    <br><small style="color: #6E6A63;"><?= $row['nis_siswa'] ?? '' ?></small>
                                </td>
                                <td>
                                    Loker <?= $row['nomor_loker'] ?? '-' ?>
                                    <br><small style="color: #6E6A63;"><?= $row['lokasi'] ?? '' ?></small>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'Tersimpan'): ?>
                                        <span class="badge badge-tersimpan">Tersimpan</span>
                                    <?php else: ?>
                                        <span class="badge badge-diambil">Sudah diambil</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($row['waktu_penitipan'])) ?></td>
                                <td>
                                    <?= !empty($row['waktu_pengambilan']) ? date('d/m/Y H:i', strtotime($row['waktu_pengambilan'])) : '-' ?>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <a href="verificationBarang.php?id=<?= $row['id_barang'] ?>" class="btn btn-sm btn-primary">Verifikasi</a>
                                    <a href="updateBarang.php?id=<?= $row['id_barang'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="deleteBarang.php?id=<?= $row['id_barang'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus barang <?= $row['kode_barang'] ?>?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" style="text-align: center; padding: 30px; color: #6E6A63;">
                                    Belum ada data barang. Silakan klik tombol <strong>+ Tambah Barang</strong>.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
function toggleSelectAll(master) {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => cb.checked = master.checked);
    hitungTerpilih();
}

function hitungTerpilih() {
    const semua = document.querySelectorAll('.item-checkbox');
    const terpilih = document.querySelectorAll('.item-checkbox:checked');

    document.getElementById('jumlahTerpilih').innerText = terpilih.length;

    const checkAll = document.getElementById('checkAll');
    if (checkAll && semua.length > 0) {
        checkAll.checked = (terpilih.length === semua.length);
    }
}

function konfirmasiAksi(jenisAksi) {
    const total = document.querySelectorAll('.item-checkbox:checked').length;

    if (total === 0) {
        alert('Silakan pilih minimal 1 barang terlebih dahulu!');
        return false;
    }

    if (jenisAksi === 'verifikasi') {
        return confirm('Yakin ingin memverifikasi ' + total + ' barang terpilih sudah diambil?');
    } else if (jenisAksi === 'hapus') {
        return confirm('Yakin ingin menghapus ' + total + ' data barang terpilih beserta fotonya?');
    }
    return false;
}

const notifAlert = document.getElementById('notifikasiAlert');
if (notifAlert) {
    setTimeout(() => {
        notifAlert.style.display = 'none';
    }, 4000);
}
</script>

</body>
</html>