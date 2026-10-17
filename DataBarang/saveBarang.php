<?php
include "connectBarang.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: indexBarang.php");
    exit();
}

$id_siswa = isset($_POST['id_siswa']) ? (int) $_POST['id_siswa'] : 0;
$id_loker = isset($_POST['id_loker']) ? (int) $_POST['id_loker'] : 0;

if ($id_siswa <= 0 || $id_loker <= 0) {
    header("Location: createBarang.php?pesan=error&teks=" . urlencode("Siswa dan Loker wajib dipilih."));
    exit();
}

$pilihan_barang = $_POST['pilihan_barang'] ?? [];
$custom_barang  = $_POST['nama_barang_custom'] ?? [];

$daftar_barang = [];
for ($i = 0; $i < count($pilihan_barang); $i++) {
    $pilihan = trim($pilihan_barang[$i]);
    $custom  = trim($custom_barang[$i] ?? '');

    if ($pilihan === 'Lainnya') {
        if (!empty($custom)) {
            $daftar_barang[] = substr($custom, 0, 100);
        } else {
            header("Location: createBarang.php?pesan=error&teks=" . urlencode("Nama barang custom wajib diisi jika memilih opsi 'Lainnya'."));
            exit();
        }
    } elseif (!empty($pilihan)) {
        $daftar_barang[] = substr($pilihan, 0, 100);
    }
}

if (count($daftar_barang) < 3) {
    header("Location: createBarang.php?pesan=error&teks=" . urlencode("Siswa harus memasukkan minimal 3 barang yang dititipkan."));
    exit();
}

$barang_tercatat = [];
foreach ($daftar_barang as $nama_item) {
    $nama_item = trim($nama_item);

    
    if (strlen($nama_item) < 3 || strlen($nama_item) > 50) {
        header("Location: createBarang.php?pesan=error&teks=" . urlencode("Nama barang '$nama_item' harus antara 3 sampai 50 karakter."));
        exit();
    }

    
    if (!preg_match('/^[a-zA-Z0-9\s\-]+$/', $nama_item)) {
        header("Location: createBarang.php?pesan=error&teks=" . urlencode("Nama barang '$nama_item' tidak boleh mengandung simbol atau karakter aneh."));
        exit();
    }

    
    preg_match_all('/[a-zA-Z]/', $nama_item, $huruf_matches);
    if (empty($huruf_matches[0]) || count($huruf_matches[0]) < 3) {
        header("Location: createBarang.php?pesan=error&teks=" . urlencode("Nama barang '$nama_item' tidak boleh hanya angka atau simbol, minimal harus 3 huruf alfabet."));
        exit();
    }

    
    if (preg_match('/(.)\1{2,}/i', $nama_item)) {
        header("Location: createBarang.php?pesan=error&teks=" . urlencode("Nama barang '$nama_item' mengandung pengulangan huruf yang tidak wajar."));
        exit();
    }

    
    $nama_bersih = strtolower(trim(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $nama_item)));
    $kata_nama = array_filter(explode(' ', $nama_bersih), function($k) { return strlen($k) >= 3; });

    foreach ($barang_tercatat as $item_sebelumnya) {
        $sebelumnya_bersih = strtolower(trim(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $item_sebelumnya)));
        $kata_sebelumnya = array_filter(explode(' ', $sebelumnya_bersih), function($k) { return strlen($k) >= 3; });

        
        if ($nama_bersih === $sebelumnya_bersih) {
            header("Location: createBarang.php?pesan=error&teks=" . urlencode("Tidak boleh memasukkan barang yang sama: '$nama_item'."));
            exit();
        }

        
        if (strlen($nama_bersih) >= 3 && strlen($sebelumnya_bersih) >= 3) {
            if (strpos($nama_bersih, $sebelumnya_bersih) !== false || strpos($sebelumnya_bersih, $nama_bersih) !== false) {
                header("Location: createBarang.php?pesan=error&teks=" . urlencode("Nama barang '$nama_item' terlalu mirip dengan '$item_sebelumnya'."));
                exit();
            }
        }

        
        foreach ($kata_nama as $k1) {
            foreach ($kata_sebelumnya as $k2) {
                if ($k1 === $k2) {
                    header("Location: createBarang.php?pesan=error&teks=" . urlencode("Nama barang '$nama_item' mengandung kata '$k1' yang mirip dengan '$item_sebelumnya'."));
                    exit();
                }
                if (levenshtein($k1, $k2) <= 1 && min(strlen($k1), strlen($k2)) >= 4) {
                    header("Location: createBarang.php?pesan=error&teks=" . urlencode("Nama barang '$nama_item' sangat mirip dengan '$item_sebelumnya'."));
                    exit();
                }
            }
        }

        
        if (levenshtein($nama_bersih, $sebelumnya_bersih) <= 2 && min(strlen($nama_bersih), strlen($sebelumnya_bersih)) >= 4) {
            header("Location: createBarang.php?pesan=error&teks=" . urlencode("Nama barang '$nama_item' sangat mirip dengan '$item_sebelumnya'."));
            exit();
        }
    }

    $barang_tercatat[] = $nama_item;
}

