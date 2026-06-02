CREATE DATABASE jejak_berkas;
USE jejak_berkas;

CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('dinas') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE layanan_ktp (
    id_layanan INT AUTO_INCREMENT PRIMARY KEY,
    nomor_registrasi VARCHAR(50) NOT NULL UNIQUE,
    nik_warga VARCHAR(16) NOT NULL,
    nama_warga VARCHAR(100) NOT NULL,
    alamat_warga TEXT NOT NULL,
    status_sekarang ENUM('Loket Pendaftaran', 'Verifikasi Berkas', 'Perekaman Biometrik', 'Pencetakan KTP', 'Siap Diambil') DEFAULT 'Loket Pendaftaran',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE tracking_log (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_layanan INT NOT NULL,
    status ENUM('Loket Pendaftaran', 'Verifikasi Berkas', 'Perekaman Biometrik', 'Pencetakan KTP', 'Siap Diambil') NOT NULL,
    keterangan TEXT,
    id_petugas INT NOT NULL,
    waktu_log TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_layanan) REFERENCES layanan_ktp(id_layanan) ON DELETE CASCADE,
    FOREIGN KEY (id_petugas) REFERENCES users(id_user)
);

-- 4. Suntik Akun Petugas Dinas Langsung Menggunakan Enkripsi MD5 (Pasti Cocok dengan Script PHP)
INSERT INTO users (id_user, username, password, nama_lengkap, role) VALUES 
(1, 'admincapil', MD5('capil123'), 'Operator Registrasi Capil', 'dinas');