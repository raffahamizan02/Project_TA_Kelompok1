<?php
session_start();
require_once 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user      = $_SESSION['id_user'];
$nama_lengkap = $_SESSION['nama_lengkap'] ?? 'User';
$role         = $_SESSION['role'] ?? 'siswa';

// PENANGANAN INPUT BARANG (Untuk Siswa)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_barang'])) {
    $nama_barang = mysqli_real_escape_string($koneksi, trim($_POST['nama_barang']));
    $no_loker    = mysqli_real_escape_string($koneksi, trim($_POST['no_loker']));
    $tgl_simpan  = date('Y-m-d H:i:s');
    $status      = 'Tersimpan';

    if (!empty($nama_barang) && !empty($no_loker)) {
        $insert = mysqli_query($koneksi, "INSERT INTO barang (id_user, nama_barang, no_loker, tgl_simpan, status) VALUES ('$id_user', '$nama_barang', '$no_loker', '$tgl_simpan', '$status')");
        if ($insert) {
            header("Location: dashboard.php");
            exit();
        }
    }
}

// AMBIL DATA BERDASARKAN ROLE
if ($role === 'admin') {
    $q_barang = mysqli_query($koneksi, "SELECT b.*, u.nama_lengkap FROM barang b LEFT JOIN users u ON b.id_user = u.id_user ORDER BY b.id_barang DESC");
    $q_count  = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM barang WHERE status = 'Tersimpan'");
} else {
    $q_barang = mysqli_query($koneksi, "SELECT * FROM barang WHERE id_user = '$id_user' ORDER BY id_barang DESC");
    $q_count  = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM barang WHERE id_user = '$id_user' AND status = 'Tersimpan'");
}

$data_count   = mysqli_fetch_assoc($q_count);
$total_barang = $data_count['total'] ?? 0;

