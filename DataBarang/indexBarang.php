<?php
include "connectBarang.php";

if (!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("location: ../login/login.php?p=Silakan login terlebih dahulu!");
    exit();
}

$role = $_SESSION['role'];

$cari = "";
if (isset($_GET['cari'])) {
    $cari = mysqli_real_escape_string($koneksi, $_GET['cari']);
}

$where = "WHERE 1=1";
if ($role == 'siswa') {
    $id_siswa = (int) $_SESSION['id_siswa'];
    $where .= " AND b.id_siswa = $id_siswa";
}
if ($cari != "") {
    $where .= " AND (b.nama_barang LIKE '%$cari%' OR b.kode_barang LIKE '%$cari%')";
}

$data = mysqli_query($koneksi, "SELECT b.*, s.nama, l.kode_loker FROM barang b JOIN siswa s ON b.id_siswa = s.id
    JOIN loker l ON b.id_loker = l.id_loker $where ORDER BY b.id_barang DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Barang</title>
    <link rel="stylesheet" href="assets/css/data-barang.css">
</head>
<body>
    <div class="container">
        <h2>Data Barang</h2>
        <hr>

        <?php if (isset($_GET['p'])) { ?>
            <p class="pesan"><?php echo htmlspecialchars($_GET['p']); ?></p>
        <?php } ?>

        <?php if ($role == 'siswa') { ?>
            <a href="create.php" class="tombol hijau">TAMBAH BARANG</a>
        <?php } ?>

        <form method="GET">
            <input type="text" name="cari" placeholder="Cari kode atau nama barang" value="<?php echo htmlspecialchars($cari); ?>">
            <button type="submit">CARI</button>
        </form>

        <table>
            <tr>
                <th>Foto</th>
                <th>Kode</th>
                <th>Nama Siswa</th>
                <th>Nama Barang</th>
                <th>Loker</th>
                <th>Status</th>
                <th>Waktu Penitipan</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($data)) { ?>
            <tr>
                <td><img src="uploads/barang/<?php echo $row['foto_barang']; ?>" width="60"></td>
                <td><?php echo $row['kode_barang']; ?></td>
                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                <td><?php echo $row['kode_loker']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><?php echo $row['waktu_penitipan']; ?></td>
                <td>
                    <a href="detail.php?id=<?php echo $row['id_barang']; ?>">DETAIL</a>
                    <?php if ($role == 'admin') { ?>
                        | <a href="verifikasi.php?id=<?php echo $row['id_barang']; ?>">VERIFIKASI</a>
                        | <a href="update.php?id=<?php echo $row['id_barang']; ?>">EDIT</a>
                        | <a href="delete.php?id=<?php echo $row['id_barang']; ?>"
                             onclick="return confirm('Yakin ingin hapus?')">DELETE</a>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>