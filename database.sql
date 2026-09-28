-- ==========================================================
-- SISTEM TRACKING TRAINING, ABSENSI & PEMINJAMAN OPERATOR
-- PT PARKLAND WORLD INDONESIA (CodeIgniter 4)
-- Database Name: parkland_tc_db
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `parkland_tc_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `parkland_tc_db`;

-- --------------------------------------------------------
-- 1. Table: users (Autentikasi & Role Matrix)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `role` ENUM('superadmin', 'trainer', 'guest') NOT NULL DEFAULT 'guest',
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping Data Users (Password default: admin123 / trainer123 / guest123)
INSERT INTO `users` (`id`, `username`, `password`, `name`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$oXF7kM9oK4kHQ8d3XmOcf.wHh1E6NqvUeM8jB5hYt9hYl7s1hO9g.', 'Admin Training Center (Lean & Head Trainer)', 'superadmin', NOW(), NOW()),
(2, 'trainer', '$2y$10$tZ2c6U2YV7GjVvB7sP0NTeoO6G2T2M2p9kH8sY7l6h5g4f3d2s1a.', 'Trainer Parkland', 'trainer', NOW(), NOW()),
(3, 'guest', '$2y$10$wK1k6Y2TV7GjVvB7sP0NTeoO6G2T2M2p9kH8sY7l6h5g4f3d2s1a.', 'Guest / Viewer', 'guest', NOW(), NOW());

-- --------------------------------------------------------
-- 2. Table: trainees (Data Karyawan / Peserta Pelatihan)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `trainees`;
CREATE TABLE `trainees` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rfid_tag` VARCHAR(50) NULL,
  `nik` VARCHAR(30) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `gender` VARCHAR(20) NOT NULL DEFAULT 'Laki-laki',
  `department` VARCHAR(100) NOT NULL DEFAULT 'Production',
  `track` VARCHAR(50) NOT NULL DEFAULT 'Belum Ditentukan', -- Sewing, Cutting, Assembly, Inhouse, Belum Ditentukan
  `stage` VARCHAR(50) NOT NULL DEFAULT 'Induction', -- Induction, Motoric Test, Hard Skill, Upskill, OJT, Lulus, Tidak Lolos, Resign
  `cycle_time_motoric` DECIMAL(6,2) NULL,
  `motoric_notes` TEXT NULL,
  `start_date` DATE NULL,
  `status` VARCHAR(30) NOT NULL DEFAULT 'Aktif', -- Aktif, Lulus, Tidak Lolos, Resign
  `notes` TEXT NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_nik` (`nik`),
  KEY `idx_rfid` (`rfid_tag`),
  KEY `idx_stage` (`stage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping Data Trainees (5 Data Awal Sesuai Gambar Dashboard)
INSERT INTO `trainees` (`id`, `rfid_tag`, `nik`, `name`, `gender`, `department`, `track`, `stage`, `cycle_time_motoric`, `motoric_notes`, `start_date`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'RFID-00184920', 'PWI-2026-001', 'Ahmad Fauzi', 'Laki-laki', 'Production - Sewing', 'Belum Ditentukan', 'Induction', NULL, 'Baru masuk hari pertama, menjalani induksi teori & pengenalan K3.', CURDATE(), 'Aktif', 'Peserta baru batch September 2026', NOW(), NOW()),
(2, 'RFID-00273819', 'PWI-2026-002', 'Budi Santoso', 'Laki-laki', 'Production - Cutting', 'Cutting', 'OJT', 34.50, 'Lulus motoric test 34.5 detik. Memilih track Cutting.', DATE_SUB(CURDATE(), INTERVAL 18 DAY), 'Aktif', 'Sedang menjalani OJT minggu ke-2 di Line Cutting A.', NOW(), NOW()),
(3, 'RFID-00391823', 'PWI-2026-003', 'Citra Lestari', 'Perempuan', 'Production - Sewing', 'Sewing', 'Lulus', 31.20, 'Hasil motoric sangat baik, lulus Sewing dan siap penempatan penuh.', DATE_SUB(CURDATE(), INTERVAL 35 DAY), 'Lulus', 'Lulus dengan predikat memuaskan, ditempatkan di Sewing Line 4.', NOW(), NOW()),
(4, 'RFID-00481729', 'PWI-2026-004', 'Dewi Anggraini', 'Perempuan', 'Production - Assembly', 'Assembly', 'Lulus', 38.00, 'Lulus motoric test 38.0 detik. Memilih track Assembly.', DATE_SUB(CURDATE(), INTERVAL 40 DAY), 'Lulus', 'Lulus OJT dan siap penempatan.', NOW(), NOW()),
(5, 'RFID-00561928', 'PWI-2026-005', 'Eko Prasetyo', 'Laki-laki', 'Production - Inhouse', 'Inhouse', 'Lulus', 42.10, 'Cycle time motoric 42.1 detik -> Mengambil track Inhouse.', DATE_SUB(CURDATE(), INTERVAL 42 DAY), 'Lulus', 'Lulus training inhouse dengan performa stabil.', NOW(), NOW());

