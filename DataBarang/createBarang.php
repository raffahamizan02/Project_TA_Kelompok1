<?php
include "connectBarang.php";

$queryLoker = mysqli_query($koneksi, "SELECT * FROM loker ORDER BY nomor_loker ASC");

$querySiswa = mysqli_query($koneksi, "SELECT id_user, nama_lengkap, username FROM users WHERE role = 'siswa' ORDER BY nama_lengkap ASC");

$id_siswa_login = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Barang - Penitipan Loker</title>
    <link rel="stylesheet" href="styleBarang.css">
</head>
<body>

<div class="container">

    <div class="header-box">
        <div>
            <h1>Tambah Penitipan Barang</h1>
            <p>Masukkan data barang bawaan siswa yang akan dititipkan ke dalam loker</p>
        </div>
        <a href="indexBarang.php" class="btn btn-secondary">← Kembali ke Daftar</a>
    </div>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'error'): ?>
        <div class="alert alert-danger">
            <strong>Gagal Menyimpan:</strong> <?php echo htmlspecialchars($_GET['teks'] ?? 'Data tidak lengkap atau format tidak sesuai.'); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="saveBarang.php" method="POST" enctype="multipart/form-data" id="formTambahBarang">

            <div class="form-group">
                <label for="id_siswa">Siswa Pemilik Barang:</label>
                <select name="id_siswa" id="id_siswa" class="form-control" required>
                    <option value="">-- Pilih Siswa Penitip --</option>
                    <?php while ($siswa = mysqli_fetch_assoc($querySiswa)): ?>
                        <option value="<?php echo $siswa['id_user']; ?>" <?php echo ($id_siswa_login == $siswa['id_user']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($siswa['nama_lengkap']); ?> (NIS/Username: <?php echo htmlspecialchars($siswa['username']); ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_loker">Loker Tujuan:</label>
                <select name="id_loker" id="id_loker" class="form-control" required>
                    <option value="">-- Pilih Loker --</option>
                    <?php while ($loker = mysqli_fetch_assoc($queryLoker)): ?>
                        <option value="<?php echo $loker['id_loker']; ?>">
                            Loker <?php echo htmlspecialchars($loker['nomor_loker']); ?> - Lokasi: <?php echo htmlspecialchars($loker['lokasi']); ?> (Status: <?php echo htmlspecialchars($loker['status']); ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="pilihan_barang">Barang yang Dititipkan:</label>
                <select name="pilihan_barang" id="pilihan_barang" class="form-control" required onchange="cekPilihanBarang()">
                    <option value="">-- Pilih Nama Barang --</option>
                    <option value="Tas Ransel">Tas Ransel</option>
                    <option value="Jaket / Hoodie">Jaket / Hoodie</option>
                    <option value="Helm Sepeda Motor">Helm</option>
                    <option value="Laptop / Perangkat Elektronik">Laptop / Perangkat Elektronik</option>
                    <option value="Sepatu / Sandal">Sepatu / Sandal</option>
                    <option value="Buku / Dokumen">Buku Tulis / Buku Paket</option>
                    <option value="Botol Minum / Tumbler">Botol Minum</option>
                    <option value="Lainnya">Lainnya (Ketik Sendiri)</option>
                </select>
            </div>

            <div class="form-group" id="boxBarangCustom" style="display: none;">
                <label for="nama_barang_custom">Nama Barang Custom: <small style="color: #dc2626;">*Wajib diisi jika memilih Lainnya</small></label>
                <input type="text" name="nama_barang_custom" id="nama_barang_custom" class="form-control" maxlength="100" placeholder="Ketik nama barang yang dibawa siswa...">
            </div>

            <div class="form-group">
                <label for="foto_barang">Foto Barang Bawaan:</label>
                <input type="file" name="foto_barang" id="foto_barang" class="form-control" accept="image/*" required onchange="previewFoto(event)">
                
                <div class="image-preview-box" id="previewBox">
                    <span id="previewText" style="color: #94a3b8; font-size: 13px;">Pratinjau foto barang akan muncul di sini</span>
                    <img id="previewImage" src="" alt="Pratinjau Foto" style="display: none;">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Data Barang</button>
                <a href="indexBarang.php" class="btn btn-secondary">Batal</a>
            </div>

        </form>
    </div>

</div>

<script>
function cekPilihanBarang() {
    var pilihan = document.getElementById("pilihan_barang").value;
    var boxCustom = document.getElementById("boxBarangCustom");
    var inputCustom = document.getElementById("nama_barang_custom");

    if (pilihan === "Lainnya") {
        boxCustom.style.display = "block";
        inputCustom.required = true;
        inputCustom.focus();
    } else {
        boxCustom.style.display = "none";
        inputCustom.required = false;
        inputCustom.value = "";
    }
}

function previewFoto(event) {
    var reader = new FileReader();
    reader.onload = function(){
        var output = document.getElementById('previewImage');
        var text = document.getElementById('previewText');
        output.src = reader.result;
        output.style.display = 'block';
        text.style.display = 'none';
    };
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}
</script>

</body>
</html>