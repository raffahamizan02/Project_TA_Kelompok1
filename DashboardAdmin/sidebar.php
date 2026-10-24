<?php
$halaman_aktif = $halaman_aktif ?? 'beranda';
?>

<aside class="sidebar">
    <div class="logo">
        <div class="logo-icon">L</div>
        <div>
            <h2>LokerKu</h2>
            <span>Admin TU</span>
        </div>
    </div>

    <div class="menu-title">MENU UTAMA</div>

    <nav class="nav-menu">
        <a href="beranda.php" class="menu <?= ($halaman_aktif === 'beranda') ? 'active' : '' ?>">
            Beranda
        </a>
        <a href="../DataBarang/indexBarang.php" class="menu <?= ($halaman_aktif === 'databarang') ? 'active' : '' ?>">
            Data Barang
        </a>
        <a href="dataloker.php" class="menu <?= ($halaman_aktif === 'dataloker') ? 'active' : '' ?>">
            Data Loker
        </a>
        <a href="laporan.php" class="menu <?= ($halaman_aktif === 'laporan') ? 'active' : '' ?>">
            Laporan
        </a>
        <a href="setelan.php" class="menu <?= ($halaman_aktif === 'setelan') ? 'active' : '' ?>">
            Pengaturan
        </a>
    </nav>
</aside>