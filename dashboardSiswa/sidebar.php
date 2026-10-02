<?php
$menu_gedung  = ['Gedung A', 'Gedung B', 'Gedung C', 'Gedung D'];
$gedung_aktif = $gedung_aktif ?? 'Gedung A';
?>
<div class="sidebar">
    <div>
        <div class="brand-area">
            <img src="logoLOKIFY.png" alt="Logo" onerror="this.style.display='none'">
            <div class="brand-text">
                <h2>LOKER <span>SEKOLAH</span></h2>
            </div>
        </div>

        <ul class="nav-menu">
            <div class="menu-heading">Utama</div>
            
            <li class="nav-item">
                <a href="javascript:void(0);" onclick="toggleGedungMenu()" style="display: flex; justify-content: space-between; align-items: center;">
                    <span><i class="fa-solid fa-gauge"></i> Dashboard <?php echo ($role === 'admin') ? 'Admin' : 'Siswa'; ?></span>
                    <i class="fa-solid fa-chevron-down arrow-icon-menu" id="arrowGedung"></i>
                </a>
            </li>

            <div class="sub-menu-container" id="subMenuGedung">
                <?php foreach ($menu_gedung as $g): ?>
                    <li class="nav-item <?php echo ($gedung_aktif === $g) ? 'active' : ''; ?>">
                        <a href="dashboard.php?gedung=<?php echo urlencode($g); ?>">
                            <i class="fa-solid fa-building"></i> <span><?php echo $g; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </div>

            <li class="nav-item">
                <a href="javascript:void(0);" onclick="openModalHistory()">
                    <i class="fa-solid fa-clock-rotate-left"></i> 
                    <span>Riwayat Penitipan</span>
                    <?php if (($total_tersimpan ?? 0) > 0): ?>
                        <span class="badge-notif"><?php echo $total_tersimpan; ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <div class="menu-heading">Pengaturan</div>

            <li class="nav-item">
                <a href="javascript:void(0);" onclick="openModalProfile()">
                    <i class="fa-solid fa-user-gear"></i> <span>Pengaturan Profil</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="sidebar-footer">
        SMK PGRI 3 Malang
    </div>
</div>