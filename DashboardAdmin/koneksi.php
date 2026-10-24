<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Data sesi default admin jika belum login
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

@mysqli_report(MYSQLI_REPORT_OFF);

$koneksi = false;
try {
    $koneksi = @mysqli_connect($host, $username, $password, $database);
    if (!$koneksi) {
        $koneksi = @mysqli_connect($host, "app_kel1", "D@t@6@53K3l1", $database);
    }
} catch (Throwable $e) {
    $koneksi = false;
}

// Data simulasi (digunakan jika database offline atau belum diisi)
$mock_loker = [
    ['id_loker' => 1,  'nomor_loker' => 'C01', 'lokasi' => 'Gedung A', 'kapasitas' => 5, 'terisi' => 3, 'status' => 'Terisi'],
    ['id_loker' => 2,  'nomor_loker' => 'C02', 'lokasi' => 'Gedung A', 'kapasitas' => 5, 'terisi' => 2, 'status' => 'Terisi'],
    ['id_loker' => 3,  'nomor_loker' => 'C03', 'lokasi' => 'Gedung A', 'kapasitas' => 5, 'terisi' => 0, 'status' => 'Kosong'],
    ['id_loker' => 4,  'nomor_loker' => 'C04', 'lokasi' => 'Gedung A', 'kapasitas' => 5, 'terisi' => 4, 'status' => 'Menunggu Verifikasi'],
    ['id_loker' => 5,  'nomor_loker' => 'C05', 'lokasi' => 'Gedung A', 'kapasitas' => 5, 'terisi' => 1, 'status' => 'Terisi'],
    ['id_loker' => 6,  'nomor_loker' => 'C06', 'lokasi' => 'Gedung A', 'kapasitas' => 5, 'terisi' => 0, 'status' => 'Kosong'],
    ['id_loker' => 7,  'nomor_loker' => 'C07', 'lokasi' => 'Gedung A', 'kapasitas' => 5, 'terisi' => 5, 'status' => 'Penuh'],
    ['id_loker' => 8,  'nomor_loker' => 'C08', 'lokasi' => 'Gedung A', 'kapasitas' => 5, 'terisi' => 2, 'status' => 'Terisi'],
    ['id_loker' => 9,  'nomor_loker' => 'C09', 'lokasi' => 'Gedung A', 'kapasitas' => 5, 'terisi' => 3, 'status' => 'Terisi'],
    ['id_loker' => 10, 'nomor_loker' => 'C10', 'lokasi' => 'Gedung A', 'kapasitas' => 5, 'terisi' => 1, 'status' => 'Terisi'],
    ['id_loker' => 11, 'nomor_loker' => 'C11', 'lokasi' => 'Gedung A', 'kapasitas' => 5, 'terisi' => 2, 'status' => 'Terisi'],
    ['id_loker' => 12, 'nomor_loker' => 'C12', 'lokasi' => 'Gedung A', 'kapasitas' => 5, 'terisi' => 3, 'status' => 'Menunggu Verifikasi'],
    ['id_loker' => 13, 'nomor_loker' => 'C13', 'lokasi' => 'Gedung A', 'kapasitas' => 5, 'terisi' => 0, 'status' => 'Kosong'],
    ['id_loker' => 14, 'nomor_loker' => 'C14', 'lokasi' => 'Gedung A', 'kapasitas' => 5, 'terisi' => 0, 'status' => 'Kosong'],
    ['id_loker' => 15, 'nomor_loker' => 'C15', 'lokasi' => 'Gedung A', 'kapasitas' => 5, 'terisi' => 1, 'status' => 'Terisi'],
    ['id_loker' => 16, 'nomor_loker' => 'C16', 'lokasi' => 'Gedung A', 'kapasitas' => 5, 'terisi' => 5, 'status' => 'Penuh'],
];

