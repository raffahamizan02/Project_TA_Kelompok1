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

    // Jika memilih mode Admin
    if (role === 'admin') {
        tabAdmin.className = "tab-btn active theme-blue";
        btnLogin.style.background = "#AFDDFF";
        btnLogin.style.color      = "#0f233a";

        if (labelUsername) labelUsername.innerText = "Username Admin";
        if (iconUsername)  iconUsername.className  = "fa-solid fa-user-gear";
        
        // Sembunyikan opsi register untuk admin
        if (btnToRegister) btnToRegister.style.display = "none";
        showLoginForm();
    } else {
        // Mode Siswa
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

// Inisialisasi awal saat DOM siap
document.addEventListener('DOMContentLoaded', function () {
    const roleInput = document.getElementById('login-role-input');
    const initialRole = roleInput ? roleInput.value : 'siswa';
    switchRole(initialRole);
});