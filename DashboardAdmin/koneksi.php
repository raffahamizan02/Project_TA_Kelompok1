<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Data session default admin jika belum login
if (!isset($_SESSION['id_user'])) {
    $_SESSION['id_user']      = 1;
    $_SESSION['username']     = 'admin';
    $_SESSION['nama_lengkap'] = 'Admin TU';
    $_SESSION['role']         = 'admin';
}

$host     = "localhost";
$username = "root";
$password = "";
$database = "db_kel1";

$koneksi = @mysqli_connect($host, $username, $password, $database);

// Data cadangan jika tabel di database masih kosong
$mock_loker = [
    ['id_loker' => 1,  'nomor_loker' => 'C01', 'lokasi' => 'Gedung A Lantai 1', 'kapasitas' => 5, 'terisi' => 3, 'status' => 'Terisi'],
    ['id_loker' => 2,  'nomor_loker' => 'C02', 'lokasi' => 'Gedung A Lantai 1', 'kapasitas' => 5, 'terisi' => 2, 'status' => 'Terisi'],
    ['id_loker' => 3,  'nomor_loker' => 'C03', 'lokasi' => 'Gedung A Lantai 1', 'kapasitas' => 5, 'terisi' => 0, 'status' => 'Kosong'],
    ['id_loker' => 4,  'nomor_loker' => 'C04', 'lokasi' => 'Gedung A Lantai 1', 'kapasitas' => 5, 'terisi' => 4, 'status' => 'Menunggu Verifikasi'],
    ['id_loker' => 5,  'nomor_loker' => 'C05', 'lokasi' => 'Gedung A Lantai 1', 'kapasitas' => 5, 'terisi' => 1, 'status' => 'Terisi'],
    ['id_loker' => 6,  'nomor_loker' => 'C06', 'lokasi' => 'Gedung A Lantai 1', 'kapasitas' => 5, 'terisi' => 0, 'status' => 'Kosong'],
    ['id_loker' => 7,  'nomor_loker' => 'C07', 'lokasi' => 'Gedung A Lantai 1', 'kapasitas' => 5, 'terisi' => 5, 'status' => 'Penuh'],
    ['id_loker' => 8,  'nomor_loker' => 'C08', 'lokasi' => 'Gedung A Lantai 1', 'kapasitas' => 5, 'terisi' => 2, 'status' => 'Terisi'],
    ['id_loker' => 9,  'nomor_loker' => 'C09', 'lokasi' => 'Gedung A Lantai 1', 'kapasitas' => 5, 'terisi' => 3, 'status' => 'Terisi'],
    ['id_loker' => 10, 'nomor_loker' => 'C10', 'lokasi' => 'Gedung A Lantai 1', 'kapasitas' => 5, 'terisi' => 1, 'status' => 'Terisi'],
    ['id_loker' => 11, 'nomor_loker' => 'C11', 'lokasi' => 'Gedung A Lantai 1', 'kapasitas' => 5, 'terisi' => 2, 'status' => 'Terisi'],
    ['id_loker' => 12, 'nomor_loker' => 'C12', 'lokasi' => 'Gedung A Lantai 1', 'kapasitas' => 5, 'terisi' => 3, 'status' => 'Menunggu Verifikasi'],
    ['id_loker' => 13, 'nomor_loker' => 'C13', 'lokasi' => 'Gedung A Lantai 1', 'kapasitas' => 5, 'terisi' => 0, 'status' => 'Kosong'],
    ['id_loker' => 14, 'nomor_loker' => 'C14', 'lokasi' => 'Gedung A Lantai 1', 'kapasitas' => 5, 'terisi' => 0, 'status' => 'Kosong'],
    ['id_loker' => 15, 'nomor_loker' => 'C15', 'lokasi' => 'Gedung A Lantai 1', 'kapasitas' => 5, 'terisi' => 1, 'status' => 'Terisi'],
    ['id_loker' => 16, 'nomor_loker' => 'C16', 'lokasi' => 'Gedung A Lantai 1', 'kapasitas' => 5, 'terisi' => 5, 'status' => 'Penuh'],
];

