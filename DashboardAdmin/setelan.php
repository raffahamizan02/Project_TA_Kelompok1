<?php
require_once "koneksi.php";

$halaman_aktif = 'setelan';
$pesan_sukses  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_baru = trim($_POST['nama_lengkap'] ?? '');
    if ($nama_baru !== '') {
        $_SESSION['nama_lengkap'] = $nama_baru;
    }
    $pesan_sukses = 'Pengaturan berhasil disimpan.';
}

$nama_admin = $_SESSION['nama_lengkap'] ?? 'Admin TU';
$username   = $_SESSION['username'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - LokerKu</title>
    <link rel="stylesheet" href="styleAdmin.css">
</head>
<body>

<div class="layout">
    <?php include "sidebar.php"; ?>

    <main class="content">
        <div class="topbar">
            <div>
                <p class="welcome">Panel Tata Usaha</p>
                <h1>Pengaturan Sistem</h1>
            </div>
            <div class="profile">
                <div class="profile-icon">TU</div>
                <div>
                    <strong><?= $nama_admin ?></strong>
                    <small>Tata Usaha</small>
                </div>
            </div>
        </div>

        <?php if ($pesan_sukses !== ''): ?>
            <div class="alert-box alert-sukses">
                <span><?= $pesan_sukses ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="setelan.php" style="max-width: 600px;">
            <div class="card">
                <div class="card-header">
                    <h3>Profil Admin</h3>
                </div>

                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: bold; color: #6E6A63; margin-bottom: 4px;">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" style="width: 100%;" value="<?= $nama_admin ?>" required>
                    </div>

                    <div>
                        <label style="display: block; font-size: 12px; font-weight: bold; color: #6E6A63; margin-bottom: 4px;">Username</label>
                        <input type="text" class="form-control" style="width: 100%; background-color: #F7F4EE;" value="<?= $username ?>" readonly>
                    </div>

                    <div>
                        <label style="display: block; font-size: 12px; font-weight: bold; color: #6E6A63; margin-bottom: 4px;">Hak Akses</label>
                        <input type="text" class="form-control" style="width: 100%; background-color: #F7F4EE;" value="Administrator (TU)" readonly>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Preferensi Notifikasi</h3>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="notif_penuh" checked style="accent-color: #E04F16;">
                        <span>Peringatan Loker Penuh di Beranda</span>
                    </label>

                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="notif_barang" checked style="accent-color: #E04F16;">
                        <span>Notifikasi Penitipan Barang Baru</span>
                    </label>

                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="notif_rusak" checked style="accent-color: #E04F16;">
                        <span>Peringatan Kondisi Fisik Barang</span>
                    </label>

                    <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid #E8E3D9;">
                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </div>
                </div>
            </div>
        </form>
    </main>
</div>

</body>
</html>