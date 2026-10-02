<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function clean_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function format_no_loker($nama_gedung, $no_loker_int) {
    $prefix = strtoupper(trim(str_replace('Gedung', '', $nama_gedung)));
    return $prefix . sprintf("%03d", (int)$no_loker_int);
}

function get_inisial($nama) {
    return strtoupper(substr(trim($nama), 0, 1));
}

function is_logged_in() {
    return isset($_SESSION['id_user']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function set_flash($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

function get_flash($type) {
    if (isset($_SESSION['flash'][$type])) {
        $msg = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $msg;
    }
    return null;
}
?>