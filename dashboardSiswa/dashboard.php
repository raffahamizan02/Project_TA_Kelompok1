<?php
require_once __DIR__ . '/auth.php';

$gedung_aktif = isset($_GET['gedung']) ? $_GET['gedung'] : 'Gedung A';
$kode_gedung_prefix = strtoupper(trim(str_replace('Gedung', '', $gedung_aktif)));
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, trim($_GET['search'])) : '';

$page_title = 'Dashboard ' . ($role === 'admin' ? 'Admin' : 'Siswa') . ' - ' . $gedung_aktif;
$kapasitas_total = 150;

$pesan_error  = get_flash('error');
$pesan_sukses = get_flash('success');

// KARTU 1: Barang tersimpan
if ($role === 'admin') {
    $q_count_tersimpan = mysqli_query($koneksi, "
        SELECT COUNT(DISTINCT t.id_loker) as total 
        FROM transaksi_penitipan t 
        JOIN loker l ON t.id_loker = l.id_loker 
        JOIN gedung g ON l.id_gedung = g.id_gedung 
        WHERE g.nama_gedung = '$gedung_aktif' AND t.status = 'Tersimpan'
    ");
} else {
    $q_count_tersimpan = mysqli_query($koneksi, "
        SELECT COUNT(*) as total 
        FROM transaksi_penitipan t 
        JOIN loker l ON t.id_loker = l.id_loker 
        JOIN gedung g ON l.id_gedung = g.id_gedung 
        WHERE t.id_user = '$id_user' AND g.nama_gedung = '$gedung_aktif' AND t.status = 'Tersimpan'
    ");
}
$total_tersimpan = mysqli_fetch_assoc($q_count_tersimpan)['total'] ?? 0;

// KARTU 2: Sudah diambil
if ($role === 'admin') {
    $q_count_diambil = mysqli_query($koneksi, "
        SELECT COUNT(*) as total 
        FROM transaksi_penitipan t 
        JOIN loker l ON t.id_loker = l.id_loker 
        JOIN gedung g ON l.id_gedung = g.id_gedung 
        WHERE g.nama_gedung = '$gedung_aktif' AND t.status = 'Sudah Diambil'
    ");
} else {
    $q_count_diambil = mysqli_query($koneksi, "
        SELECT COUNT(*) as total 
        FROM transaksi_penitipan t 
        JOIN loker l ON t.id_loker = l.id_loker 
        JOIN gedung g ON l.id_gedung = g.id_gedung 
        WHERE t.id_user = '$id_user' AND g.nama_gedung = '$gedung_aktif' AND t.status = 'Sudah Diambil'
    ");
}
$total_diambil = mysqli_fetch_assoc($q_count_diambil)['total'] ?? 0;

// KARTU 3: Sisa loker
$q_global_tersimpan = mysqli_query($koneksi, "
    SELECT COUNT(DISTINCT t.id_loker) as total 
    FROM transaksi_penitipan t
    JOIN loker l ON t.id_loker = l.id_loker
    JOIN gedung g ON l.id_gedung = g.id_gedung
    WHERE g.nama_gedung = '$gedung_aktif' AND t.status = 'Tersimpan'
");
$total_global_tersimpan = mysqli_fetch_assoc($q_global_tersimpan)['total'] ?? 0;
$sisa_loker = $kapasitas_total - $total_global_tersimpan;

// Loker terpakai & kosong
$q_loker_terpakai = mysqli_query($koneksi, "
    SELECT DISTINCT l.no_loker FROM transaksi_penitipan t
    JOIN loker l ON t.id_loker = l.id_loker
    JOIN gedung g ON l.id_gedung = g.id_gedung
    WHERE g.nama_gedung = '$gedung_aktif' AND t.status = 'Tersimpan'
");

$loker_terpakai_num = [];
while ($row_l = mysqli_fetch_assoc($q_loker_terpakai)) {
    $num = (int) filter_var($row_l['no_loker'], FILTER_SANITIZE_NUMBER_INT);
    if ($num > 0) $loker_terpakai_num[] = $num;
}

$loker_kosong_list = [];
for ($i = 1; $i <= $kapasitas_total; $i++) {
    if (!in_array($i, $loker_terpakai_num)) {
        $loker_kosong_list[] = $kode_gedung_prefix . sprintf("%03d", $i);
    }
}

// AUTO-FILL NOMOR LOKER KOSONG TERKECIL
$next_loker = 1;
for ($i = 1; $i <= 150; $i++) {
    if (!in_array($i, $loker_terpakai_num)) {
        $next_loker = $i;
        break;
    }
}

// Tabel barang
$where_search = "";
if (!empty($search)) {
    $where_search = " AND (b.nama_barang LIKE '%$search%' OR l.no_loker LIKE '%$search%' OR b.keterangan LIKE '%$search%') ";
}

if ($role === 'admin') {
    $q_barang = mysqli_query($koneksi, "
        SELECT t.id_transaksi, b.id_barang, b.nama_barang, b.keterangan, 
               l.no_loker, g.nama_gedung, t.tgl_simpan, t.status, u.nama_lengkap
        FROM transaksi_penitipan t
        JOIN barang b ON t.id_barang = b.id_barang
        JOIN loker l ON t.id_loker = l.id_loker
        JOIN gedung g ON l.id_gedung = g.id_gedung
        LEFT JOIN users u ON t.id_user = u.id_user
        WHERE g.nama_gedung = '$gedung_aktif' AND t.status = 'Tersimpan' $where_search
        ORDER BY t.id_transaksi DESC
    ");
} else {
    $q_barang = mysqli_query($koneksi, "
        SELECT t.id_transaksi, b.id_barang, b.nama_barang, b.keterangan, 
               l.no_loker, g.nama_gedung, t.tgl_simpan, t.status
        FROM transaksi_penitipan t
        JOIN barang b ON t.id_barang = b.id_barang
        JOIN loker l ON t.id_loker = l.id_loker
        JOIN gedung g ON l.id_gedung = g.id_gedung
        WHERE t.id_user = '$id_user' AND g.nama_gedung = '$gedung_aktif' 
              AND t.status = 'Tersimpan' $where_search
        ORDER BY t.id_transaksi DESC
    ");
}

// Riwayat
if ($role === 'admin') {
    $q_history = mysqli_query($koneksi, "
        SELECT t.id_transaksi, b.nama_barang, b.keterangan, l.no_loker, 
               g.nama_gedung, t.tgl_simpan, t.tgl_ambil, u.nama_lengkap
        FROM transaksi_penitipan t
        JOIN barang b ON t.id_barang = b.id_barang
        JOIN loker l ON t.id_loker = l.id_loker
        JOIN gedung g ON l.id_gedung = g.id_gedung
        LEFT JOIN users u ON t.id_user = u.id_user
        WHERE t.status = 'Sudah Diambil'
        ORDER BY t.tgl_ambil DESC
    ");
} else {
    $q_history = mysqli_query($koneksi, "
        SELECT t.id_transaksi, b.nama_barang, b.keterangan, l.no_loker, 
               g.nama_gedung, t.tgl_simpan, t.tgl_ambil
        FROM transaksi_penitipan t
        JOIN barang b ON t.id_barang = b.id_barang
        JOIN loker l ON t.id_loker = l.id_loker
        JOIN gedung g ON l.id_gedung = g.id_gedung
        WHERE t.id_user = '$id_user' AND t.status = 'Sudah Diambil'
        ORDER BY t.tgl_ambil DESC
    ");
}

include __DIR__ . '/header.php';
?>

<?php if (!empty($pesan_error)): ?>
    <div class="alert-danger">
        <i class="fa-solid fa-triangle-exclamation" style="font-size: 18px;"></i>
        <div><?php echo $pesan_error; ?></div>
    </div>
<?php endif; ?>

<?php if (!empty($pesan_sukses)): ?>
    <div class="alert-success">
        <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
        <div><?php echo $pesan_sukses; ?></div>
    </div>
<?php endif; ?>

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
            <form method="GET" action="dashboard.php" class="search-box">
                <input type="hidden" name="gedung" value="<?php echo htmlspecialchars($gedung_aktif); ?>">
                <input type="text" name="search" class="search-input" placeholder="Cari barang / loker..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn-primary" style="padding: 8px 12px;"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>

            <?php if ($role === 'siswa'): ?>
                <button type="button" class="btn-primary" onclick="openModal()"><i class="fa-solid fa-plus"></i> + Titip Barang</button>
            <?php endif; ?>
        </div>
    </div>

    <table class="table-custom">
        <thead>
            <tr>
                <th>NO</th>
                <?php if ($role === 'admin'): ?><th>PEMILIK</th><?php endif; ?>
                <th>NAMA BARANG</th>
                <th>KETERANGAN</th>
                <th>NO. LOKER</th>
                <th>TANGGAL SIMPAN</th>
                <th>STATUS</th>
                <th>AKSI</th>
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
                                onclick="konfirmasiAmbil('ambil_barang.php?gedung=<?php echo urlencode($gedung_aktif); ?>&id_transaksi=<?php echo $row['id_transaksi']; ?>', '<?php echo htmlspecialchars($row['no_loker']); ?>');">
                            <i class="fa-solid fa-hand-holding"></i> Ambil
                        </button>
                    </td>
                </tr>
            <?php
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="<?php echo ($role === 'admin') ? '8' : '7'; ?>" 
                        style="text-align: center; color: #94a3b8; padding: 30px;">
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
        <form method="POST" action="titip_barang.php">
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
                           value="<?php echo $next_loker; ?>"
                           placeholder="Contoh: 1 (Otomatis <?php echo $kode_gedung_prefix; ?>001)" 
                           min="1" max="150" required>
                </div>
                
                <div class="loker-info-box">
                    <strong><i class="fa-solid fa-info-circle"></i> Daftar Loker Kosong:</strong><br>
                    <?php if (count($loker_kosong_list) > 0): ?>
                        <span style="color: #16a34a; font-weight: 600;">
                            <?php echo implode(', ', array_slice($loker_kosong_list, 0, 30)); ?>
                            <?php echo count($loker_kosong_list) > 30 ? ' ...dan seterusnya' : ''; ?>
                        </span>
                    <?php else: ?>
                        <span style="color: #dc2626; font-weight: 600;">Semua Loker Penuh!</span>
                    <?php endif; ?>
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
        <form method="POST" action="update_profil.php" enctype="multipart/form-data">
            <input type="hidden" name="gedung_asal" value="<?php echo htmlspecialchars($gedung_aktif); ?>">

            <div style="text-align: center; margin-bottom: 15px;">
                <div class="avatar-circle" style="width: 70px; height: 70px; margin: 0 auto 10px; font-size: 24px;">
                    <?php if (!empty($foto_user) && file_exists('uploads/' . $foto_user)): ?>
                        <img src="uploads/<?php echo htmlspecialchars($foto_user); ?>" alt="Profil">
                    <?php else: ?>
                        <?php echo $inisial; ?>
                    <?php endif; ?>
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
                        <?php if ($role === 'admin'): ?><th>PEMILIK</th><?php endif; ?>
                        <th>NAMA BARANG</th>
                        <th>GEDUNG</th>
                        <th>NO. LOKER</th>
                        <th>TGL SIMPAN</th>
                        <th>TGL AMBIL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no_h = 1;
                    if ($q_history && mysqli_num_rows($q_history) > 0):
                        while ($rh = mysqli_fetch_assoc($q_history)):
                    ?>
                        <tr>
                            <td><?php echo $no_h++; ?></td>
                            <?php if ($role === 'admin'): ?>
                                <td><strong><?php echo htmlspecialchars($rh['nama_lengkap'] ?? '-'); ?></strong></td>
                            <?php endif; ?>
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
                    <?php
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="<?php echo ($role === 'admin') ? '7' : '6'; ?>" 
                                style="text-align: center; color: #94a3b8; padding: 20px;">
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