if (!isset($_FILES['foto_barang']) || $_FILES['foto_barang']['error'] !== 0) {
    header("Location: createBarang.php?pesan=error&teks=" . urlencode("Foto barang wajib diunggah sebagai bukti fisik."));
    exit();
}

$file_tmp  = $_FILES['foto_barang']['tmp_name'];
$file_nama = $_FILES['foto_barang']['name'];
$ekstensi  = strtolower(pathinfo($file_nama, PATHINFO_EXTENSION));

$ekstensi_boleh = ['jpg', 'jpeg', 'png', 'webp'];
if (!in_array($ekstensi, $ekstensi_boleh)) {
    header("Location: createBarang.php?pesan=error&teks=" . urlencode("Format foto harus JPG, JPEG, atau PNG."));
    exit();
}

$folder_upload = __DIR__ . '/uploads/';
if (!is_dir($folder_upload)) {
    mkdir($folder_upload, 0777, true);
}

$nama_file_foto = 'foto_' . time() . '_' . rand(100, 999) . '.' . $ekstensi;
$tujuan_upload  = $folder_upload . $nama_file_foto;

if (!move_uploaded_file($file_tmp, $tujuan_upload)) {
    header("Location: createBarang.php?pesan=error&teks=" . urlencode("Gagal mengupload file foto ke server."));
    exit();
}

$qLoker = mysqli_query($koneksi, "SELECT nomor_loker FROM loker WHERE id_loker = '$id_loker' LIMIT 1");
$dLoker = mysqli_fetch_assoc($qLoker);
$nomor_loker = $dLoker ? $dLoker['nomor_loker'] : 'LK';

foreach ($daftar_barang as $nama_item) {
    $nama_item_bersih = mysqli_real_escape_string($koneksi, $nama_item);

    $qHitung = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM barang WHERE id_loker = '$id_loker'");
    $dHitung = mysqli_fetch_assoc($qHitung);
    $urutan  = (int) $dHitung['total'] + 1;

    $kode_barang = $nomor_loker . '-' . sprintf("%04d", $urutan);

    $cek = mysqli_query($koneksi, "SELECT id_barang FROM barang WHERE kode_barang = '$kode_barang'");
    while (mysqli_num_rows($cek) > 0) {
        $urutan++;
        $kode_barang = $nomor_loker . '-' . sprintf("%04d", $urutan);
        $cek = mysqli_query($koneksi, "SELECT id_barang FROM barang WHERE kode_barang = '$kode_barang'");
    }

    mysqli_query($koneksi, "INSERT INTO barang 
        (kode_barang, id_siswa, id_loker, nama_barang, foto_barang, waktu_penitipan, status, waktu_pengambilan) 
        VALUES 
        ('$kode_barang', '$id_siswa', '$id_loker', '$nama_item_bersih', '$nama_file_foto', NOW(), 'Tersimpan', NULL)");
}

mysqli_query($koneksi, "UPDATE loker SET status = 'Terisi' WHERE id_loker = '$id_loker'");

header("Location: indexBarang.php?pesan=sukses_tambah");
exit();
?>
