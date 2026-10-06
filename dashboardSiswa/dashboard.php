<?php
// ============================================
// DATA DUMMY (sementara, untuk tampilan saja)
// Nanti digabung dengan backend asli
// ============================================

// Data user dummy
$nama_lengkap = 'Zigi Zagazigi';
$role         = 'siswa';
$inisial      = 'Z';
$foto_user    = '';
$email_user   = 'zigi@example.com';
$no_hp_user   = '08123456789';

// Gedung aktif (dari URL, default Gedung A)
$gedung_aktif = isset($_GET['gedung']) ? $_GET['gedung'] : 'Gedung A';
$kode_gedung_prefix = strtoupper(trim(str_replace('Gedung', '', $gedung_aktif)));

// Data dummy statistik
$total_tersimpan = 1;
$sisa_loker      = 149;
$total_diambil   = 6;

// Data dummy tabel barang
$barang_list = [
    [
        'id_transaksi' => 1,
        'nama_barang'  => 'LAPTOP',
        'keterangan'   => 'Warna hitam, casing ada stiker',
        'no_loker'     => 'A010',
        'tgl_simpan'   => '2026-09-15 03:33:04',
    ],
];

// Data dummy riwayat
$riwayat_list = [
    [
        'nama_barang'  => 'HELM',
        'nama_gedung'  => 'Gedung A',
        'no_loker'     => 'A001',
        'tgl_simpan'   => '2026-09-10 08:00:00',
        'tgl_ambil'    => '2026-09-12 15:30:00',
    ],
    [
        'nama_barang'  => 'TAS',
        'nama_gedung'  => 'Gedung B',
        'no_loker'     => 'B002',
        'tgl_simpan'   => '2026-09-05 07:15:00',
        'tgl_ambil'    => '2026-09-06 12:00:00',
    ],
];

// Daftar loker kosong (dummy)
$loker_kosong_list = [];
for ($i = 1; $i <= 150; $i++) {
    if ($i != 10) {
        $loker_kosong_list[] = $kode_gedung_prefix . sprintf("%03d", $i);
    }
}

$page_title = 'Dashboard Siswa - ' . $gedung_aktif;

include __DIR__ . '/header.php';
?>

<!-- 3 KARTU STATISTIK -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-box"></i></div>
        <div class="stat-data">
            <h4>Barang Tersimpan</h4>
            <h2><?php echo $total_tersimpan; ?> Item</h2>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-door-open"></i></div>
        <div class="stat-data">
            <h4>Sisa Loker Kosong</h4>
            <h2 style="color: #0284c7;"><?php echo $sisa_loker; ?> Loker</h2>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
        <div class="stat-data">
            <h4>Sudah Diambil</h4>
            <h2><?php echo $total_diambil; ?> Item</h2>
        </div>
    </div>
</div>

<!-- TABEL -->
<div class="panel">
    <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div class="panel-title">Daftar Barang Tersimpan - <?php echo htmlspecialchars($gedung_aktif); ?></div>
        
        <div style="display: flex; gap: 10px; align-items: center;">
            <form method="GET" action="dashboard.php" class="search-box" onsubmit="event.preventDefault(); Swal.fire({icon:'info',title:'Fitur belum aktif',text:'Search akan aktif setelah integrasi backend.'});">
                <input type="hidden" name="gedung" value="<?php echo htmlspecialchars($gedung_aktif); ?>">
                <input type="text" name="search" class="search-input" placeholder="Cari barang / loker...">
                <button type="submit" class="btn-primary" style="padding: 8px 12px;"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>

            <button type="button" class="btn-primary" onclick="openModal()"><i class="fa-solid fa-plus"></i> + Titip Barang</button>
        </div>
    </div>

    <table class="table-custom">
        <thead>
            <tr>
                <th>NO</th>
                <th>NAMA BARANG</th>
                <th>KETERANGAN</th>
                <th>NO. LOKER</th>
                <th>TANGGAL SIMPAN</th>
                <th>STATUS</th>
                <th>AKSI</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($barang_list) > 0): ?>
                <?php $no = 1; foreach ($barang_list as $row): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><strong><?php echo htmlspecialchars($row['nama_barang']); ?></strong></td>
                        <td style="color: #64748b; font-size: 13px;">
                            <?php echo !empty($row['keterangan']) ? htmlspecialchars($row['keterangan']) : '-'; ?>
                        </td>
                        <td><strong style="color: #0f233a;"><?php echo htmlspecialchars($row['no_loker']); ?></strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['tgl_simpan'])); ?></td>
                        <td><span class="badge-tersimpan">Tersimpan</span></td>
                        <td>
                            <button type="button" 
                                    class="btn-ambil" 
                                    onclick="konfirmasiAmbil('<?php echo htmlspecialchars($row['no_loker']); ?>');">
                                <i class="fa-solid fa-hand-holding"></i> Ambil
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #94a3b8; padding: 30px;">
                        Belum ada barang tersimpan di <strong><?php echo htmlspecialchars($gedung_aktif); ?></strong>.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- MODAL 1: TITIP BARANG -->