$accent_color = ($role === 'admin') ? '#AFDDFF' : '#FF9149';
$accent_text  = ($role === 'admin') ? '#0f233a' : '#ffffff';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Loker Sekolah</title>
   
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background-color: #f1f5f9;
            color: #1e293b;
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            background: #0f233a;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-area {
            padding: 20px;
            border-bottom: 1px solid #1e3a8a;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-area img {
            height: 40px;
        }

        .brand-text h2 {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .brand-text h2 span {
            color: <?php echo $accent_color; ?>;
        }

        .nav-menu {
            list-style: none;
            padding: 15px 0;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            border-left: 4px solid transparent;
        }

        .nav-item.active a {
            background: #1e293b;
            color: #ffffff;
            border-left-color: <?php echo $accent_color; ?>;
            font-weight: 700;
        }

        /* MAIN CONTENT */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .top-header {
            background: #ffffff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }

        .page-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f233a;
        }

        .user-box {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-details {
            text-align: right;
        }

        .user-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f233a;
        }

        .user-role {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
        }

        .btn-logout {
            background: #ef4444;
            color: #ffffff;
            padding: 7px 14px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
        }

        .content-area {
            padding: 25px 30px;
        }

        /* CARDS RINGKASAN */
        .stats-row {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            flex: 1;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 4px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #0f233a;
        }

        .stat-data h4 {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
        }

        .stat-data h2 {
            font-size: 20px;
            font-weight: 800;
            color: #0f233a;
        }

        /* CONTAINER PANEL / TABLE */
        .panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        .panel-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .panel-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f233a;
            text-transform: uppercase;
        }

        .btn-primary {
            background: <?php echo $accent_color; ?>;
            color: <?php echo $accent_text; ?>;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        /* TABEL DATA */
        .table-custom {
            width: 100%;
            border-collapse: collapse;
        }

        .table-custom th {
            background: #f8fafc;
            padding: 12px 20px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-custom td {
            padding: 12px 20px;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .badge {
            background: #dcfce7;
            color: #166534;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 700;
        }

        /* MODAL SIMPLE */
        .modal-bg {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            align-items: center; justify-content: center;
            z-index: 99;
        }

        .modal-box {
            background: #ffffff;
            width: 400px;
            border-radius: 6px;
            padding: 20px;
            border: 1px solid #cbd5e1;
        }

        .modal-box h3 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #0f233a;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #334155;
        }

        .form-input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 13px;
        }

        .modal-footer {
            margin-top: 15px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #334155;
            border: none;
            padding: 8px 14px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div>
            <div class="brand-area">
                <img src="logoLOKIFY.png" alt="Logo" onerror="this.style.display='none'">
                <div class="brand-text">
                    <h2>LOKER <span>SEKOLAH</span></h2>
                </div>
            </div>
            <ul class="nav-menu">
                <li class="nav-item active">
                    <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard Utama</a>
                </li>
            </ul>
        </div>
        <div style="padding: 20px; font-size: 11px; color: #64748b;">
            SMK PGRI 3 Malang
        </div>
    </div>

    <!-- MAIN WRAPPER -->
    <div class="main-wrapper">
       
        <!-- HEADER -->
        <div class="top-header">
            <div class="page-title">Dashboard Utama</div>
            <div class="user-box">
                <div class="user-details">
                    <div class="user-name"><?php echo htmlspecialchars($nama_lengkap); ?></div>
                    <div class="user-role"><?php echo htmlspecialchars($role); ?></div>
                </div>
                <a href="logout.php" class="btn-logout"><i class="fa-solid fa-power-off"></i> Keluar</a>
            </div>
        </div>

        <!-- CONTENT AREA -->
        <div class="content-area">

            <!-- STATS CARDS -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-box"></i></div>
                    <div class="stat-data">
                        <h4>Barang Saya</h4>
                        <h2><?php echo $total_barang; ?> Item</h2>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-door-closed"></i></div>
                    <div class="stat-data">
                        <h4>Kapasitas Loker</h4>
                        <h2>24 Unit</h2>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                    <div class="stat-data">
                        <h4>Status Sistem</h4>
                        <h2 style="color: #16a34a;">Aktif</h2>
                    </div>
                </div>
            </div>

            <!-- TABLE PANEL -->
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title">Daftar Barang Milik Saya</div>
                    <?php if ($role === 'siswa'): ?>
                        <button class="btn-primary" onclick="openModal()"><i class="fa-solid fa-plus"></i> Titip Barang</button>
                    <?php endif; ?>
                </div>

                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <?php if ($role === 'admin'): ?><th>PEMILIK</th><?php endif; ?>
                            <th>NAMA BARANG</th>
                            <th>NO. LOKER</th>
                            <th>TANGGAL SIMPAN</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if ($q_barang && mysqli_num_rows($q_barang) > 0):
                            while ($row = mysqli_fetch_assoc($q_barang)):
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <?php if ($role === 'admin'): ?>
                                    <td><strong><?php echo htmlspecialchars($row['nama_lengkap'] ?? '-'); ?></strong></td>
                                <?php endif; ?>
                                <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                <td>Loker #<?php echo htmlspecialchars($row['no_loker']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['tgl_simpan'])); ?></td>
                                <td><span class="badge"><?php echo htmlspecialchars($row['status']); ?></span></td>
                            </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="<?php echo ($role === 'admin') ? '6' : '5'; ?>" style="text-align: center; color: #94a3b8; padding: 30px;">
                                    Tidak ada data barang yang tersimpan saat ini.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- MODAL FORM -->
    <div class="modal-bg" id="modalInput">
        <div class="modal-box">
            <h3>Form Titip Barang</h3>
            <form method="POST" action="dashboard.php">
                <input type="hidden" name="simpan_barang" value="1">
               
                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control form-input" placeholder="Misal: Helm / Tas / Laptop" required>
                </div>

                <div class="form-group">
                    <label>Nomor Loker</label>
                    <input type="number" name="no_loker" class="form-control form-input" placeholder="Masukkan No. Loker (1-24)" min="1" max="24" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('modalInput').style.display = 'flex'; }
        function closeModal() { document.getElementById('modalInput').style.display = 'none'; }
    </script>
</body>
</html>