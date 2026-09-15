<?php
session_start();
require_once 'koneksi.php';

$error_msg   = '';
$success_msg = '';

// Default role aktif
$active_role     = $_POST['role'] ?? 'siswa';
$filled_username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action_type = $_POST['action_type'] ?? '';

    // 1. PROSES LOGIN (Siswa & Admin)
    if ($action_type === 'login_user') {
        $username    = isset($_POST['username']) ? mysqli_real_escape_string($koneksi, trim($_POST['username'])) : '';
        $password    = isset($_POST['password']) ? trim($_POST['password']) : '';
        $role        = isset($_POST['role']) ? mysqli_real_escape_string($koneksi, trim($_POST['role'])) : 'siswa';
        $active_role = $role;

        if (!empty($username) && !empty($password)) {
            $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username' AND role = '$role'");
            if ($query && mysqli_num_rows($query) > 0) {
                $user = mysqli_fetch_assoc($query);
                if (password_verify($password, $user['password']) || md5($password) === $user['password'] || $password === $user['password']) {
                    $_SESSION['id_user']      = $user['id_user'];
                    $_SESSION['username']     = $user['username'];
                    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                    $_SESSION['role']         = $user['role'];
                    header("Location: dashboard.php");
                    exit();
                } else { 
                    $error_msg = "Password yang Anda masukkan salah!"; 
                }
            } else { 
                $error_msg = "Akun " . ucfirst($role) . " dengan username tersebut tidak ditemukan!"; 
            }
        } else { 
            $error_msg = "Username dan Password wajib diisi!"; 
        }
    }

    // 2. PROSES REGISTER (Khusus Siswa)
    elseif ($action_type === 'register_user') {
        $nama_lengkap = isset($_POST['reg_nama']) ? mysqli_real_escape_string($koneksi, trim($_POST['reg_nama'])) : '';
        $username     = isset($_POST['reg_username']) ? mysqli_real_escape_string($koneksi, trim($_POST['reg_username'])) : '';
        $password     = isset($_POST['reg_password']) ? trim($_POST['reg_password']) : '';
        $role         = 'siswa';
        $active_role  = 'siswa';

        if (!empty($nama_lengkap) && !empty($username) && !empty($password)) {
            $check = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username' AND role = 'siswa'");
            if ($check && mysqli_num_rows($check) > 0) {
                $error_msg = "Username / NISN '$username' sudah terdaftar sebagai Siswa!";
            } else {
                $pass_hash = password_hash($password, PASSWORD_DEFAULT);
                $insert    = mysqli_query($koneksi, "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$pass_hash', '$nama_lengkap', '$role')");
                if ($insert) {
                    $success_msg     = "Registrasi Siswa berhasil! Silakan login.";
                    $filled_username = $username;
                } else {
                    $error_msg = "Gagal mendaftarkan akun: " . mysqli_error($koneksi);
                }
            }
        } else { 
            $error_msg = "Semua field pendaftaran wajib diisi!"; 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register - Sistem Loker Sekolah</title>
    
    <!-- External Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- External Style CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="card-login">
        
        <!-- HEADER -->
        <div class="card-header-custom">
            <img src="logoLOKIFY.png" alt="Logo LOKIFY" onerror="this.style.display='none'">
            <h1>SISTEM PENDATAAN<br><span>BARANG LOKER</span> SEKOLAH</h1>
        </div>

        <!-- TAB SWITCHER -->
        <div class="role-tabs" id="tab-container">
            <button class="tab-btn" id="tab-siswa" onclick="switchRole('siswa')">
                <i class="fa-solid fa-user-graduate"></i> Akses Siswa
            </button>
            <button class="tab-btn" id="tab-admin" onclick="switchRole('admin')">
                <i class="fa-solid fa-user-shield"></i> Akses Admin
            </button>
        </div>

        <!-- BODY -->
        <div class="card-body-inner">
            
            <?php if (!empty($error_msg)): ?>
                <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <?php if (!empty($success_msg)): ?>
                <div class="alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>

            <!-- FORM LOGIN (Siswa & Admin) -->
            <form id="form-login" class="form-section active" method="POST" action="login.php">
                <input type="hidden" name="action_type" value="login_user">
                <input type="hidden" name="role" id="login-role-input" value="<?php echo htmlspecialchars($active_role); ?>">

                <div class="form-group">
                    <label id="label-username">Username / NISN</label>
                    <div class="input-group">
                        <i class="fa-solid fa-user" id="icon-username"></i>
                        <input type="text" name="username" class="input-control" id="login-username-field" placeholder="Masukkan Username / NISN" value="<?php echo htmlspecialchars($filled_username); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-group">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" class="input-control" placeholder="Masukkan Password" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="btn-login-submit">
                    Login
                </button>

                <button type="button" class="btn-register-gray" id="btn-to-register" onclick="showRegisterForm()">
                    Register Siswa
                </button>
            </form>

            <!-- FORM REGISTER (Khusus Siswa) -->
            <form id="form-register" class="form-section" method="POST" action="login.php">
                <input type="hidden" name="action_type" value="register_user">

                <div class="form-group">
                    <label>Nama Lengkap Siswa</label>
                    <div class="input-group">
                        <i class="fa-solid fa-id-card"></i>
                        <input type="text" name="reg_nama" class="input-control" placeholder="Masukkan nama lengkap" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Username / NISN Baru</label>
                    <div class="input-group">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="reg_username" class="input-control" placeholder="Buat Username / NISN" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password Baru</label>
                    <div class="input-group">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="reg_password" class="input-control" placeholder="Buat Password" required>
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

    <!-- External Script JS -->
    <script src="script.js"></script>
</body>
</html>