<div class="modal-bg" id="modalInput">
    <div class="modal-box">
        <h3><i class="fa-solid fa-box-archive"></i> Form Titip Barang (<?php echo htmlspecialchars($gedung_aktif); ?>)</h3>
        <form onsubmit="event.preventDefault(); Swal.fire({icon:'info',title:'Fitur Belum Aktif',text:'Form titip barang akan aktif setelah integrasi backend.'});">
            <input type="hidden" name="gedung" value="<?php echo htmlspecialchars($gedung_aktif); ?>">
            
            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" class="form-input" placeholder="Misal: Helm KYT / Tas Laptop" required>
            </div>

            <div class="form-group">
                <label>Nomor Loker Kosong di <?php echo htmlspecialchars($gedung_aktif); ?> (1-150)</label>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-weight: 700; font-size: 14px; background: #e2e8f0; padding: 7px 12px; border-radius: 6px; border: 1px solid #cbd5e1;">
                        <?php echo $kode_gedung_prefix; ?>
                    </span>
                    <input type="number" name="no_loker" class="form-input" 
                           value="1"
                           placeholder="Contoh: 1 (Otomatis <?php echo $kode_gedung_prefix; ?>001)" 
                           min="1" max="150" required>
                </div>
                
                <div class="loker-info-box">
                    <strong><i class="fa-solid fa-info-circle"></i> Daftar Loker Kosong:</strong><br>
                    <span style="color: #16a34a; font-weight: 600;">
                        <?php echo implode(', ', array_slice($loker_kosong_list, 0, 30)); ?>
                        <?php echo count($loker_kosong_list) > 30 ? ' ...dan seterusnya' : ''; ?>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label>Keterangan / Ciri Barang (Opsional)</label>
                <input type="text" name="keterangan" class="form-input" 
                       placeholder="Misal: Warna hitam, casing gambar anime">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: PROFIL -->
<div class="modal-bg" id="modalProfile">
    <div class="modal-box">
        <h3><i class="fa-solid fa-user-gear"></i> Pengaturan Profil Saya</h3>
        <form onsubmit="event.preventDefault(); Swal.fire({icon:'info',title:'Fitur Belum Aktif',text:'Form profil akan aktif setelah integrasi backend.'});" enctype="multipart/form-data">
            <input type="hidden" name="gedung_asal" value="<?php echo htmlspecialchars($gedung_aktif); ?>">

            <div style="text-align: center; margin-bottom: 15px;">
                <div class="avatar-circle" style="width: 70px; height: 70px; margin: 0 auto 10px; font-size: 24px;">
                    <?php echo $inisial; ?>
                </div>
                <label style="cursor: pointer; font-size: 12px; color: #0284c7; font-weight: 600;">
                    <i class="fa-solid fa-camera"></i> Ubah Foto Profil
                    <input type="file" name="foto_profil" accept="image/*" style="display: none;">
                </label>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-input" 
                       value="<?php echo htmlspecialchars($nama_lengkap); ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-input" 
                       value="<?php echo htmlspecialchars($email_user); ?>" placeholder="alamat@email.com">
            </div>

            <div class="form-group">
                <label>No. WhatsApp / Telepon</label>
                <input type="text" name="no_hp" class="form-input" 
                       value="<?php echo htmlspecialchars($no_hp_user); ?>" placeholder="08123456789">
            </div>

            <div class="form-group">
                <label>Password Baru (Kosongkan jika tidak ingin diubah)</label>
                <input type="password" name="password_baru" class="form-input" 
                       placeholder="Masukkan password baru">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModalProfile()">Batal</button>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: RIWAYAT -->
<div class="modal-bg" id="modalHistory">
    <div class="modal-box modal-large">
        <h3><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Penitipan Barang (Sudah Diambil)</h3>
        
        <div style="overflow-x: auto; max-height: 400px;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA BARANG</th>
                        <th>GEDUNG</th>
                        <th>NO. LOKER</th>
                        <th>TGL SIMPAN</th>
                        <th>TGL AMBIL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($riwayat_list) > 0): ?>
                        <?php $no_h = 1; foreach ($riwayat_list as $rh): ?>
                            <tr>
                                <td><?php echo $no_h++; ?></td>
                                <td><strong><?php echo htmlspecialchars($rh['nama_barang']); ?></strong></td>
                                <td><?php echo htmlspecialchars($rh['nama_gedung']); ?></td>
                                <td><strong><?php echo htmlspecialchars($rh['no_loker']); ?></strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($rh['tgl_simpan'])); ?></td>
                                <td>
                                    <span style="color: #059669; font-weight: 600;">
                                        <?php echo date('d/m/Y H:i', strtotime($rh['tgl_ambil'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8; padding: 20px;">
                                Belum ada riwayat barang yang pernah diambil.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModalHistory()">Tutup</button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>