$mock_barang = [
    ['id_barang' => 1, 'kode_barang' => 'C04-0012', 'nama_barang' => 'Jaket Olahraga Parasut', 'nama_siswa' => 'Ahmad Rizki', 'nis_siswa' => '2024101', 'nomor_loker' => 'C04', 'lokasi' => 'Gedung A Lantai 1', 'foto_barang' => '', 'status' => 'Tersimpan', 'waktu_penitipan' => '2026-10-17 07:15:00', 'waktu_pengambilan' => null],
    ['id_barang' => 2, 'kode_barang' => 'C01-0005', 'nama_barang' => 'Helm Bogo Hitam Doff', 'nama_siswa' => 'Siti Nurhaliza', 'nis_siswa' => '2024102', 'nomor_loker' => 'C01', 'lokasi' => 'Gedung A Lantai 1', 'foto_barang' => '', 'status' => 'Tersimpan', 'waktu_penitipan' => '2026-10-17 07:22:00', 'waktu_pengambilan' => null],
    ['id_barang' => 3, 'kode_barang' => 'C07-0019', 'nama_barang' => 'Laptop Lenovo Thinkpad', 'nama_siswa' => 'Budi Santoso', 'nis_siswa' => '2024103', 'nomor_loker' => 'C07', 'lokasi' => 'Gedung A Lantai 1', 'foto_barang' => '', 'status' => 'Tersimpan', 'waktu_penitipan' => '2026-10-17 07:35:00', 'waktu_pengambilan' => null],
    ['id_barang' => 4, 'kode_barang' => 'C02-0008', 'nama_barang' => 'Tas Ransel Eiger Biru', 'nama_siswa' => 'Dewi Lestari', 'nis_siswa' => '2024104', 'nomor_loker' => 'C02', 'lokasi' => 'Gedung A Lantai 1', 'foto_barang' => '', 'status' => 'Sudah diambil', 'waktu_penitipan' => '2026-10-16 07:10:00', 'waktu_pengambilan' => '2026-10-16 15:45:00'],
    ['id_barang' => 5, 'kode_barang' => 'C05-0014', 'nama_barang' => 'Sepatu Futsal Specs', 'nama_siswa' => 'Fajar Pratama', 'nis_siswa' => '2024105', 'nomor_loker' => 'C05', 'lokasi' => 'Gedung A Lantai 1', 'foto_barang' => '', 'status' => 'Sudah diambil', 'waktu_penitipan' => '2026-10-16 08:00:00', 'waktu_pengambilan' => '2026-10-16 16:10:00'],
    ['id_barang' => 6, 'kode_barang' => 'C12-0021', 'nama_barang' => 'Botol Minum Tupperware', 'nama_siswa' => 'Rina Gunawan', 'nis_siswa' => '2024106', 'nomor_loker' => 'C12', 'lokasi' => 'Gedung A Lantai 1', 'foto_barang' => '', 'status' => 'Tersimpan', 'waktu_penitipan' => '2026-10-17 08:30:00', 'waktu_pengambilan' => null]
];

$mock_log = [
    ['id_log' => 1, 'nama_admin' => 'Admin TU', 'aksi' => 'Ubah', 'barang' => 'Tas Ransel Eiger Biru (C02-0008)', 'keterangan' => 'Mengubah status barang menjadi Sudah diambil', 'waktu' => '2026-10-16 15:45:10'],
    ['id_log' => 2, 'nama_admin' => 'Admin TU', 'aksi' => 'Ubah', 'barang' => 'Sepatu Futsal Specs (C05-0014)', 'keterangan' => 'Mengubah status barang menjadi Sudah diambil', 'waktu' => '2026-10-16 16:10:22'],
    ['id_log' => 3, 'nama_admin' => 'Admin TU', 'aksi' => 'Tambah', 'barang' => 'Jaket Olahraga Parasut (C04-0012)', 'keterangan' => 'Pencatatan penitipan barang siswa ke loker C04', 'waktu' => '2026-10-17 07:15:40'],
    ['id_log' => 4, 'nama_admin' => 'Admin TU', 'aksi' => 'Tambah', 'barang' => 'Helm Bogo Hitam Doff (C01-0005)', 'keterangan' => 'Pencatatan penitipan barang siswa ke loker C01', 'waktu' => '2026-10-17 07:22:15'],
    ['id_log' => 5, 'nama_admin' => 'Admin TU', 'aksi' => 'Tambah', 'barang' => 'Laptop Lenovo Thinkpad (C07-0019)', 'keterangan' => 'Pencatatan penitipan barang siswa ke loker C07 (Loker Penuh)', 'waktu' => '2026-10-17 07:35:50']
];

$mock_grafik = [
    ['hari' => 'Senin',  'jumlah' => 32, 'tinggi' => '55%'],
    ['hari' => 'Selasa', 'jumlah' => 45, 'tinggi' => '75%'],
    ['hari' => 'Rabu',   'jumlah' => 28, 'tinggi' => '48%'],
    ['hari' => 'Kamis',  'jumlah' => 38, 'tinggi' => '65%'],
    ['hari' => 'Jumat',  'jumlah' => 22, 'tinggi' => '38%'],
    ['hari' => 'Sabtu',  'jumlah' => 58, 'tinggi' => '100%']
];

