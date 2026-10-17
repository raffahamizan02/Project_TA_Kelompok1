<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include "connectBarang.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_massal'])) {
    $aksi = $_POST['aksi_massal'];
    $ids = isset($_POST['id_barang']) ? (array) $_POST['id_barang'] : [];

    $ids = array_filter(array_map('intval', $ids));

    if (empty($ids)) {
        header("Location: indexBarang.php?pesan=tidak_ada_pilihan");
        exit();
    }

    $id_list = implode(',', $ids);

    if ($aksi === 'verifikasi') {
        $qLoker = mysqli_query($koneksi, "SELECT DISTINCT id_loker FROM barang WHERE id_barang IN ($id_list)");
        $loker_terpengaruh = [];
        while ($l = mysqli_fetch_assoc($qLoker)) {
            if (!empty($l['id_loker'])) {
                $loker_terpengaruh[] = $l['id_loker'];
            }
        }

        $queryVerifikasi = "UPDATE barang SET 
                                status = 'Sudah diambil', 
                                waktu_pengambilan = NOW() 
                            WHERE id_barang IN ($id_list) AND status = 'Tersimpan'";
        mysqli_query($koneksi, $queryVerifikasi);

        foreach ($loker_terpengaruh as $id_lkr) {
            $cekSisa = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM barang WHERE id_loker = '$id_lkr' AND status = 'Tersimpan'");
            $sisa = mysqli_fetch_assoc($cekSisa)['total'] ?? 0;
            if ($sisa == 0) {
                mysqli_query($koneksi, "UPDATE loker SET status = 'Kosong' WHERE id_loker = '$id_lkr'");
            }
        }

        header("Location: indexBarang.php?pesan=sukses_verifikasi_massal&total=" . count($ids));
        exit();

    } elseif ($aksi === 'hapus') {
        $qData = mysqli_query($koneksi, "SELECT foto_barang, id_loker FROM barang WHERE id_barang IN ($id_list)");
        $loker_terpengaruh = [];

        while ($b = mysqli_fetch_assoc($qData)) {
            if (!empty($b['foto_barang'])) {
                $path_foto = __DIR__ . '/uploads/' . $b['foto_barang'];
                if (file_exists($path_foto)) {
                    unlink($path_foto);
                }
            }
            if (!empty($b['id_loker'])) {
                $loker_terpengaruh[$b['id_loker']] = true;
            }
        }

        mysqli_query($koneksi, "DELETE FROM barang WHERE id_barang IN ($id_list)");

        foreach (array_keys($loker_terpengaruh) as $id_lkr) {
            $cekSisa = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM barang WHERE id_loker = '$id_lkr' AND status = 'Tersimpan'");
            $sisa = mysqli_fetch_assoc($cekSisa)['total'] ?? 0;
            if ($sisa == 0) {
                mysqli_query($koneksi, "UPDATE loker SET status = 'Kosong' WHERE id_loker = '$id_lkr'");
            }
        }

        header("Location: indexBarang.php?pesan=sukses_hapus_massal&total=" . count($ids));
        exit();
    }
}

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

