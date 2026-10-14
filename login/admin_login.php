<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/functions.php';

// Kalau sudah login sebagai admin, langsung ke dashboard admin
if (is_admin()) {
    redirect('../DashboardAdmin/index.php');
}

$error_msg = get_flash('error');
$filled_username = $_SESSION['form_username'] ?? '';
unset($_SESSION['form_username']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - LOKIFY</title>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Roboto', sans-serif; }

    /* BACKGROUND PUTIH seperti login siswa */
    body {
        background-color: #f1f5f9;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    /* Kotak login tetap GELAP */
    .card-login {
        width: 100%;
        max-width: 450px;
        background: #1e293b;
        border-radius: 12px;
        border: 1px solid #334155;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .card-header-custom {
        padding: 35px 20px 25px 20px;
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
        color: #f1f5f9;
        line-height: 1.3;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        text-align: center;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .card-header-custom h1 span { color: #AFDDFF; }

    .role-title {
        width: 100%;
        padding: 12px;
        background: #0f233a;
        border-top: 1px solid #1e3a5f;
        border-bottom: 1px solid #1e3a5f;
        text-align: center;
        font-size: 13px;
        font-weight: 700;
        color: #AFDDFF;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .card-body-inner {
        width: 100%;
        padding: 25px 35px 30px 35px;
    }

    .alert-error {
        background: #450a0a; border: 1px solid #991b1b; color: #fca5a5;
        padding: 10px; border-radius: 6px; font-size: 12px;
        margin-bottom: 15px; text-align: center;
    }

    .form-group { margin-bottom: 15px; width: 100%; }
    .form-group label {
        display: block; font-size: 12px; font-weight: 600;
        color: #cbd5e1; margin-bottom: 6px;
    }

    .input-group { position: relative; display: flex; align-items: center; width: 100%; }
    .input-group i {
        position: absolute; left: 12px; color: #64748b; font-size: 13px;
    }
    .input-control {
        width: 100%; padding: 10px 12px 10px 35px;
        font-size: 13px; border: 1px solid #334155;
        border-radius: 6px; outline: none;
        color: #f1f5f9; background: #0f172a;
    }
    .input-control:focus { border-color: #AFDDFF; background: #0f172a; }
    .input-control::placeholder { color: #475569; }

    .btn-submit {
        width: 100%; padding: 11px; background: #AFDDFF;
        color: #0f233a; border: none; border-radius: 6px;
        font-size: 13px; font-weight: 700; cursor: pointer;
        margin-top: 10px; transition: opacity 0.2s;
    }
    .btn-submit:hover { opacity: 0.9; }

    .card-footer-custom {
        width: 100%; padding: 15px 35px 25px 35px;
        text-align: center; font-size: 11px; color: #475569;
    }
    </style>
</head>
<body>

    <div class="card-login">

        <div class="card-header-custom">
            <img src="logoLOKIFY.png" alt="Logo LOKIFY" onerror="this.style.display='none'">
            <h1>SISTEM PENDATAAN<br><span>BARANG LOKER</span> SEKOLAH</h1>
        </div>

        <div class="role-title">
            <i class="fa-solid fa-user-shield"></i> Administrator
        </div>

        <div class="card-body-inner">

            <?php if (!empty($error_msg)): ?>
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login_process.php">
                <input type="hidden" name="role" value="admin">

                <div class="form-group">
                    <label>Username Admin</label>
                    <div class="input-group">
                        <i class="fa-solid fa-user-gear"></i>
                        <input type="text" name="username" class="input-control"
                               placeholder="Masukkan Username Admin"
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

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-right-to-bracket"></i> Login Admin
                </button>
            </form>

        </div>

        <div class="card-footer-custom">
            &copy; 2026 SMK PGRI 3 Malang. All Rights Reserved.
        </div>

    </div>

</body>
</html>