<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once "connectBarang.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$queryLoker = mysqli_query($koneksi, "SELECT id_loker, nomor_loker, lokasi FROM loker ORDER BY nomor_loker ASC");
$querySiswa = mysqli_query($koneksi, "SELECT id_user, nama_lengkap, username FROM users WHERE role = 'siswa' ORDER BY nama_lengkap ASC");
$id_siswa_login = $_SESSION['id_user'] ?? '';

$daftarBarang = ["Tas", "Jaket", "Helm", "Laptop", "Sepatu", "Buku", "Botol", "Lainnya"
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Barang</title>
    <link rel="stylesheet" href="styleBarang.css?v=<?php echo time(); ?>">
    <style>
        <?php include __DIR__ . '/styleBarang.css'; ?>
    </style>
</head>
<body>

<div class="container">
    <div class="header-box">
        <div>
            <h2>Tambah Penitipan Barang</h2>
            <p>Formulir pencatatan barang siswa yang dititipkan di loker</p>
        </div>
        <div>
            <a href="indexBarang.php" class="btn btn-secondary">Kembali</a>
        </div>
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
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="barang-item-box">
                            <label>Barang <?= $i ?> (Wajib):</label>
                            <select name="pilihan_barang[]" class="form-control" onchange="ubahPilihan(this)" required>
                                <option value="">Pilih Barang</option>
                                <?php foreach ($daftarBarang as $val => $label): ?>
                                    <option value="<?= $val ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" name="nama_barang_custom[]" class="form-control input-custom" placeholder="Ketik nama barang..." oninput="validasiSatuCustom(this)">
                            <small class="pesan-error-custom" style="color: #B8473E; font-size: 11px; display: none; margin-top: 4px; font-weight: bold;"></small>
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
const kategoriStandar = <?= json_encode(array_keys(array_diff_key($daftarBarang, ['Lainnya' => '']))) ?>;

function ubahPilihan(select) {
    cekLainnya(select);
    perbaruiOpsiDropdown();
    validasiSemuaBarang();
}

function cekLainnya(select) {
    const box = select.closest('.barang-item-box');
    const input = box.querySelector('.input-custom');
    const errorMsg = box.querySelector('.pesan-error-custom');
    const isLainnya = select.value === 'Lainnya';

    input.style.display = isLainnya ? 'block' : 'none';
    input.required = isLainnya;

    if (isLainnya) {
        input.focus();
        validasiSatuCustom(input);
    } else {
        input.value = '';
        input.style.borderColor = '';
        if (errorMsg) {
            errorMsg.textContent = '';
            errorMsg.style.display = 'none';
        }
    }
}

function perbaruiOpsiDropdown() {
    const semuaSelect = document.querySelectorAll('select[name="pilihan_barang[]"]');
    
    const nilaiTerpilih = [];
    semuaSelect.forEach(s => {
        if (s.value && s.value !== 'Lainnya') {
            nilaiTerpilih.push(s.value);
        }
    });

    semuaSelect.forEach(s => {
        const nilaiAktif = s.value;
        Array.from(s.options).forEach(opt => {
            if (!opt.value || opt.value === 'Lainnya') {
                opt.hidden = false;
                opt.disabled = false;
                opt.style.display = '';
                return;
            }

            if (opt.value === nilaiAktif) {
                opt.hidden = false;
                opt.disabled = false;
                opt.style.display = '';
            } 
            else if (nilaiTerpilih.includes(opt.value)) {
                opt.hidden = true;
                opt.disabled = true;
                opt.style.display = 'none';
            } 
            else {
                opt.hidden = false;
                opt.disabled = false;
                opt.style.display = '';
            }
        });
    });
}

function validasiKarakter(teks) {
    teks = teks.trim();
    if (!teks) {
        return { valid: false, pesan: "Nama barang wajib diisi jika memilih opsi 'Lainnya'." };
    }
    if (teks.length < 3) {
        return { valid: false, pesan: "Nama barang terlalu pendek, minimal 3 karakter." };
    }
    if (teks.length > 50) {
        return { valid: false, pesan: "Nama barang terlalu panjang, maksimal 50 karakter." };
    }

    if (!/^[a-zA-Z0-9\s\-]+$/.test(teks)) {
        return { valid: false, pesan: "Tidak boleh mengandung simbol atau karakter aneh (hanya huruf, angka, dan spasi)." };
    }

    const huruf = teks.match(/[a-zA-Z]/g);
    if (!huruf || huruf.length < 3) {
        return { valid: false, pesan: "Nama barang tidak boleh hanya angka atau simbol, wajib mengandung minimal 3 huruf alfabet." };
    }

    if (/(.)\1{2,}/i.test(teks)) {
        return { valid: false, pesan: "Nama barang mengandung pengulangan huruf yang tidak wajar." };
    }

    return { valid: true };
}

function cekKemiripanNama(namaBaru, daftarBanding) {
    const n1 = normalisasiTeks(namaBaru);
    if (!n1) return null;

    const kata1 = n1.split(' ').filter(k => k.length >= 3);

    for (let item of daftarBanding) {
        const n2 = normalisasiTeks(item);
        if (!n2) continue;

        if (n1 === n2) {
            return `Nama barang tidak boleh sama dengan "${item}".`;
        }

        if (n1.length >= 3 && n2.length >= 3) {
            if (n1.includes(n2) || n2.includes(n1)) {
                return `Nama barang terlalu mirip dengan barang "${item}".`;
            }
        }

        const kata2 = n2.split(' ').filter(k => k.length >= 3);
        for (let k1 of kata1) {
            for (let k2 of kata2) {
                if (k1 === k2) {
                    return `Nama barang mengandung kata "${k1}" yang mirip dengan "${item}".`;
                }
                if (levenshteinDistance(k1, k2) <= 1 && Math.min(k1.length, k2.length) >= 4) {
                    return `Nama barang sangat mirip dengan "${item}".`;
                }
            }
        }

        if (levenshteinDistance(n1, n2) <= 2 && Math.min(n1.length, n2.length) >= 4) {
            return `Nama barang sangat mirip dengan "${item}".`;
        }
    }

    return null;
}

function normalisasiTeks(str) {
    return str.toLowerCase().replace(/[^a-z0-9\s]/g, ' ').replace(/\s+/g, ' ').trim();
}

function levenshteinDistance(a, b) {
    const m = [];
    for (let i = 0; i <= b.length; i++) m[i] = [i];
    for (let j = 0; j <= a.length; j++) m[0][j] = j;
    for (let i = 1; i <= b.length; i++) {
        for (let j = 1; j <= a.length; j++) {
            m[i][j] = b.charAt(i - 1) === a.charAt(j - 1)
                ? m[i - 1][j - 1]
                : Math.min(m[i - 1][j - 1] + 1, m[i][j - 1] + 1, m[i - 1][j] + 1);
        }
    }
    return m[b.length][a.length];
}

function validasiSatuCustom(input) {
    const box = input.closest('.barang-item-box');
    const select = box.querySelector('select');
    const errorMsg = box.querySelector('.pesan-error-custom');

    if (select.value !== 'Lainnya') {
        input.style.borderColor = '';
        if (errorMsg) {
            errorMsg.textContent = '';
            errorMsg.style.display = 'none';
        }
        return true;
    }

    const nilai = input.value.trim();

    const cekKarakter = validasiKarakter(nilai);
    if (!cekKarakter.valid) {
        input.style.borderColor = '#B8473E';
        if (errorMsg) {
            errorMsg.textContent = cekKarakter.pesan;
            errorMsg.style.display = 'block';
        }
        return false;
    }

    const daftarBanding = [...kategoriStandar];
    const semuaBox = document.querySelectorAll('.barang-item-box');
    semuaBox.forEach(b => {
        if (b !== box) {
            const s = b.querySelector('select');
            const inp = b.querySelector('.input-custom');
            if (s.value === 'Lainnya' && inp.value.trim()) {
                daftarBanding.push(inp.value.trim());
            } else if (s.value && s.value !== 'Lainnya') {
                daftarBanding.push(s.value);
            }
        }
    });

    const cekMirip = cekKemiripanNama(nilai, daftarBanding);
    if (cekMirip) {
        input.style.borderColor = '#B8473E';
        if (errorMsg) {
            errorMsg.textContent = cekMirip;
            errorMsg.style.display = 'block';
        }
        return false;
    }

    input.style.borderColor = '#181715';
    if (errorMsg) {
        errorMsg.textContent = '';
        errorMsg.style.display = 'none';
    }
    return true;
}

function validasiSemuaBarang() {
    let pesanError = null;
    const semuaBox = document.querySelectorAll('.barang-item-box');

    for (let box of semuaBox) {
        const select = box.querySelector('select');
        const input = box.querySelector('.input-custom');

        if (select.value === 'Lainnya') {
            const valid = validasiSatuCustom(input);
            if (!valid && !pesanError) {
                const errorMsg = box.querySelector('.pesan-error-custom');
                pesanError = errorMsg ? errorMsg.textContent : 'Terdapat kesalahan pada input nama barang.';
                input.focus();
            }
        }
    }
    return pesanError;
}

function tambahBarang() {
    const container = document.getElementById('daftarBarang');
    const box = container.firstElementChild.cloneNode(true);
    const select = box.querySelector('select');
    const input = box.querySelector('.input-custom');
    const errorMsg = box.querySelector('.pesan-error-custom');

    box.querySelector('label').outerHTML = `
        <div class="toolbar" style="margin-bottom: 5px;">
            <label class="judul-tambahan" style="margin: 0;"></label>
            <button type="button" class="btn btn-danger btn-sm" onclick="hapusBarang(this)">Hapus</button>
        </div>`;

    select.required = false;
    select.value = '';
    select.options[0].text = 'Pilih Barang (Boleh Kosong)';
    input.value = '';
    input.style.borderColor = '';
    if (errorMsg) {
        errorMsg.textContent = '';
        errorMsg.style.display = 'none';
    }

    container.appendChild(box);
    cekLainnya(select);
    urutkanNomor();
    perbaruiOpsiDropdown();
}

function hapusBarang(btn) {
    btn.closest('.barang-item-box').remove();
    urutkanNomor();
    perbaruiOpsiDropdown();
    validasiSemuaBarang();
}

function urutkanNomor() {
    document.querySelectorAll('.judul-tambahan').forEach((el, idx) => {
        el.textContent = `Barang ${idx + 4} (Tambahan):`;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    perbaruiOpsiDropdown();

    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const error = validasiSemuaBarang();
            if (error) {
                e.preventDefault();
                alert('Peringatan Validasi: ' + error);
            }
        });
    }
});
</script>

</body>
</html>