$qHitungStatus = mysqli_query($koneksi, "SELECT 
    COUNT(*) AS total_semua,
    SUM(CASE WHEN status = 'Tersimpan' THEN 1 ELSE 0 END) AS total_tersimpan,
    SUM(CASE WHEN status = 'Sudah diambil' THEN 1 ELSE 0 END) AS total_diambil
FROM barang");
$dataHitung = mysqli_fetch_assoc($qHitungStatus);
$c_semua = (int)($dataHitung['total_semua'] ?? 0);
$c_tersimpan = (int)($dataHitung['total_tersimpan'] ?? 0);
$c_diambil = (int)($dataHitung['total_diambil'] ?? 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang - Penitipan Loker</title>
    <link rel="stylesheet" href="styleBarang.css?v=<?php echo time(); ?>">
    <style>
        <?php include __DIR__ . '/styleBarang.css'; ?>
    </style>
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
            <?php if ($_GET['pesan'] == 'sukses_tambah'): ?>
                <div class="alert alert-success">Data barang berhasil ditambahkan ke loker.</div>
            <?php elseif ($_GET['pesan'] == 'sukses_edit'): ?>
                <div class="alert alert-success">Data barang berhasil diperbarui.</div>
            <?php elseif ($_GET['pesan'] == 'sukses_hapus'): ?>
                <div class="alert alert-info">Data barang berhasil dihapus.</div>
            <?php elseif ($_GET['pesan'] == 'sukses_ambil'): ?>
                <div class="alert alert-success">Status barang berhasil diubah menjadi Sudah diambil.</div>
            <?php elseif ($_GET['pesan'] == 'sukses_verifikasi_massal'): ?>
                <div class="alert alert-success">Berhasil memverifikasi <strong><?php echo (int)($_GET['total'] ?? 0); ?></strong> barang terpilih sudah diambil.</div>
            <?php elseif ($_GET['pesan'] == 'sukses_hapus_massal'): ?>
                <div class="alert alert-info">Berhasil menghapus <strong><?php echo (int)($_GET['total'] ?? 0); ?></strong> data barang terpilih beserta fotonya.</div>
            <?php elseif ($_GET['pesan'] == 'tidak_ada_pilihan'): ?>
                <div class="alert alert-danger">Silakan pilih minimal 1 barang terlebih dahulu dengan mencentang kotak.</div>
            <?php endif; ?>
        </div>
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

            <div class="filter-tabs">
                <a href="indexBarang.php<?php echo !empty($kata_kunci) ? '?cari=' . urlencode($kata_kunci) : ''; ?>" 
                   class="filter-tab <?php echo empty($filter_status) ? 'active' : ''; ?>">
                    Semua <span class="tab-count">(<?php echo $c_semua; ?>)</span>
                </a>
                <a href="indexBarang.php?status=Tersimpan<?php echo !empty($kata_kunci) ? '&cari=' . urlencode($kata_kunci) : ''; ?>" 
                   class="filter-tab <?php echo ($filter_status == 'Tersimpan') ? 'active' : ''; ?>">
                    Tersimpan <span class="tab-count">(<?php echo $c_tersimpan; ?>)</span>
                </a>
                <a href="indexBarang.php?status=Sudah diambil<?php echo !empty($kata_kunci) ? '&cari=' . urlencode($kata_kunci) : ''; ?>" 
                   class="filter-tab <?php echo ($filter_status == 'Sudah diambil') ? 'active' : ''; ?>">
                    Sudah Diambil <span class="tab-count">(<?php echo $c_diambil; ?>)</span>
                </a>
            </div>
        </div>

        
        <form method="POST" action="indexBarang.php" id="formAksiMassal">
            
            <div class="bulk-action-bar">
                <div class="bulk-select-area">
                    <label class="label-select-all" title="Klik untuk memilih semua barang">
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
                                <td style="text-align: center;">
                                    <input type="checkbox" name="id_barang[]" value="<?php echo $row['id_barang']; ?>" class="item-checkbox" onchange="hitungTerpilih()" style="cursor: pointer; width: 16px; height: 16px;">
                                </td>
                                <td style="text-align: center;"><?php echo $no++; ?></td>
                                <td>
                                    <?php if ($ada_foto): ?>
                                        <a href="<?php echo $path_foto; ?>" target="_blank">
                                            <img src="<?php echo $path_foto; ?>" alt="Foto" class="thumb-img">
                                        </a>
                                    <?php else: ?>
                                        <span style="font-size: 11px; color: #9E988E;">No Foto</span>
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
                                    <br><small style="color: #6E6A63;"><?php echo htmlspecialchars($row['nis_siswa'] ?? ''); ?></small>
                                </td>
                                <td>
                                    Loker <?php echo htmlspecialchars($row['nomor_loker'] ?? '-'); ?>
                                    <br><small style="color: #6E6A63;"><?php echo htmlspecialchars($row['lokasi'] ?? ''); ?></small>
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
                                        echo '<span style="color: #9E988E;">-</span>';
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
    var checkboxes = document.querySelectorAll('.item-checkbox');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = master.checked;
    }
    hitungTerpilih();
}

function hitungTerpilih() {
    var checkboxes = document.querySelectorAll('.item-checkbox');
    var totalCentang = 0;
    
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            totalCentang++;
        }
    }

    var teksJumlah = document.getElementById('jumlahTerpilih');
    if (teksJumlah) {
        teksJumlah.innerText = totalCentang;
    }

    var checkAll = document.getElementById('checkAll');
    if (checkAll && checkboxes.length > 0) {
        checkAll.checked = (totalCentang === checkboxes.length);
    }
}

function konfirmasiAksi(jenisAksi) {
    var checkboxes = document.querySelectorAll('.item-checkbox:checked');
    var total = checkboxes.length;

    if (total === 0) {
        alert('Silakan pilih minimal 1 barang terlebih dahulu dengan mencentang kotak!');
        return false;
    }

    if (jenisAksi === 'verifikasi') {
        return confirm('Apakah Anda yakin ingin memverifikasi ' + total + ' barang terpilih sudah diambil?');
    } else if (jenisAksi === 'hapus') {
        return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus ' + total + ' data barang terpilih beserta fotonya? Tindakan ini tidak dapat dibatalkan.');
    }

    return false;
}

var notifAlert = document.getElementById('notifikasiAlert');
if (notifAlert) {
    if (window.history.replaceState) {
        var url = new URL(window.location.href);
        if (url.searchParams.has('pesan')) {
            url.searchParams.delete('pesan');
            url.searchParams.delete('total');
            window.history.replaceState(null, '', url.pathname + url.search);
        }
    }

    setTimeout(function() {
        notifAlert.style.transition = 'opacity 0.6s ease, max-height 0.6s ease, margin 0.6s ease';
        notifAlert.style.opacity = '0';
        notifAlert.style.maxHeight = '0px';
        notifAlert.style.marginBottom = '0px';
        notifAlert.style.overflow = 'hidden';

        setTimeout(function() {
            notifAlert.style.display = 'none';
        }, 600);
    }, 5000);
}
</script>

</body>
</html>