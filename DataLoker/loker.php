<?php
session_start();
include "koneksi.php";

$data = mysqli_query($koneksi, "SELECT * FROM loker");

// Statistik loker
$total  = mysqli_num_rows($data);
$kosong = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_loker FROM loker WHERE status = 'Kosong'"));
$terisi = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_loker FROM loker WHERE status != 'Kosong'"));

// Pesan notifikasi (kotak pesan di atas tabel)
$daftar_pesan = [
    'tambah' => '✓ Data Loker berhasil ditambahkan!',
    'edit'   => '✓ Data Loker berhasil diubah!',
    'hapus'  => '✓ Data Loker berhasil dihapus!',
];
$pesan = $daftar_pesan[$_GET['pesan'] ?? ''] ?? null;

// Daftar gedung untuk dropdown
$daftar_gedung = ['Gedung A', 'Gedung B', 'Gedung C', 'Gedung D'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Loker - LokerKu</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">
</head>

<body>

<div class="layout">

    <!-- ========== SIDEBAR ========== -->
    <aside class="sidebar">

        <div class="logo">
            <div class="logo-icon">L</div>
            <div>
                <h2>LokerKu</h2>
                <span>School System</span>
            </div>
        </div>

        <div class="menu-title">MENU UTAMA</div>

        <a href="#" class="menu active">
            <span>▦</span>
            Data Loker
        </a>

    </aside>


    <!-- ========== CONTENT ========== -->
    <main class="content">

        <!-- TOPBAR -->
        <div class="topbar">
            <div>
                <p class="welcome">Selamat datang kembali, Admin 👋</p>
                <h1>Manajemen Loker</h1>
            </div>

            <div class="profile">
                <div class="profile-icon">A</div>
                <div>
                    <strong>Admin</strong>
                    <small>Administrator</small>
                </div>
            </div>
        </div>


        <!-- STATISTIK -->
        <div class="statistics">

            <div class="stat-card">
                <div class="stat-icon blue">▣</div>
                <div>
                    <p>Total Loker</p>
                    <h2><?= $total ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">✓</div>
                <div>
                    <p>Loker Kosong</p>
                    <h2><?= $kosong ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon red">!</div>
                <div>
                    <p>Loker Terisi</p>
                    <h2><?= $terisi ?></h2>
                </div>
            </div>

        </div>


        <!-- DATA LOKER -->
        <div class="data-card">

            <div class="data-header">
                <div>
                    <h2>Data Loker</h2>
                    <p>Daftar seluruh loker yang tersedia di sekolah</p>
                </div>

                <button type="button" class="btn-add" onclick="bukaModalTambah()">
                    + Tambah Loker
                </button>
            </div>


            <!-- PESAN -->
            <?php if ($pesan): ?>
                <div class="pesan"><?= $pesan ?></div>
            <?php endif; ?>


            <!-- TABEL -->
            <div class="table-container">
                <table>

                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>KODE LOKER</th>
                            <th>LOKASI</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $no = 1;
                        mysqli_data_seek($data, 0);

                        while ($row = mysqli_fetch_assoc($data)):
                            // Data untuk dikirim ke JavaScript (aman di dalam atribut HTML)
                            $js_id     = (int) $row['id_loker'];
                            $js_nomor  = htmlspecialchars(json_encode($row['nomor_loker']), ENT_QUOTES);
                            $js_lokasi = htmlspecialchars(json_encode($row['lokasi']), ENT_QUOTES);
                            $js_status = htmlspecialchars(json_encode($row['status']), ENT_QUOTES);
                        ?>
                        <tr>

                            <td><?= $no++ ?></td>

                            <td>
                                <strong class="kode">
                                    <?= htmlspecialchars($row['nomor_loker']) ?>
                                </strong>
                            </td>

                            <td><?= htmlspecialchars($row['lokasi']) ?></td>

                            <td>
                                <?php if ($row['status'] == 'Kosong'): ?>
                                    <span class="status kosong">● Kosong</span>
                                <?php else: ?>
                                    <span class="status terisi">● Terisi</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="actions">

                                    <button
                                        type="button"
                                        class="btn-edit"
                                        onclick="bukaModalEdit(<?= $js_id ?>, <?= $js_nomor ?>, <?= $js_lokasi ?>, <?= $js_status ?>)">
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        class="btn-delete"
                                        onclick="bukaModalHapus(<?= $js_id ?>, <?= $js_nomor ?>)">
                                        Delete
                                    </button>

                                </div>
                            </td>

                        </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>
            </div>

        </div>

    </main>

</div>


<!-- ================================================= -->
<!-- MODAL TAMBAH LOKER -->
<!-- ================================================= -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal-card">

        <div class="modal-header">
            <div>
                <h2>Tambah Loker</h2>
                <p>Tambahkan data loker baru</p>
            </div>

            <button type="button" class="modal-close" onclick="tutupModal()">×</button>
        </div>

        <form action="tambah_loker.php" method="POST">

            <div class="form-group">
                <label>Nomor Loker</label>
                <input
                    type="text"
                    name="nomor_loker"
                    placeholder="Contoh: L-001"
                    required>
            </div>

            <div class="form-group">
                <label>Lokasi</label>
                <select name="lokasi" required>
                    <option value="" disabled selected>-- Pilih Gedung --</option>
                    <?php foreach ($daftar_gedung as $gedung): ?>
                        <option value="<?= $gedung ?>"><?= $gedung ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="status-info">
                <span class="status kosong">● Kosong</span>
                <p>
                    Loker baru otomatis berstatus <strong>Kosong</strong>.
                    Status akan berubah otomatis ketika user mengisi barang.
                </p>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="tutupModal()">Batal</button>
                <button type="submit" name="simpan" class="btn-save">Simpan Loker</button>
            </div>

        </form>

    </div>
</div>


<!-- ================================================= -->
<!-- MODAL EDIT LOKER -->
<!-- ================================================= -->
<div class="modal-overlay" id="modalEdit">
    <div class="modal-card">

        <div class="modal-header">
            <div>
                <h2>Edit Loker</h2>
                <p>Ubah informasi loker</p>
            </div>

            <button type="button" class="modal-close" onclick="tutupEdit()">×</button>
        </div>

        <form action="edit_loker.php" method="POST">

            <input type="hidden" name="id_loker" id="edit_id_loker">

            <div class="form-group">
                <label>Nomor Loker</label>
                <input
                    type="text"
                    name="nomor_loker"
                    id="edit_nomor_loker"
                    required>
            </div>

            <div class="form-group">
                <label>Lokasi</label>
                <select name="lokasi" id="edit_lokasi" required>
                    <?php foreach ($daftar_gedung as $gedung): ?>
                        <option value="<?= $gedung ?>"><?= $gedung ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="status-info">
                <span class="status" id="edit_status_tampilan">● Status</span>
                <p>
                    Status loker tidak dapat diubah oleh admin.
                    Status akan ditentukan otomatis berdasarkan barang user.
                </p>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="tutupEdit()">Batal</button>
                <button type="submit" name="edit" class="btn-save">Simpan Perubahan</button>
            </div>

        </form>

    </div>
</div>


<!-- ================================================= -->
<!-- MODAL HAPUS LOKER -->
<!-- ================================================= -->
<div class="modal-overlay" id="modalHapus">
    <div class="modal-card delete-card">

        <div class="delete-icon">!</div>

        <h2>Hapus Loker?</h2>

        <p>
            Apakah kamu yakin ingin menghapus loker
            <strong id="namaLokerHapus"></strong>?
        </p>

        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="tutupHapus()">Batal</button>
            <a href="#" id="linkHapus" class="btn-delete-confirm">Ya, Hapus</a>
        </div>

    </div>
</div>


<!-- JAVASCRIPT -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
<script src="script.js"></script>

</body>
</html>