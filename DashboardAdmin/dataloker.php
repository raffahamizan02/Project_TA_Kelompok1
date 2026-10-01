<?php
$pageTitle='Data Loker';$activePage='loker';require_once __DIR__.'/partials/header.php';
$occupied=[];$res=$koneksi->query("SELECT b.no_loker,COUNT(*) jumlah,GROUP_CONCAT(CONCAT(COALESCE(u.nama_lengkap,'Tidak diketahui'),' - ',b.nama_barang) SEPARATOR '||') detail FROM barang b LEFT JOIN users u ON u.id_user=b.id_user WHERE b.status='Tersimpan' GROUP BY b.no_loker");
if($res)while($row=$res->fetch_assoc())$occupied[(int)$row['no_loker']]=$row;
$jumlahTerisi=count($occupied);$jumlahTersedia=max(24-$jumlahTerisi,0);
?>
<div class="panel"><div class="panel-head"><div><h2>Pemetaan 24 Loker Sekolah</h2><p>Loker berstatus terisi menampilkan pemilik dan barang.</p></div><div class="toolbar"><span class="badge badge-full"><?=$jumlahTerisi?> Terisi</span><span class="badge badge-empty"><?=$jumlahTersedia?> Tersedia</span></div></div>
<div class="panel-body"><div class="loker-grid">
<?php for($i=1;$i<=24;$i++):$isOccupied=isset($occupied[$i]);$detail=$isOccupied?explode('||',(string)$occupied[$i]['detail']):[];?>
<article class="loker-card <?=$isOccupied?'occupied':''?>"><div class="loker-number">#<?=$i?></div><div class="loker-state"><?php if($isOccupied):?><span class="badge badge-full"><i class="fa-solid fa-lock"></i> Terisi</span><?php else:?><span class="badge badge-empty"><i class="fa-solid fa-lock-open"></i> Tersedia</span><?php endif;?></div>
<?php if($isOccupied):?><div class="loker-info"><strong><?=e($detail[0]??'Barang tersimpan')?></strong><?php if(count($detail)>1):?><span>+ <?=count($detail)-1?> data lain</span><?php endif;?></div><?php else:?><div class="loker-info">Belum ada barang tersimpan.</div><?php endif;?></article>
<?php endfor;?></div></div></div>
<?php require_once __DIR__.'/partials/footer.php';?>