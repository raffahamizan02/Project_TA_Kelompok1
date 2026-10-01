<?php require_once __DIR__.'/../auth.php'; ?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($pageTitle)?> - LOKIFY</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head><body>
<div class="admin-shell"><aside class="sidebar"><div><div class="brand"><img src="../Login & Dashboard Siswa/logoLOKIFY.png" alt="LOKIFY" onerror="this.style.display='none'"><div><strong>LOKIFY</strong><span>ADMIN PANEL</span></div></div>
<nav class="nav-list">
<a href="beranda.php" class="<?= $activePage==='beranda'?'active':'' ?>"><i class="fa-solid fa-gauge-high"></i><span>Dashboard Utama</span></a>
<a href="databarang.php" class="<?= $activePage==='barang'?'active':'' ?>"><i class="fa-solid fa-boxes-stacked"></i><span>Data Barang</span></a>
<a href="dataloker.php" class="<?= $activePage==='loker'?'active':'' ?>"><i class="fa-solid fa-lock"></i><span>Data Loker</span></a>
<a href="laporan.php" class="<?= $activePage==='laporan'?'active':'' ?>"><i class="fa-solid fa-file-lines"></i><span>Laporan</span></a>
<a href="setelan.php" class="<?= $activePage==='setelan'?'active':'' ?>"><i class="fa-solid fa-gear"></i><span>Setelan</span></a>
</nav></div><div class="sidebar-footer"><div>SMK PGRI 3 Malang</div><small>Sistem Pendataan Barang Loker</small></div></aside>
<div class="main-area"><header class="topbar"><div><div class="breadcrumb">Admin / <?=e($pageTitle)?></div><h1><?=e($pageTitle)?></h1></div>
<div class="topbar-user"><div class="avatar"><i class="fa-solid fa-user-shield"></i></div><div><strong><?=e($namaAdmin)?></strong><span>Administrator</span></div><a href="logout.php" class="btn btn-danger btn-small"><i class="fa-solid fa-power-off"></i></a></div></header><main class="page-content">