$mock_barang = [
    [
        'id_barang' => 1,
        'kode_barang' => 'C04-0012',
        'nama_barang' => 'Jaket Olahraga Parasut',
        'nama_siswa' => 'Ahmad Rizki',
        'nis_siswa'  => '2024101',
        'nomor_loker' => 'C04',
        'lokasi'     => 'Gedung A',
        'foto_barang' => '',
        'status'     => 'Tersimpan',
        'waktu_penitipan' => '2026-10-17 07:15:00',
        'waktu_pengambilan' => null
    ],
    [
        'id_barang' => 2,
        'kode_barang' => 'C01-0005',
        'nama_barang' => 'Helm Bogo Hitam Doff',
        'nama_siswa' => 'Siti Nurhaliza',
        'nis_siswa'  => '2024102',
        'nomor_loker' => 'C01',
        'lokasi'     => 'Gedung A',
        'foto_barang' => '',
        'status'     => 'Tersimpan',
        'waktu_penitipan' => '2026-10-17 07:22:00',
        'waktu_pengambilan' => null
    ],
    [
        'id_barang' => 3,
        'kode_barang' => 'C07-0019',
        'nama_barang' => 'Laptop Lenovo Thinkpad',
        'nama_siswa' => 'Budi Santoso',
        'nis_siswa'  => '2024103',
        'nomor_loker' => 'C07',
        'lokasi'     => 'Gedung A',
        'foto_barang' => '',
        'status'     => 'Tersimpan',
        'waktu_penitipan' => '2026-10-17 07:35:00',
        'waktu_pengambilan' => null
    ]
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

// Ambil ringkasan statistik loker dan barang
function ambilStatistik($koneksi, $mock_loker, $mock_barang) {
    if ($koneksi) {
        $qTotalLoker = mysqli_query($koneksi, "SELECT id_loker FROM loker");
        if ($qTotalLoker && mysqli_num_rows($qTotalLoker) > 0) {
            $total_loker = mysqli_num_rows($qTotalLoker);

            $qBarang = mysqli_query($koneksi, "SELECT 
                SUM(CASE WHEN status = 'Tersimpan' THEN 1 ELSE 0 END) as tersimpan,
                SUM(CASE WHEN status = 'Sudah diambil' THEN 1 ELSE 0 END) as diambil
            FROM barang");
            $dBarang = $qBarang ? mysqli_fetch_assoc($qBarang) : [];
            $b_tersimpan = (int)($dBarang['tersimpan'] ?? 0);
            $b_diambil   = (int)($dBarang['diambil'] ?? 0);

            // Hitung status tiap loker berdasarkan jumlah barang aktif di loker
            $qKapasitas = mysqli_query($koneksi, "SELECT 
                l.id_loker,
                (SELECT COUNT(*) FROM barang b WHERE b.id_loker = l.id_loker AND b.status = 'Tersimpan') as terisi
            FROM loker l");

            $kosong = 0; $terisi = 0; $penuh = 0;
            if ($qKapasitas) {
                while ($r = mysqli_fetch_assoc($qKapasitas)) {
                    $jml = (int)$r['terisi'];
                    if ($jml >= 5) {
                        $penuh++;
                    } elseif ($jml > 0) {
                        $terisi++;
                    } else {
                        $kosong++;
                    }
                }
            }

            return [
                'total_loker'      => $total_loker,
                'loker_kosong'     => $kosong,
                'loker_terisi'     => $terisi,
                'loker_penuh'      => $penuh,
                'barang_tersimpan' => $b_tersimpan,
                'barang_diambil'   => $b_diambil
            ];
        }
    }

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

// Ambil daftar loker
function ambilDaftarLoker($koneksi, $mock_loker) {
    if ($koneksi) {
        $sql = "SELECT l.*, g.nama_gedung, 5 as kapasitas,
                       (SELECT COUNT(*) FROM barang b WHERE b.id_loker = l.id_loker AND b.status = 'Tersimpan') as terisi
                FROM loker l
                LEFT JOIN gedung g ON l.id_gedung = g.id_gedung
                ORDER BY l.nomor_loker ASC
                LIMIT 16";
        $q = mysqli_query($koneksi, $sql);
        if ($q && mysqli_num_rows($q) > 0) {
            $data = [];
            while ($r = mysqli_fetch_assoc($q)) {
                $terisi = (int)($r['terisi'] ?? 0);
                $kapasitas = 5;
                if ($terisi >= $kapasitas) {
                    $r['status'] = 'Penuh';
                } elseif ($terisi > 0) {
                    $r['status'] = 'Terisi';
                } else {
                    $r['status'] = 'Kosong';
                }
                $r['lokasi'] = $r['nama_gedung'] ?? 'Gedung A';
                $data[] = $r;
            }
            return $data;
        }
    }
    return $mock_loker;
}

// Ambil daftar barang dengan filter
function ambilDaftarBarang($koneksi, $mock_barang, $cari = '', $filter_status = '') {
    if ($koneksi) {
        $kondisi = "WHERE 1=1";
        if ($cari !== '') {
            $c = mysqli_real_escape_string($koneksi, $cari);
            $kondisi .= " AND (b.nama_barang LIKE '%$c%' OR b.kode_barang LIKE '%$c%' OR u.nama_lengkap LIKE '%$c%' OR l.nomor_loker LIKE '%$c%')";
        }
        if ($filter_status !== '') {
            $s = mysqli_real_escape_string($koneksi, $filter_status);
            $kondisi .= " AND b.status = '$s'";
        }

        $sql = "SELECT b.*, u.nama_lengkap AS nama_siswa, u.username AS nis_siswa, l.nomor_loker, l.id_gedung, g.nama_gedung AS lokasi 
                FROM barang b 
                LEFT JOIN users u ON b.id_siswa = u.id_user 
                LEFT JOIN loker l ON b.id_loker = l.id_loker 
                LEFT JOIN gedung g ON l.id_gedung = g.id_gedung 
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

// Ambil log aktivitas
function ambilLogAktivitas($koneksi, $mock_log, $filter_aksi = '', $filter_tanggal = '') {
    if ($koneksi) {
        $kondisi = "WHERE 1=1";
        if ($filter_aksi !== '') {
            $a = mysqli_real_escape_string($koneksi, $filter_aksi);
            $kondisi .= " AND l.aksi = '$a'";
        }
        if ($filter_tanggal !== '') {
            $t = mysqli_real_escape_string($koneksi, $filter_tanggal);
            $kondisi .= " AND DATE(l.waktu) = '$t'";
        }

        $sql = "SELECT l.*, u.nama_lengkap AS nama_admin, b.nama_barang, b.kode_barang 
                FROM log_aktivitas l 
                LEFT JOIN users u ON l.id_admin = u.id_user 
                LEFT JOIN barang b ON l.id_barang_terkait = b.id_barang 
                $kondisi ORDER BY l.id_log DESC";
        $q = mysqli_query($koneksi, $sql);
        if ($q && mysqli_num_rows($q) > 0) {
            $data = [];
            while ($r = mysqli_fetch_assoc($q)) {
                $objek = $r['keterangan'];
                if (!empty($r['nama_barang'])) {
                    $objek = $r['nama_barang'] . ' (' . ($r['kode_barang'] ?? '') . ')';
                }
                $r['barang'] = $objek;
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

// Ambil data grafik mingguan
function ambilGrafikMingguan($koneksi, $mock_grafik) {
    if ($koneksi) {
        $hari_indonesia = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
        ];
        $q = mysqli_query($koneksi, "SELECT DAYNAME(waktu_penitipan) as nama_hari, COUNT(*) as jml 
            FROM barang 
            WHERE waktu_penitipan >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DAYNAME(waktu_penitipan)");
        if ($q && mysqli_num_rows($q) > 0) {
            $counts = [];
            $max = 1;
            while ($r = mysqli_fetch_assoc($q)) {
                $h = $hari_indonesia[$r['nama_hari']] ?? $r['nama_hari'];
                $counts[$h] = (int)$r['jml'];
                if ($counts[$h] > $max) $max = $counts[$h];
            }
            $grafik = [];
            foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari) {
                $jml = $counts[$hari] ?? 0;
                $tinggi = ($jml > 0) ? round(($jml / $max) * 100) . '%' : '10%';
                $grafik[] = ['hari' => $hari, 'jumlah' => $jml, 'tinggi' => $tinggi];
            }
            return $grafik;
        }
    }
    return $mock_grafik;
}

// Ambil rekapitulasi data barang dan statistik kategori (Week 6)
function ambilRekapBarang($koneksi, $mock_barang, $tgl_awal = '', $tgl_akhir = '', $status = '', $kategori = '') {
    $data = [];
    $total = 0;
    $tersimpan = 0;
    $diambil = 0;
    $kategori_hitung = [];

    if ($koneksi) {
        $kondisi = "WHERE 1=1";
        if ($tgl_awal !== '') {
            $ta = mysqli_real_escape_string($koneksi, $tgl_awal);
            $kondisi .= " AND DATE(b.waktu_penitipan) >= '$ta'";
        }
        if ($tgl_akhir !== '') {
            $tk = mysqli_real_escape_string($koneksi, $tgl_akhir);
            $kondisi .= " AND DATE(b.waktu_penitipan) <= '$tk'";
        }
        if ($status !== '') {
            $st = mysqli_real_escape_string($koneksi, $status);
            $kondisi .= " AND b.status = '$st'";
        }
        if ($kategori !== '') {
            $kt = mysqli_real_escape_string($koneksi, $kategori);
            $kondisi .= " AND b.nama_barang LIKE '%$kt%'";
        }

        $sql = "SELECT b.*, u.nama_lengkap AS nama_siswa, u.username AS nis_siswa, l.nomor_loker, g.nama_gedung AS lokasi 
                FROM barang b 
                LEFT JOIN users u ON b.id_siswa = u.id_user 
                LEFT JOIN loker l ON b.id_loker = l.id_loker 
                LEFT JOIN gedung g ON l.id_gedung = g.id_gedung 
                $kondisi ORDER BY b.waktu_penitipan DESC";
        $q = mysqli_query($koneksi, $sql);

        if ($q) {
            while ($r = mysqli_fetch_assoc($q)) {
                $w_masuk = strtotime($r['waktu_penitipan']);
                $w_keluar = !empty($r['waktu_pengambilan']) ? strtotime($r['waktu_pengambilan']) : time();
                $diff_menit = max(1, round(($w_keluar - $w_masuk) / 60));
                
                if ($diff_menit < 60) {
                    $durasi = $diff_menit . ' menit';
                } elseif ($diff_menit < 1440) {
                    $durasi = round($diff_menit / 60, 1) . ' jam';
                } else {
                    $durasi = round($diff_menit / 1440) . ' hari';
                }
                $r['durasi'] = $durasi;

                if ($r['status'] === 'Tersimpan') {
                    $tersimpan++;
                } else {
                    $diambil++;
                }

                $nama = strtolower($r['nama_barang']);
                $kat = 'Lainnya';
                foreach (['tas', 'jaket', 'helm', 'laptop', 'sepatu', 'buku', 'botol'] as $k) {
                    if (strpos($nama, $k) !== false) {
                        $kat = ucfirst($k);
                        break;
                    }
                }
                $kategori_hitung[$kat] = ($kategori_hitung[$kat] ?? 0) + 1;

                $data[] = $r;
            }
            $total = count($data);
        }
    }

    if (empty($data)) {
        foreach ($mock_barang as $item) {
            $tgl_item = substr($item['waktu_penitipan'], 0, 10);
            $cocok_tgl = true;
            if ($tgl_awal !== '' && $tgl_item < $tgl_awal) $cocok_tgl = false;
            if ($tgl_akhir !== '' && $tgl_item > $tgl_akhir) $cocok_tgl = false;
            $cocok_status = ($status === '' || $item['status'] === $status);
            $cocok_kat = ($kategori === '' || stripos($item['nama_barang'], $kategori) !== false);

            if ($cocok_tgl && $cocok_status && $cocok_kat) {
                $item['durasi'] = ($item['status'] === 'Tersimpan') ? 'Aktif' : '8 jam';
                if ($item['status'] === 'Tersimpan') $tersimpan++;
                else $diambil++;

                $nama = strtolower($item['nama_barang']);
                $kat = 'Lainnya';
                foreach (['tas', 'jaket', 'helm', 'laptop', 'sepatu', 'buku', 'botol'] as $k) {
                    if (strpos($nama, $k) !== false) {
                        $kat = ucfirst($k);
                        break;
                    }
                }
                $kategori_hitung[$kat] = ($kategori_hitung[$kat] ?? 0) + 1;

                $data[] = $item;
            }
        }
        $total = count($data);
    }

    arsort($kategori_hitung);
    $persen_diambil = ($total > 0) ? round(($diambil / $total) * 100, 1) : 0;

    return [
        'data'             => $data,
        'total'            => $total,
        'tersimpan'        => $tersimpan,
        'diambil'          => $diambil,
        'persen_diambil'   => $persen_diambil,
        'kategori_populer' => $kategori_hitung
    ];
}
?>