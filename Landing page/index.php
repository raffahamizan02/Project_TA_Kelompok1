<?php
session_start();
include __DIR__ . "/../login/koneksi.php";

// Gedung valid: A-D, default A. Di tabel loker kolom `lokasi` berisi 'Gedung A' dst.
$daftar_gedung = ['A', 'B', 'C', 'D'];
$gedung = strtoupper($_GET['gedung'] ?? 'A');
if (!in_array($gedung, $daftar_gedung)) {
    $gedung = 'A';
}
$lokasi_gedung = 'Gedung ' . $gedung;

// 5 baris x 7 kolom = 35 loker per gedung
$stmt = mysqli_prepare($koneksi, "SELECT nomor_loker, lokasi, status FROM loker WHERE lokasi = ? ORDER BY nomor_loker LIMIT 35");
mysqli_stmt_bind_param($stmt, "s", $lokasi_gedung);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

$lokers = [];
$kosong = 0;
while ($row = mysqli_fetch_assoc($hasil)) {
    $lokers[] = $row;
    if (strtolower($row['status']) == 'kosong') $kosong++;
}
$terisi = count($lokers) - $kosong;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokify - Pendataan Barang Loker Sekolah</title>
    <link rel="stylesheet" href="landing.css">
</head>
<body>
    <nav class="nav">
        <div class="logo">Loki<span>Fy</span></div>
        <div class="nav-btn">
            <a href="../login/login.php" class="btn btn-login">Login</a>
            <a href="../login/login.php" class="btn btn-reg">Register</a>
        </div>
    </nav>

    <header class="hero">
        <span class="label">PENDATAAN LOKER SEKOLAH</span>
        <h1>Kelola Barang di Loker Sekolah</h1>
        <p>Lihat ketersediaan loker per gedung. Login untuk menyimpan dan mengambil barang.</p>
    </header>

    <main class="main">
        <div class="panel">
            <div class="top">
                <div class="tabs">
                    <?php foreach ($daftar_gedung as $g) { ?>
                        <a class="tab <?php echo $g == $gedung ? 'active' : ''; ?>" href="?gedung=<?php echo $g; ?>">Gedung <?php echo $g; ?></a>
                    <?php } ?>
                </div>
                <div class="stat">
                    <span>Kosong: <b><?php echo $kosong; ?></b></span>
                    <span>Terisi: <b><?php echo $terisi; ?></b></span>
                </div>
            </div>

            <div class="grid">
                <?php foreach ($lokers as $row) {
                    $ada = strtolower($row['status']) == 'kosong';
                ?>
                    <div class="card">
                        <div class="num"><?php echo htmlspecialchars($row['nomor_loker']); ?></div>
                        <div class="loc"><?php echo htmlspecialchars($row['lokasi']); ?></div>
                        <span class="st <?php echo $ada ? 'tersedia' : 'terisi'; ?>"><i></i><?php echo $ada ? 'TERSEDIA' : 'TERISI'; ?></span>
                    </div>
                <?php } ?>
            </div>
            <p class="note">Tampilan ini hanya untuk melihat status loker.</p>
        </div>
    </main>
    <footer>&copy; LokerKu</footer>
</body>
</html>