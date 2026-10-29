CREATE DATABASE IF NOT EXISTS aksibersama CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aksibersama;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS registrations;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS admins;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE admins (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    category ENUM('lingkungan','pendidikan','sosial','kesehatan','kemanusiaan','lainnya') NOT NULL DEFAULT 'sosial',
    event_date DATE NOT NULL,
    location VARCHAR(180) NOT NULL,
    capacity INT UNSIGNED NOT NULL DEFAULT 30,
    description TEXT NOT NULL,
    requirements TEXT NULL,
    image_url VARCHAR(500) NULL,
    status ENUM('draft','published') NOT NULL DEFAULT 'draft',
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_events_admin FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_events_public (status, event_date),
    INDEX idx_events_category (category)
) ENGINE=InnoDB;

CREATE TABLE registrations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    motivation VARCHAR(1000) NULL,
    status ENUM('menunggu','diterima','ditolak','hadir') NOT NULL DEFAULT 'menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_registrations_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_email (event_id, email),
    INDEX idx_registration_status (status),
    INDEX idx_registration_created (created_at)
) ENGINE=InnoDB;

-- Akun demo: ubah kata sandi setelah instalasi.
-- Email: admin@aksibersama.test | Kata sandi: AksiAdmin2026!
INSERT INTO admins (name, email, password_hash) VALUES
('Administrator AksiBersama', 'admin@aksibersama.test', '$2y$12$ExccSD.i4nMS95aWzqv1VO7FYyclDombD1TR9VjLFAZ783fEARKvG');

-- Data demo menggunakan tanggal relatif agar dapat langsung dicoba.
INSERT INTO events (title, category, event_date, location, capacity, description, requirements, status, created_by) VALUES
('Satu Hari untuk Bumi', 'lingkungan', DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'Taman Kota Jepara', 40, 'Mari bergabung dalam kegiatan bersih-bersih taman dan edukasi pemilahan sampah. Bersama relawan lokal, kita menjaga ruang publik tetap nyaman sekaligus mengajak pengunjung lebih peduli terhadap lingkungan.', 'Gunakan pakaian nyaman, bawa botol minum pribadi, dan sarung tangan bila tersedia.', 'published', 1),
('Teman Belajar Akhir Pekan', 'pendidikan', DATE_ADD(CURDATE(), INTERVAL 21 DAY), 'Balai Warga Mlonggo', 20, 'Luangkan waktu untuk menemani anak-anak belajar membaca, berhitung, dan mengembangkan kreativitas. Kegiatan ini terbuka bagi siapa saja yang sabar, bersemangat, dan ingin berbagi pengetahuan.', 'Siapkan alat tulis dan ide permainan edukatif sederhana. Pengalaman mengajar tidak wajib.', 'published', 1),
('Paket Hangat untuk Sesama', 'sosial', DATE_ADD(CURDATE(), INTERVAL 28 DAY), 'Rumah Komunitas Bangsri', 30, 'Bantu menyiapkan dan mengemas paket kebutuhan harian untuk keluarga yang membutuhkan. Relawan akan bekerja dalam kelompok untuk memilah barang, menyiapkan paket, dan mendata distribusi bantuan.', 'Datang tepat waktu dan ikuti arahan koordinator kegiatan.', 'published', 1);
