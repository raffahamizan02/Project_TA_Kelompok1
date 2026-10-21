<?php
session_start();
include __DIR__ . "/../login/koneksi.php";

// ==========================================
// AMBIL DATA GEDUNG DARI DATABASE
// ==========================================

$query_gedung = mysqli_query(
    $koneksi,
    "SELECT id_gedung, nama_gedung
     FROM gedung
     ORDER BY id_gedung ASC"
);

$daftar_gedung = [];

while ($row = mysqli_fetch_assoc($query_gedung)) {
    $daftar_gedung[] = $row;
}


// ==========================================
// TENTUKAN GEDUNG YANG DIPILIH
// ==========================================

// Default = Gedung pertama
$id_gedung = isset($_GET['gedung']) ? (int) $_GET['gedung'] : 1;

$nama_gedung = '';
$gedung_ditemukan = false;


// Cari nama gedung berdasarkan ID
foreach ($daftar_gedung as $gedung) {

    if ((int) $gedung['id_gedung'] === $id_gedung) {

        $gedung_ditemukan = true;
        $nama_gedung = $gedung['nama_gedung'];

        break;
    }
}


// Jika ID gedung tidak ditemukan,
// kembali ke gedung pertama
if (!$gedung_ditemukan && !empty($daftar_gedung)) {

    $id_gedung = (int) $daftar_gedung[0]['id_gedung'];
    $nama_gedung = $daftar_gedung[0]['nama_gedung'];

}


// ==========================================
// AMBIL DATA LOKER BERDASARKAN GEDUNG
// ==========================================

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT nomor_loker, id_gedung, status
     FROM loker
     WHERE id_gedung = ?
     ORDER BY nomor_loker ASC
     LIMIT 35"
);

mysqli_stmt_bind_param($stmt, "i", $id_gedung);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);


// ==========================================
// HITUNG STATUS LOKER
// ==========================================

$lokers = [];
$kosong = 0;

while ($row = mysqli_fetch_assoc($hasil)) {

    $lokers[] = $row;

    if (strtolower($row['status']) == 'kosong') {
        $kosong++;
    }

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


<!-- ==========================================
     NAVBAR
========================================== -->

<nav class="nav">

    <div class="logo">
        Loki<span>Fy</span>
    </div>

    <div class="nav-btn">

        <a href="../login/login.php" class="btn btn-login">
            Login
        </a>

        <a href="../login/login.php" class="btn btn-reg">
            Register
        </a>

    </div>

</nav>



<!-- ==========================================
     HERO
========================================== -->

<header class="hero">

    <span class="label">
        PENDATAAN LOKER SEKOLAH
    </span>

    <h1>
        Kelola Barang di Loker Sekolah
    </h1>

    <p>
        Lihat ketersediaan loker per gedung.
        Login untuk menyimpan dan mengambil barang.
    </p>

</header>



<!-- ==========================================
     MAIN
========================================== -->

<main class="main">

    <div class="panel">


        <!-- ==========================================
             TAB GEDUNG + STATISTIK
        ========================================== -->

        <div class="top">


            <div class="tabs">

                <?php foreach ($daftar_gedung as $g) { ?>

                    <a
                        class="tab <?php echo ((int)$g['id_gedung'] === $id_gedung) ? 'active' : ''; ?>"
                        href="?gedung=<?php echo $g['id_gedung']; ?>"
                    >

                        <?php echo htmlspecialchars($g['nama_gedung']); ?>

                    </a>

                <?php } ?>

            </div>


            <div class="stat">

                <span>
                    Kosong:
                    <b>
                        <?php echo $kosong; ?>
                    </b>
                </span>


                <span>
                    Terisi:
                    <b>
                        <?php echo $terisi; ?>
                    </b>
                </span>

            </div>

        </div>



        <!-- ==========================================
             GRID LOKER
        ========================================== -->

        <div class="grid">

            <?php if (empty($lokers)) { ?>

                <div class="empty">

                    <p>
                        Belum ada data loker di
                        <?php echo htmlspecialchars($nama_gedung); ?>.
                    </p>

                </div>

            <?php } else { ?>


                <?php foreach ($lokers as $row) {

                    $ada = strtolower($row['status']) == 'kosong';

                ?>

                    <div class="card">


                        <!-- NOMOR LOKER -->

                        <div class="num">

                            <?php
                            echo htmlspecialchars(
                                $row['nomor_loker']
                            );
                            ?>

                        </div>


                        <!-- NAMA GEDUNG -->

                        <div class="loc">

                            <?php
                            echo htmlspecialchars(
                                $nama_gedung
                            );
                            ?>

                        </div>


                        <!-- STATUS -->

                        <span
                            class="st <?php echo $ada ? 'tersedia' : 'terisi'; ?>"
                        >

                            <i></i>

                            <?php echo $ada ? 'TERSEDIA' : 'TERISI'; ?>

                        </span>


                    </div>

                <?php } ?>

            <?php } ?>

        </div>



        <!-- ==========================================
             CATATAN
        ========================================== -->

        <p class="note">

            Tampilan ini hanya untuk melihat status loker.

        </p>


    </div>

</main>



<!-- ==========================================
     FOOTER
========================================== -->

<footer>

    &copy; LokerKu

</footer>


</body>

</html>