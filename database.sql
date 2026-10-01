CREATE DATABASE IF NOT EXISTS db_ta CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_ta;

CREATE TABLE IF NOT EXISTS users (
    id_user INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin','siswa') NOT NULL DEFAULT 'siswa',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_username_role (username,role)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS barang (
    id_barang INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_user INT UNSIGNED NOT NULL,
    nama_barang VARCHAR(100) NOT NULL,
    no_loker TINYINT UNSIGNED NOT NULL,
    tgl_simpan DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Tersimpan','Diambil') NOT NULL DEFAULT 'Tersimpan',
    CONSTRAINT fk_barang_user FOREIGN KEY (id_user) REFERENCES users(id_user) ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_barang_status(status),
    INDEX idx_barang_loker_status(no_loker,status),
    INDEX idx_barang_tanggal(tgl_simpan)
) ENGINE=InnoDB;

INSERT INTO users(username,password,nama_lengkap,role)
VALUES('admin','$2y$12$vMSD2t.R7P1P1Z/m1R0MMOty2CXgXlTkODbdDJBzk2Bsbgf2JjbSC','Administrator','admin')
ON DUPLICATE KEY UPDATE username=VALUES(username);