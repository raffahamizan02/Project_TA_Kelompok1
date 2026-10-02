<?php
$page_title = $page_title ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - LOKIFY</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Roboto:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', 'Roboto', sans-serif; }
    
    :root {
        --sidebar-width: 250px;
        --sidebar-collapsed-width: 70px;
        --accent-color: <?php echo ($role === 'admin') ? '#AFDDFF' : '#FF9149'; ?>;
        --accent-text: <?php echo ($role === 'admin') ? '#0f233a' : '#ffffff'; ?>;
    }

    body { background-color: #f1f5f9; color: #1e293b; display: flex; min-height: 100vh; }

    /* SIDEBAR */
    .sidebar { width: var(--sidebar-width); background: #0f233a; color: #ffffff; display: flex; flex-direction: column; justify-content: space-between; transition: width 0.3s ease; flex-shrink: 0; }
    .brand-area { padding: 20px; border-bottom: 1px solid #1e3a8a; display: flex; align-items: center; gap: 12px; }
    .brand-area img { height: 40px; width: auto; }
    .brand-text h2 { font-size: 14px; font-weight: 800; text-transform: uppercase; line-height: 1.2; }
    .brand-text h2 span { color: var(--accent-color); }
    .nav-menu { list-style: none; padding: 15px 0; }
    .menu-heading { font-size: 10px; font-weight: 700; color: #64748b; padding: 12px 20px 4px; text-transform: uppercase; letter-spacing: 1px; }
    .nav-item a { display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px; font-weight: 500; border-left: 4px solid transparent; transition: all 0.2s; }
    .nav-item a:hover { background: rgba(255, 255, 255, 0.05); color: #ffffff; }
    .nav-item.active a { background: #1e293b; color: #ffffff; font-weight: 700; border-left-color: var(--accent-color); }
    .sub-menu-container { display: none; background: rgba(0, 0, 0, 0.15); }
    .sub-menu-container .nav-item a { padding-left: 45px; font-size: 13px; }
    .arrow-icon-menu { transition: transform 0.3s ease; font-size: 12px; }
    .sidebar-footer { padding: 20px; font-size: 11px; color: #64748b; }
    .badge-notif { background: #ef4444; color: white; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 700; margin-left: auto; }

    /* MAIN */
    .main-wrapper { flex: 1; display: flex; flex-direction: column; min-width: 0; }
    .top-header { background: #ffffff; padding: 12px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; }
    .page-title { font-size: 18px; font-weight: 700; color: #0f233a; }
    .btn-toggle-sidebar { background: #f8fafc; border: 1px solid #cbd5e1; font-size: 16px; color: #334155; cursor: pointer; padding: 6px 12px; border-radius: 6px; transition: all 0.2s; }
    .btn-toggle-sidebar:hover { background-color: #e2e8f0; }

    /* COLLAPSED */
    body.sidebar-collapsed .sidebar { width: var(--sidebar-collapsed-width) !important; }
    body.sidebar-collapsed .brand-text,
    body.sidebar-collapsed .nav-item a span,
    body.sidebar-collapsed .arrow-icon-menu,
    body.sidebar-collapsed .menu-heading,
    body.sidebar-collapsed .sidebar-footer { display: none !important; }
    body.sidebar-collapsed .brand-area,
    body.sidebar-collapsed .nav-item a { justify-content: center; padding: 15px 0; }
    body.sidebar-collapsed .sub-menu-container .nav-item a { padding-left: 0; text-align: center; }

    /* PROFILE */
    .profile-dropdown-container { position: relative; }
    .profile-btn { display: flex; align-items: center; gap: 12px; padding: 6px 14px 6px 8px; border-radius: 30px; background: #f8fafc; border: 1px solid #e2e8f0; cursor: pointer; user-select: none; }
    .profile-btn:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .avatar-circle { width: 36px; height: 36px; border-radius: 50%; background: #0f233a; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; overflow: hidden; flex-shrink: 0; }
    .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
    .user-details { text-align: left; }
    .user-name { font-size: 13px; font-weight: 700; color: #0f233a; line-height: 1.1; }
    .role-badge { font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .arrow-icon { font-size: 11px; color: #64748b; margin-left: 2px; transition: transform 0.2s ease; }
    .profile-dropdown-container.active .arrow-icon { transform: rotate(180deg); }

    .dropdown-menu { display: none; position: absolute; right: 0; top: calc(100% + 8px); width: 210px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 10px 25px rgba(15, 35, 58, 0.1); z-index: 100; overflow: hidden; }
    .dropdown-menu.show { display: block; }
    .dropdown-header { padding: 12px 16px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
    .dropdown-header p { font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 2px; }
    .dropdown-header strong { font-size: 13px; color: #0f233a; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .dropdown-item { display: flex; align-items: center; gap: 10px; padding: 12px 16px; font-size: 13px; color: #334155; text-decoration: none; font-weight: 500; cursor: pointer; }
    .dropdown-item:hover { background: #f8fafc; }
    .dropdown-item.logout-item { color: #dc2626; font-weight: 600; border-top: 1px solid #f1f5f9; }
    .dropdown-item.logout-item:hover { background: #fef2f2; }

    /* CONTENT */
    .content-area { padding: 25px 30px; }
    .stats-row { display: flex; gap: 20px; margin-bottom: 25px; flex-wrap: wrap; }
    .stat-card { flex: 1; min-width: 200px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 18px; display: flex; align-items: center; gap: 15px; }
    .stat-icon { width: 45px; height: 45px; border-radius: 4px; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #0f233a; flex-shrink: 0; }
    .stat-data h4 { font-size: 12px; color: #64748b; font-weight: 500; text-transform: uppercase; }
    .stat-data h2 { font-size: 20px; font-weight: 800; color: #0f233a; }

    .panel { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; }
    .panel-header { padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
    .panel-title { font-size: 14px; font-weight: 700; color: #0f233a; text-transform: uppercase; }

    .btn-primary { background: var(--accent-color); color: var(--accent-text); border: none; padding: 9px 16px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
    .btn-primary:hover { opacity: 0.9; }
    .search-box { display: flex; gap: 8px; align-items: center; }
    .search-input { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; outline: none; }
    .search-input:focus { border-color: var(--accent-color); }

    .table-custom { width: 100%; border-collapse: collapse; }
    .table-custom th { background: #f8fafc; padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
    .table-custom td { padding: 12px 20px; font-size: 13px; border-bottom: 1px solid #f1f5f9; color: #334155; }

    .badge-tersimpan { background-color: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 20px; font-weight: 600; font-size: 12px; display: inline-block; }
    .btn-ambil { background-color: #ef4444; color: white; padding: 5px 12px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; border: none; }
    .btn-ambil:hover { background-color: #dc2626; }

    /* MODAL */
    .modal-bg { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 99; }
    .modal-box { background: #ffffff; width: 450px; max-width: 90%; border-radius: 8px; padding: 20px; border: 1px solid #cbd5e1; max-height: 90vh; overflow-y: auto; }
    .modal-box.modal-large { width: 800px; }
    .modal-box h3 { font-size: 16px; font-weight: 700; margin-bottom: 15px; color: #0f233a; border-bottom: 1px solid #eee; padding-bottom: 10px; display: flex; align-items: center; gap: 8px; }
    .form-group { margin-bottom: 14px; }
    .form-group label { display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px; color: #334155; }
    .form-input { width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; outline: none; }
    .form-input:focus { border-color: var(--accent-color); }
    .loker-info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; font-size: 12px; color: #475569; margin-top: 6px; line-height: 1.5; max-height: 90px; overflow-y: auto; }
    .modal-footer { margin-top: 18px; display: flex; justify-content: flex-end; gap: 8px; }
    .btn-secondary { background: #e2e8f0; color: #334155; border: none; padding: 8px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-secondary:hover { background: #cbd5e1; }

    /* RESPONSIVE MOBILE */
    @media (max-width: 768px) {
        .sidebar { position: fixed; left: -250px; z-index: 999; height: 100vh; box-shadow: 2px 0 10px rgba(0,0,0,0.2); transition: left 0.3s ease; }
        body.sidebar-mobile-open .sidebar { left: 0; }
        .main-wrapper { margin-left: 0 !important; }
        .stats-row { flex-direction: column; }
        .stat-card { min-width: 100%; }
        .modal-box { width: 95% !important; }
        .modal-box.modal-large { width: 95% !important; }
        .content-area { padding: 15px; }
        .top-header { padding: 10px 15px; }
        .page-title { font-size: 14px; }
        .user-details { display: none; }
    }
    </style>
</head>
<body>

<?php include __DIR__ . '/sidebar.php'; ?>

<div class="main-wrapper">
    <div class="top-header">
        <div style="display: flex; align-items: center; gap: 15px;">
            <button type="button" class="btn-toggle-sidebar" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="page-title"><?php echo htmlspecialchars($page_title); ?></div>
        </div>
        
        <div class="profile-dropdown-container" id="profileDropdownContainer">
            <div class="profile-btn" onclick="toggleDropdown()">
                <div class="avatar-circle">
                    <?php if (!empty($foto_user) && file_exists('uploads/' . $foto_user)): ?>
                        <img src="uploads/<?php echo htmlspecialchars($foto_user); ?>" alt="Profil">
                    <?php else: ?>
                        <?php echo $inisial; ?>
                    <?php endif; ?>
                </div>
                <div class="user-details">
                    <div class="user-name"><?php echo htmlspecialchars($nama_lengkap); ?></div>
                    <div class="role-badge"><?php echo htmlspecialchars($role); ?></div>
                </div>
                <i class="fa-solid fa-chevron-down arrow-icon"></i>
            </div>

            <div class="dropdown-menu" id="userMenu">
                <div class="dropdown-header">
                    <p>Masuk sebagai</p>
                    <strong><?php echo htmlspecialchars($nama_lengkap); ?></strong>
                </div>
                <a href="javascript:void(0);" onclick="openModalProfile()" class="dropdown-item">
                    <i class="fa-solid fa-id-card"></i> Edit Profil
                </a>
                <a href="../login/logout.php" class="dropdown-item logout-item">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </a>
            </div>
        </div>
    </div>

    <div class="content-area">