// Fungsi menghitung jumlah statistik loker dan barang
function ambilStatistik($koneksi, $mock_loker, $mock_barang) {
    if ($koneksi) {
        $qTotalLoker = mysqli_query($koneksi, "SELECT id_loker FROM loker");
        if ($qTotalLoker && mysqli_num_rows($qTotalLoker) > 0) {
            return [
                'total_loker'      => mysqli_num_rows($qTotalLoker),
                'loker_kosong'     => mysqli_num_rows(mysqli_query($koneksi, "SELECT id_loker FROM loker WHERE status = 'Kosong'")),
                'loker_terisi'     => mysqli_num_rows(mysqli_query($koneksi, "SELECT id_loker FROM loker WHERE status = 'Terisi'")),
                'loker_penuh'      => mysqli_num_rows(mysqli_query($koneksi, "SELECT id_loker FROM loker WHERE status = 'Penuh'")),
                'barang_tersimpan' => mysqli_num_rows(mysqli_query($koneksi, "SELECT id_barang FROM barang WHERE status = 'Tersimpan'")),
                'barang_diambil'   => mysqli_num_rows(mysqli_query($koneksi, "SELECT id_barang FROM barang WHERE status = 'Sudah diambil'"))
            ];
        }
    }

    // Jika database kosong, hitung dari data simulasi (mock)
    $kosong = 0; $terisi = 0; $penuh = 0;
    foreach ($mock_loker as $l) {
        if ($l['status'] === 'Kosong') $kosong++;
        elseif ($l['status'] === 'Penuh') $penuh++;
        else $terisi++;
    }

    return [
        'total_loker'      => count($mock_loker),
        'loker_kosong'     => $kosong,
        'loker_terisi'     => $terisi,
        'loker_penuh'      => $penuh,
        'barang_tersimpan' => 186,
        'barang_diambil'   => 1284
    ];
}

// Fungsi mengambil daftar seluruh loker
function ambilDaftarLoker($koneksi, $mock_loker) {
    if ($koneksi) {
        $q = mysqli_query($koneksi, "SELECT * FROM loker ORDER BY nomor_loker ASC");
        if ($q && mysqli_num_rows($q) > 0) {
            $data = [];
            while ($r = mysqli_fetch_assoc($q)) {
                $data[] = $r;
            }
            return $data;
        }
    }
    return $mock_loker;
}

// Fungsi mengambil daftar barang
function ambilDaftarBarang($koneksi, $mock_barang, $cari = '', $filter_status = '') {
    if ($koneksi) {
        $kondisi = "WHERE 1=1";
        if ($cari !== '') {
            $c = mysqli_real_escape_string($koneksi, $cari);
            $kondisi .= " AND (b.nama_barang LIKE '%$c%' OR b.kode_barang LIKE '%$c%' OR u.nama_lengkap LIKE '%$c%')";
        }
        if ($filter_status !== '') {
            $s = mysqli_real_escape_string($koneksi, $filter_status);
            $kondisi .= " AND b.status = '$s'";
        }

        $sql = "SELECT b.*, u.nama_lengkap AS nama_siswa, u.username AS nis_siswa, l.nomor_loker, l.lokasi 
                FROM barang b 
                LEFT JOIN users u ON b.id_siswa = u.id_user 
                LEFT JOIN loker l ON b.id_loker = l.id_loker 
                $kondisi ORDER BY b.id_barang DESC";
        $q = mysqli_query($koneksi, $sql);

        if ($q && mysqli_num_rows($q) > 0) {
            $data = [];
            while ($r = mysqli_fetch_assoc($q)) {
                $data[] = $r;
            }
            return $data;
        }
    }

    // Filter pada data simulasi jika database kosong
    $hasil = [];
    foreach ($mock_barang as $item) {
        $teks_gabung = strtolower($item['nama_barang'] . ' ' . $item['kode_barang'] . ' ' . $item['nama_siswa']);
        $cocok_cari = ($cari === '' || strpos($teks_gabung, strtolower($cari)) !== false);
        $cocok_status = ($filter_status === '' || $item['status'] === $filter_status);

        if ($cocok_cari && $cocok_status) {
            $hasil[] = $item;
        }
    }
    return $hasil;
}

// Fungsi mengambil riwayat log aktivitas
function ambilLogAktivitas($koneksi, $mock_log, $filter_aksi = '', $filter_tanggal = '') {
    if ($koneksi) {
        $kondisi = "WHERE 1=1";
        if ($filter_aksi !== '') {
            $a = mysqli_real_escape_string($koneksi, $filter_aksi);
            $kondisi .= " AND aksi = '$a'";
        }
        if ($filter_tanggal !== '') {
            $t = mysqli_real_escape_string($koneksi, $filter_tanggal);
            $kondisi .= " AND DATE(waktu) = '$t'";
        }

        $q = mysqli_query($koneksi, "SELECT * FROM log_aktivitas $kondisi ORDER BY waktu DESC");
        if ($q && mysqli_num_rows($q) > 0) {
            $data = [];
            while ($r = mysqli_fetch_assoc($q)) {
                $data[] = $r;
            }
            return $data;
        }
    }

    $hasil = [];
    foreach ($mock_log as $l) {
        $cocok_aksi = ($filter_aksi === '' || $l['aksi'] === $filter_aksi);
        $cocok_tgl  = ($filter_tanggal === '' || substr($l['waktu'], 0, 10) === $filter_tanggal);

        if ($cocok_aksi && $cocok_tgl) {
            $hasil[] = $l;
        }
    }
    return $hasil;
}
?>