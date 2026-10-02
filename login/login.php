<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/functions.php';

if (is_logged_in()) {
    redirect('../dashboardSiswa/dashboard.php');
}

$error_msg   = get_flash('error');
$success_msg = get_flash('success');

$active_role     = $_SESSION['form_role']     ?? 'siswa';
$filled_username = $_SESSION['form_username'] ?? '';

unset($_SESSION['form_role'], $_SESSION['form_username']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register - Sistem Loker Sekolah</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
    /* ============ LOGIN CSS ============ */
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Roboto', sans-serif; }

    body {
        background-color: #f1f5f9;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .card-login {
        width: 100%;
        max-width: 450px;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .card-header-custom {
        padding: 35px 20px 20px 20px;
        width: 100%;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .card-header-custom img {
        height: 70px;
        width: auto;
        margin-bottom: 15px;
        display: block;
        object-fit: contain;
    }

    .card-header-custom h1 {
        font-size: 18px;
        font-weight: 800;
        color: #0f233a;
        line-height: 1.3;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        text-align: center;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .card-header-custom h1 span {
        color: #FF9149;
    }

    .role-tabs {
        width: 100%;
        display: flex;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
    }

    .tab-btn {
        flex: 1;
        padding: 12px;
        text-align: center;
        background: #f8fafc;
        border: none;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .tab-btn.active.theme-orange {
        background: #ffffff;
        color: #FF9149;
        border-bottom: 3px solid #FF9149;
    }

    .tab-btn.active.theme-blue {
        background: #ffffff;
        color: #0f233a;
        border-bottom: 3px solid #AFDDFF;
    }

    .card-body-inner {
        width: 100%;
        padding: 25px 35px 30px 35px;
    }

    .alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
        padding: 10px;
        border-radius: 6px;
        font-size: 12px;
        margin-bottom: 15px;
        text-align: center;
    }

    .alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #16a34a;
        padding: 10px;
        border-radius: 6px;
        font-size: 12px;
        margin-bottom: 15px;
        text-align: center;
    }

    .form-group {
        margin-bottom: 15px;
        width: 100%;
    }

    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
    }

    .input-group {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .input-group i {
        position: absolute;
        left: 12px;
        color: #94a3b8;
        font-size: 13px;
    }

    .input-control {
        width: 100%;
        padding: 10px 12px 10px 35px;
        font-size: 13px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        outline: none;
        color: #1e293b;
        background: #f8fafc;
    }

    .input-control:focus {
        border-color: #FF9149;
        background: #ffffff;
    }

    .btn-submit {
        width: 100%;
        padding: 11px;
        background: #FF9149;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 10px;
        transition: background 0.2s;
    }

    .btn-submit:hover {
        opacity: 0.9;
    }

    .btn-register-gray {
        width: 100%;
        padding: 11px;
        background: #e2e8f0;
        color: #475569;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 8px;
        text-align: center;
        display: block;
        transition: background 0.2s;
    }

    .btn-register-gray:hover {
        background: #cbd5e1;
        color: #1e293b;
    }

    .form-section {
        display: none;
        width: 100%;
    }

    .form-section.active {
        display: block;
    }

    .card-footer-custom {
        width: 100%;
        padding: 15px 35px 25px 35px;
        text-align: center;
        font-size: 11px;
        color: #94a3b8;
    }
    </style>
</head>
<body>

    <div class="card-login">
        
        <div class="card-header-custom">
            <img src="logoLOKIFY.png" alt="Logo LOKIFY" onerror="this.style.display='none'">
            <h1>SISTEM PENDATAAN<br><span>BARANG LOKER</span> SEKOLAH</h1>
        </div>

        <div class="role-tabs" id="tab-container">
            <button class="tab-btn" id="tab-siswa" onclick="switchRole('siswa')">
                <i class="fa-solid fa-user-graduate"></i> Akses Siswa
            </button>
            <button class="tab-btn" id="tab-admin" onclick="switchRole('admin')">
                <i class="fa-solid fa-user-shield"></i> Akses Admin
            </button>
        </div>

        <div class="card-body-inner">
            
            <?php if (!empty($error_msg)): ?>
                <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error_msg; ?></div>
            <?php endif; ?>

            <?php if (!empty($success_msg)): ?>
                <div class="alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $success_msg; ?></div>
            <?php endif; ?>

            <form id="form-login" class="form-section active" method="POST" action="login_process.php">
                <input type="hidden" name="role" id="login-role-input" value="<?php echo htmlspecialchars($active_role); ?>">

                <div class="form-group">
                    <label id="label-username">Username / NISN</label>
                    <div class="input-group">
                        <i class="fa-solid fa-user" id="icon-username"></i>
                        <input type="text" name="username" class="input-control" id="login-username-field" 
                               placeholder="Masukkan Username / NISN" 
                               value="<?php echo htmlspecialchars($filled_username); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-group">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" class="input-control" 
                               placeholder="Masukkan Password" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="btn-login-submit">Login</button>

                <button type="button" class="btn-register-gray" id="btn-to-register" onclick="showRegisterForm()">
                    Register Siswa
                </button>
            </form>

            <form id="form-register" class="form-section" method="POST" action="register_process.php">
                <div class="form-group">
                    <label>Nama Lengkap Siswa</label>
                    <div class="input-group">
                        <i class="fa-solid fa-id-card"></i>
                        <input type="text" name="reg_nama" class="input-control" 
                               placeholder="Masukkan nama lengkap" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Username / NISN Baru</label>
                    <div class="input-group">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="reg_username" class="input-control" 
                               placeholder="Buat Username / NISN" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password Baru</label>
                    <div class="input-group">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="reg_password" class="input-control" 
                               placeholder="Buat Password (min 6 karakter)" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="btn-reg-submit">
                    Daftar Akun Siswa
                </button>

                <button type="button" class="btn-register-gray" onclick="showLoginForm()">
                    Kembali Ke Login
                </button>
            </form>

        </div>

        <div class="card-footer-custom">
            &copy; 2026 SMK PGRI 3 Malang. All Rights Reserved.
        </div>

    </div>

    <script>
    /* ============ LOGIN JS ============ */
    function switchRole(role) {
        const tabSiswa      = document.getElementById('tab-siswa');
        const tabAdmin      = document.getElementById('tab-admin');
        const btnLogin      = document.getElementById('btn-login-submit');
        const btnToRegister = document.getElementById('btn-to-register');
        const roleInput     = document.getElementById('login-role-input');
        const labelUsername = document.getElementById('label-username');
        const iconUsername  = document.getElementById('icon-username');

        if (roleInput) roleInput.value = role;

        tabSiswa.className = "tab-btn";
        tabAdmin.className = "tab-btn";

        if (role === 'admin') {
            tabAdmin.className = "tab-btn active theme-blue";
            btnLogin.style.background = "#AFDDFF";
            btnLogin.style.color      = "#0f233a";

            if (labelUsername) labelUsername.innerText = "Username Admin";
            if (iconUsername)  iconUsername.className  = "fa-solid fa-user-gear";
            
            if (btnToRegister) btnToRegister.style.display = "none";
            showLoginForm();
        } else {
            tabSiswa.className = "tab-btn active theme-orange";
            btnLogin.style.background = "#FF9149";
            btnLogin.style.color      = "#ffffff";

            if (labelUsername) labelUsername.innerText = "Username / NISN";
            if (iconUsername)  iconUsername.className  = "fa-solid fa-user";

            if (btnToRegister) btnToRegister.style.display = "block";
        }
    }

    function showRegisterForm() {
        document.getElementById('form-login').classList.remove('active');
        document.getElementById('form-register').classList.add('active');
    }

    function showLoginForm() {
        document.getElementById('form-register').classList.remove('active');
        document.getElementById('form-login').classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const roleInput = document.getElementById('login-role-input');
        const initialRole = roleInput ? roleInput.value : 'siswa';
        switchRole(initialRole);
    });
    </script>
</body>
</html>