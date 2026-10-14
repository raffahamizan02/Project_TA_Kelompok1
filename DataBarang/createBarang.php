<?php
require_once "connectBarang.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$queryLoker = mysqli_query($koneksi, "SELECT id_loker, nomor_loker, lokasi FROM loker ORDER BY nomor_loker ASC");
$querySiswa = mysqli_query($koneksi, "SELECT id_user, nama_lengkap, username FROM users WHERE role = 'siswa' ORDER BY nama_lengkap ASC");
$id_siswa_login =$_SESSION['id_user'] ?? '';

$opsiBarang = [
    "Tas Ransel"  => "Tas Ransel",
    "Jaket"       => "Jaket / Hoodie",
    "Helm"        => "Helm",
    "Laptop"      => "Laptop / Gadget",
    "Sepatu"      => "Sepatu",
    "Buku"        => "Buku / Modul",
    "Botol Minum" => "Botol Minum",
    "Lainnya"     => "Lainnya (Ketik Sendiri)"
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Barang</title>
    <link rel="stylesheet" href="styleBarang.css">
</head>
<body>

<div class="container">
    <div class="header-box">
        <h2>Tambah Penitipan Barang</h2>
    </div>

    <?php if (($_GET['pesan'] ?? '') === 'error'): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_GET['teks'] ?? 'Terjadi kesalahan pada data input.') ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="saveBarang.php" method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label for="id_siswa">Siswa Pemilik:</label>
                <select name="id_siswa" id="id_siswa" class="form-control" required>
                    <option value="">Pilih Siswa</option>
                    <?php while ($s = mysqli_fetch_assoc($querySiswa)): ?>
                        <option value="<?= $s['id_user'] ?>" <?= ($id_siswa_login ==$s['id_user']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars("{$s['nama_lengkap']} (NIS: {$s['username']})") ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_loker">Loker Tujuan:</label>
                <select name="id_loker" id="id_loker" class="form-control" required>
                    <option value="">Pilih Loker</option>
                    <?php while ($l = mysqli_fetch_assoc($queryLoker)): ?>
                        <option value="<?= $l['id_loker'] ?>">
                            Loker <?= htmlspecialchars("{$l['nomor_loker']} - Lokasi: {$l['lokasi']}") ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="foto_barang">Foto Barang Bawaan:</label>
                <input type="file" name="foto_barang" id="foto_barang" class="form-control" accept="image/*" required>
            </div>

            <hr class="divider">

            <div class="form-group">
                <label>Daftar Barang Bawaan:</label>
                <div id="daftarBarang">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <div class="barang-item-box">
                            <label>Barang <?= $i ?> (Wajib):</label>
                            <select name="pilihan_barang[]" class="form-control" onchange="cekLainnya(this)" required>
                                <option value="">Pilih Barang</option>
                                <?php foreach ($opsiBarang as $val =>$label): ?>
                                    <option value="<?= $val ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" name="nama_barang_custom[]" class="form-control input-custom" placeholder="Ketik nama barang...">
                        </div>
                    <?php endfor; ?>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="tambahBarang()">+ Tambah Barang</button>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Data Barang</button>
                <a href="indexBarang.php" class="btn btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>

<script>
function cekLainnya(select) {
    const input = select.nextElementSibling;
    const isLainnya = select.value === 'Lainnya';
    input.style.display = isLainnya ? 'block' : 'none';
    input.required = isLainnya;
    if (isLainnya) input.focus();
    else input.value = '';
}

function tambahBarang() {
    const container = document.getElementById('daftarBarang');
    const box = container.firstElementChild.cloneNode(true);
    const select = box.querySelector('select');

    box.querySelector('label').outerHTML = `
        <div class="toolbar" style="margin-bottom: 5px;">
            <label class="judul-tambahan" style="margin: 0;"></label>
            <button type="button" class="btn btn-danger btn-sm" onclick="hapusBarang(this)">Hapus</button>
        </div>`;

    select.required = false;
    select.value = '';
    select.options[0].text = 'Pilih Barang (Boleh Kosong)';
    cekLainnya(select);

    container.appendChild(box);
    urutkanNomor();
}

function hapusBarang(btn) {
    btn.closest('.barang-item-box').remove();
    urutkanNomor();
}

function urutkanNomor() {
    document.querySelectorAll('.judul-tambahan').forEach((el, idx) => {
        el.textContent = `Barang ${idx + 6} (Tambahan):`;
    });
}
</script>

</body>
</html>