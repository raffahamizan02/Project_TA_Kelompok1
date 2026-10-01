<?php
$pageTitle='Dashboard Utama';$activePage='beranda';require_once __DIR__.'/partials/header.php';
function countQuery(mysqli $db,string $sql):int{$r=$db->query($sql);return $r?(int)$r->fetch_assoc()['total']:0;}
$totalBarang=countQuery($koneksi,"SELECT COUNT(*) total FROM barang");
$barangTersimpan=countQuery($koneksi,"SELECT COUNT(*) total FROM barang WHERE status='Tersimpan'");
$lokerTerisi=countQuery($koneksi,"SELECT COUNT(DISTINCT no_loker) total FROM barang WHERE status='Tersimpan'");
$totalSiswa=countQuery($koneksi,"SELECT COUNT(*) total FROM users WHERE role='siswa'");
$tersedia=max(24-$lokerTerisi,0);
$latest=$koneksi->query("SELECT b.id_barang,b.nama_barang,b.no_loker,b.tgl_simpan,b.status,COALESCE(u.nama_lengkap,'Tidak diketahui') pemilik FROM barang b LEFT JOIN users u ON u.id_user=b.id_user ORDER BY b.id_barang DESC LIMIT 6");
?>
<section class="grid-4">
<article class="stat-card"><div class="stat-icon"><i class="fa-solid fa-boxes-stacked"></i></div><div><small>Total Barang</small><strong><?=$totalBarang?></strong></div></article>
<article class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div><div><small>Barang Tersimpan</small><strong><?=$barangTersimpan?></strong></div></article>
<article class="stat-card"><div class="stat-icon orange"><i class="fa-solid fa-lock"></i></div><div><small>Loker Terisi</small><strong><?=$lokerTerisi?>/24</strong></div></article>
<article class="stat-card"><div class="stat-icon"><i class="fa-solid fa-user-graduate"></i></div><div><small>Total Siswa</small><strong><?=$totalSiswa?></strong></div></article>
</section>
<div class="panel"><div class="panel-head"><div><h2>Ringkasan Kapasitas Loker</h2><p><?=$tersedia?> loker tersedia dari 24 unit.</p></div><a href="dataloker.php" class="btn btn-light"><i class="fa-solid fa-arrow-right"></i> Data Loker</a></div>
<div class="panel-body"><div class="report-meta"><div class="meta-box"><small>Kapasitas</small><strong>24 Loker</strong></div><div class="meta-box"><small>Terisi</small><strong><?=$lokerTerisi?> Loker</strong></div><div class="meta-box"><small>Tersedia</small><strong><?=$tersedia?> Loker</strong></div><div class="meta-box"><small>Barang Aktif</small><strong><?=$barangTersimpan?> Item</strong></div></div></div></div>
<div class="panel"><div class="panel-head"><div><h2>Data Barang Terbaru</h2><p>Enam data terakhir yang tercatat.</p></div><a href="databarang.php" class="btn btn-primary"><i class="fa-solid fa-box"></i> Kelola Barang</a></div>
<div class="table-wrap"><table><thead><tr><th>No</th><th>Pemilik</th><th>Barang</th><th>Loker</th><th>Tanggal Simpan</th><th>Status</th></tr></thead><tbody>
<?php if($latest&&$latest->num_rows):$no=1;while($row=$latest->fetch_assoc()):?><tr><td><?=$no++?></td><td><b><?=e($row['pemilik'])?></b></td><td><?=e($row['nama_barang'])?></td><td>Loker #<?=e($row['no_loker'])?></td><td><?=e(date('d/m/Y H:i',strtotime($row['tgl_simpan'])))?></td><td><span class="badge <?=$row['status']==='Tersimpan'?'badge-success':'badge-neutral'?>"><?=e($row['status'])?></span></td></tr>
<?php endwhile;else:?><tr><td colspan="6"><div class="empty-state"><i class="fa-regular fa-folder-open"></i>Belum ada data barang.</div></td></tr><?php endif;?>
</tbody></table></div></div>
<?php require_once __DIR__.'/partials/footer.php';?>