-- --------------------------------------------------------
-- 3. Table: daily_evaluations (Evaluasi Harian Cycle Time & Skor)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `daily_evaluations`;
CREATE TABLE `daily_evaluations` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `trainee_id` INT(11) UNSIGNED NOT NULL,
  `evaluation_date` DATE NOT NULL,
  `stage` VARCHAR(50) NOT NULL,
  `day_number` INT(4) NOT NULL DEFAULT 1,
  `cycle_time` DECIMAL(6,2) NULL,
  `target_cycle_time` DECIMAL(6,2) NULL,
  `quality_score` DECIMAL(5,2) NULL,
  `work_method` VARCHAR(50) NULL,
  `notes` TEXT NULL,
  `evaluator_id` INT(11) UNSIGNED NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_eval_trainee` (`trainee_id`),
  CONSTRAINT `fk_eval_trainee` FOREIGN KEY (`trainee_id`) REFERENCES `trainees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 4. Table: attendances (Rekam Absensi & Tap RFID Harian)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `attendances`;
CREATE TABLE `attendances` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `trainee_id` INT(11) UNSIGNED NOT NULL,
  `attendance_date` DATE NOT NULL,
  `time_in` TIME NULL,
  `time_out` TIME NULL,
  `status` ENUM('Hadir', 'Izin', 'Sakit', 'Alpha') NOT NULL DEFAULT 'Hadir',
  `rfid_tapped_at` DATETIME NULL,
  `notes` VARCHAR(255) NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_att_trainee` (`trainee_id`),
  KEY `idx_att_date` (`attendance_date`),
  CONSTRAINT `fk_att_trainee` FOREIGN KEY (`trainee_id`) REFERENCES `trainees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping Data Absensi Hari Ini
INSERT INTO `attendances` (`id`, `trainee_id`, `attendance_date`, `time_in`, `time_out`, `status`, `rfid_tapped_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, CURDATE(), '07:15:00', NULL, 'Hadir', NOW(), 'Tap RFID Gate 1 Training Center', NOW(), NOW()),
(2, 2, CURDATE(), '07:18:00', NULL, 'Hadir', NOW(), 'Tap RFID OJT Line Cutting', NOW(), NOW());

-- --------------------------------------------------------
-- 5. Table: operator_loans (Peminjaman Operator Trainee)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `operator_loans`;
CREATE TABLE `operator_loans` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `trainee_id` INT(11) UNSIGNED NOT NULL,
  `borrowing_department` VARCHAR(100) NOT NULL,
  `line_name` VARCHAR(100) NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NULL,
  `duration_days` INT(4) NOT NULL DEFAULT 1,
  `status` ENUM('Dipinjam', 'Selesai', 'Dibatalkan') NOT NULL DEFAULT 'Dipinjam',
  `purpose` TEXT NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_loan_trainee` (`trainee_id`),
  CONSTRAINT `fk_loan_trainee` FOREIGN KEY (`trainee_id`) REFERENCES `trainees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping Data Peminjaman Operator
INSERT INTO `operator_loans` (`id`, `trainee_id`, `borrowing_department`, `line_name`, `start_date`, `end_date`, `duration_days`, `status`, `purpose`, `created_at`, `updated_at`) VALUES
(1, 2, 'Cutting Dept', 'Line Cutting 02', DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 5, 'Dipinjam', 'Bantuan lonjakan output produksi sepatu model running.', NOW(), NOW());

-- --------------------------------------------------------
-- 6. Table: training_materials (Materi & SOP Training)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `training_materials`;
CREATE TABLE `training_materials` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `stage` VARCHAR(50) NOT NULL DEFAULT 'All Stages',
  `track` VARCHAR(50) NOT NULL DEFAULT 'All Tracks',
  `category` ENUM('SOP', 'Materi Teori', 'Video', 'Panduan Praktik') NOT NULL DEFAULT 'SOP',
  `file_url` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `uploaded_by` VARCHAR(100) NOT NULL DEFAULT 'Admin Training Center',
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping Data Materi Training
INSERT INTO `training_materials` (`id`, `title`, `stage`, `track`, `category`, `file_url`, `description`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(1, 'Modul Induksi K3 & Regulasi Pabrik PT Parkland World Indonesia', 'Induction', 'All Tracks', 'SOP', 'https://example.com/materials/sop-induksi-k3.pdf', 'Standar operasional prosedur pengenalan pabrik, keselamatan kerja (K3), budaya 5S, dan etika kerja.', 'Lean & Head Trainer', NOW(), NOW()),
(2, 'Panduan Pengujian Motorik & Ketelitian Tangan (Motoric Test)', 'Motoric Test', 'All Tracks', 'Panduan Praktik', 'https://example.com/materials/guide-motoric-test.pdf', 'Tata cara tes motorik, stopwatch cycle time benchmark (< 40s untuk Sewing).', 'Trainer Parkland', NOW(), NOW()),
(3, 'Modul Hard Skill: Teknik Jahit Upper Sepatu (Sewing)', 'Hard Skill', 'Sewing', 'Materi Teori', 'https://example.com/materials/hard-skill-sewing.pdf', 'Pelatihan teknik jahit dasar hingga kurva jahitan rumit selama 3 minggu.', 'Trainer Parkland', NOW(), NOW()),
(4, 'Form & Panduan Penilaian On Job Training (OJT 5 Pasang)', 'OJT', 'All Tracks', 'SOP', 'https://example.com/materials/sop-ojt-assessment.pdf', 'Matriks penilaian 5 pasang hasil proses operator baru mencakup Kualitas, Cycle Time, dan Sikap Kerja.', 'Lean & Head Trainer', NOW(), NOW());
