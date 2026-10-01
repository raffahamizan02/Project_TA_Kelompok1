<?php $pageTitle='Setelan';$activePage='setelan';require_once __DIR__.'/partials/header.php'; ?>
<div class="panel"><div class="panel-head"><div><h2>Pengaturan Sistem</h2><p>Konfigurasi yang digunakan oleh Dashboard Admin.</p></div></div>
<div class="panel-body"><div class="form-grid">
<div class="form-field"><label>Nama Aplikasi</label><div class="readonly-field">LOKIFY - Sistem Pendataan Barang di Loker Sekolah</div></div>
<div class="form-field"><label>Sekolah</label><div class="readonly-field">SMK PGRI 3 Malang</div></div>
<div class="form-field"><label>Kapasitas Loker</label><div class="readonly-field">24 Unit</div></div>
<div class="form-field"><label>Database</label><div class="readonly-field"><?=e($db)?></div></div>
<div class="form-field field-full"><label>Aturan Pendataan</label><div class="readonly-field">Satu nomor loker hanya boleh memiliki satu barang dengan status <b>Tersimpan</b>.</div></div>
</div></div></div>
<?php require_once __DIR__.'/partials/footer.php';?>