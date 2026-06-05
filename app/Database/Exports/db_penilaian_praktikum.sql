-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2026 at 02:40 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_penilaian_praktikum`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` int(10) UNSIGNED NOT NULL,
  `year_code` varchar(20) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` char(10) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `module` varchar(100) DEFAULT NULL,
  `target_type` varchar(100) DEFAULT NULL,
  `target_id` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `user_nip` char(10) NOT NULL,
  `unit_name` varchar(100) NOT NULL DEFAULT 'SISFO',
  `position` varchar(100) DEFAULT NULL,
  `status` enum('aktif','tidak_aktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`user_nip`, `unit_name`, `position`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
('0000000001', 'SISFO', 'Petugas SISFO', 'aktif', '2026-06-04 23:57:33', '2026-06-04 23:57:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `assessment_components`
--

CREATE TABLE `assessment_components` (
  `id` int(10) UNSIGNED NOT NULL,
  `template_id` int(10) UNSIGNED NOT NULL,
  `component_code` varchar(50) NOT NULL,
  `component_type` enum('kehadiran','tugas','modul','laporan','kuis','responsi','uts','uas','proyek','presentasi','sikap','custom') NOT NULL,
  `component_name` varchar(150) NOT NULL,
  `weight` decimal(6,2) NOT NULL DEFAULT 0.00,
  `max_score` decimal(6,2) NOT NULL DEFAULT 100.00,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `allow_subcomponents` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_subcomponents`
--

CREATE TABLE `assessment_subcomponents` (
  `id` int(10) UNSIGNED NOT NULL,
  `component_id` int(10) UNSIGNED NOT NULL,
  `subcomponent_code` varchar(50) NOT NULL,
  `subcomponent_name` varchar(150) NOT NULL,
  `weight` decimal(6,2) NOT NULL DEFAULT 0.00,
  `max_score` decimal(6,2) NOT NULL DEFAULT 100.00,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_templates`
--

CREATE TABLE `assessment_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `template_code` varchar(50) NOT NULL,
  `template_name` varchar(150) NOT NULL,
  `study_program_id` int(10) UNSIGNED DEFAULT NULL,
  `course_id` int(10) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assistants`
--

CREATE TABLE `assistants` (
  `user_nim` char(10) NOT NULL,
  `study_program_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('aktif','tidak_aktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assistants`
--

INSERT INTO `assistants` (`user_nim`, `study_program_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
('0000000004', 1, 'aktif', '2026-06-04 23:57:33', '2026-06-04 23:57:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attendance_records`
--

CREATE TABLE `attendance_records` (
  `id` int(10) UNSIGNED NOT NULL,
  `attendance_session_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `attendance_status_id` int(10) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `marked_by` char(10) DEFAULT NULL,
  `marked_at` datetime DEFAULT NULL,
  `previous_attendance_status_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_sessions`
--

CREATE TABLE `attendance_sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `practicum_class_id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `meeting_no` int(11) NOT NULL,
  `session_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `status` enum('draft','scheduled','open','closed','locked','cancelled') NOT NULL DEFAULT 'draft',
  `locked_at` datetime DEFAULT NULL,
  `created_by` char(10) DEFAULT NULL,
  `updated_by` char(10) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_statuses`
--

CREATE TABLE `attendance_statuses` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_statuses`
--

INSERT INTO `attendance_statuses` (`id`, `code`, `name`, `description`, `is_default`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'hadir', 'Hadir', 'Mahasiswa hadir tepat waktu', 1, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(2, 'izin', 'Izin', 'Mahasiswa izin resmi', 0, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(3, 'sakit', 'Sakit', 'Mahasiswa sakit', 0, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(4, 'alfa', 'Alfa', 'Mahasiswa tidak hadir tanpa keterangan', 0, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(5, 'susulan', 'Susulan', 'Absensi pengganti', 0, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22');

-- --------------------------------------------------------

--
-- Table structure for table `class_assistants`
--

CREATE TABLE `class_assistants` (
  `id` int(10) UNSIGNED NOT NULL,
  `practicum_class_id` int(10) UNSIGNED NOT NULL,
  `assistant_id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT 0,
  `duty_note` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_lecturers`
--

CREATE TABLE `class_lecturers` (
  `id` int(10) UNSIGNED NOT NULL,
  `practicum_class_id` int(10) UNSIGNED NOT NULL,
  `lecturer_id` int(10) UNSIGNED NOT NULL,
  `role_type` enum('pengampu','koordinator','reviewer') NOT NULL DEFAULT 'pengampu',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_students`
--

CREATE TABLE `class_students` (
  `id` int(10) UNSIGNED NOT NULL,
  `practicum_class_id` int(10) UNSIGNED NOT NULL,
  `student_nim` char(10) NOT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `enrollment_status` enum('aktif','drop','lulus','remedial') NOT NULL DEFAULT 'aktif',
  `enrolled_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coordinators`
--

CREATE TABLE `coordinators` (
  `user_nid` char(10) NOT NULL,
  `unit_name` varchar(100) NOT NULL DEFAULT 'SISFO',
  `position` varchar(100) DEFAULT NULL,
  `status` enum('aktif','tidak_aktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coordinators`
--

INSERT INTO `coordinators` (`user_nid`, `unit_name`, `position`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
('0000000002', 'SISFO', 'Koordinator Praktikum', 'aktif', '2026-06-04 23:57:33', '2026-06-04 23:57:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(10) UNSIGNED NOT NULL,
  `study_program_id` int(10) UNSIGNED NOT NULL,
  `course_code` varchar(50) NOT NULL,
  `course_name` varchar(150) NOT NULL,
  `credits` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `is_practicum` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('aktif','tidak_aktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `export_logs`
--

CREATE TABLE `export_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` char(10) NOT NULL,
  `export_type` varchar(50) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `format` varchar(20) DEFAULT NULL,
  `total_rows` int(11) NOT NULL DEFAULT 0,
  `status` enum('success','failed') NOT NULL DEFAULT 'success',
  `filters_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`filters_json`)),
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `final_scores`
--

CREATE TABLE `final_scores` (
  `id` int(10) UNSIGNED NOT NULL,
  `practicum_class_id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `final_score` decimal(6,2) DEFAULT NULL,
  `grade_letter` varchar(10) DEFAULT NULL,
  `grade_point` decimal(4,2) DEFAULT NULL,
  `status` enum('draft','submitted','reviewed','validated','locked','revision_requested','revised') NOT NULL DEFAULT 'draft',
  `validation_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `validated_by` char(10) DEFAULT NULL,
  `validated_at` datetime DEFAULT NULL,
  `locked_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grade_scales`
--

CREATE TABLE `grade_scales` (
  `id` int(10) UNSIGNED NOT NULL,
  `scale_code` varchar(50) NOT NULL,
  `grade_letter` varchar(10) NOT NULL,
  `min_score` decimal(6,2) NOT NULL,
  `max_score` decimal(6,2) NOT NULL,
  `grade_point` decimal(4,2) NOT NULL DEFAULT 0.00,
  `predicate` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_passing` tinyint(1) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade_scales`
--

INSERT INTO `grade_scales` (`id`, `scale_code`, `grade_letter`, `min_score`, `max_score`, `grade_point`, `predicate`, `description`, `is_passing`, `is_default`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'A', 'A', 85.00, 100.00, 4.00, 'Sangat Baik', 'Lulus sangat baik', 1, 1, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(2, 'B', 'B', 70.00, 84.99, 3.00, 'Baik', 'Lulus', 1, 0, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(3, 'C', 'C', 60.00, 69.99, 2.00, 'Cukup', 'Lulus minimal', 1, 0, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(4, 'D', 'D', 50.00, 59.99, 1.00, 'Kurang', 'Tidak lulus', 0, 0, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(5, 'E', 'E', 0.00, 49.99, 0.00, 'Sangat Kurang', 'Tidak lulus', 0, 0, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22');

-- --------------------------------------------------------

--
-- Table structure for table `huruf_mutu`
--

CREATE TABLE `huruf_mutu` (
  `id` int(11) UNSIGNED NOT NULL,
  `huruf` varchar(2) NOT NULL,
  `batas_bawah` decimal(5,2) NOT NULL,
  `batas_atas` decimal(5,2) NOT NULL,
  `angka_mutu` decimal(3,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `import_logs`
--

CREATE TABLE `import_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` char(10) NOT NULL,
  `import_type` varchar(50) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_hash` varchar(128) DEFAULT NULL,
  `total_rows` int(11) NOT NULL DEFAULT 0,
  `success_rows` int(11) NOT NULL DEFAULT 0,
  `failed_rows` int(11) NOT NULL DEFAULT 0,
  `status` enum('pending','processing','completed','failed','partial') NOT NULL DEFAULT 'pending',
  `error_summary` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama_kelas` varchar(50) NOT NULL,
  `mata_kuliah_id` int(11) UNSIGNED NOT NULL,
  `dosen_nid` char(10) DEFAULT NULL,
  `tahun_akademik_id` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laboratories`
--

CREATE TABLE `laboratories` (
  `id` int(10) UNSIGNED NOT NULL,
  `room_code` varchar(50) NOT NULL,
  `room_name` varchar(150) NOT NULL,
  `location` varchar(150) DEFAULT NULL,
  `capacity` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('aktif','tidak_aktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
  `user_nid` char(10) NOT NULL,
  `study_program_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('aktif','tidak_aktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`user_nid`, `study_program_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
('0000000003', 1, 'aktif', '2026-06-04 23:57:33', '2026-06-04 23:57:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mata_kuliah`
--

CREATE TABLE `mata_kuliah` (
  `id` int(11) UNSIGNED NOT NULL,
  `kode_mk` varchar(20) NOT NULL,
  `nama_mk` varchar(100) NOT NULL,
  `sks` int(2) NOT NULL,
  `prodi_id` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-05-19-052111', 'App\\Database\\Migrations\\CreateTahunAkademikTable', 'default', 'App', 1780617324, 1),
(2, '2026-05-19-052230', 'App\\Database\\Migrations\\CreateProdiTable', 'default', 'App', 1780617324, 1),
(3, '2026-05-19-052407', 'App\\Database\\Migrations\\CreateMataKuliahTable', 'default', 'App', 1780617324, 1),
(4, '2026-05-19-052531', 'App\\Database\\Migrations\\CreateKelasTable', 'default', 'App', 1780617324, 1),
(5, '2026-05-19-052727', 'App\\Database\\Migrations\\CreateHurufMutuTable', 'default', 'App', 1780617324, 1),
(6, '2026-05-19-052909', 'App\\Database\\Migrations\\CreateTemplateNilaiTable', 'default', 'App', 1780617324, 1),
(7, '2026-05-21-000001', 'App\\Database\\Migrations\\CreateAuthCoreTables', 'default', 'App', 1780617325, 1),
(8, '2026-05-21-000002', 'App\\Database\\Migrations\\CreateAcademicMasterTables', 'default', 'App', 1780617325, 1),
(9, '2026-05-21-000004', 'App\\Database\\Migrations\\CreateAssessmentTables', 'default', 'App', 1780617326, 1),
(10, '2026-05-21-000005', 'App\\Database\\Migrations\\CreatePracticumStructureTables', 'default', 'App', 1780617327, 1),
(11, '2026-05-21-000006', 'App\\Database\\Migrations\\CreateAttendanceTables', 'default', 'App', 1780617327, 1),
(12, '2026-05-21-000007', 'App\\Database\\Migrations\\CreateScoreTables', 'default', 'App', 1780617328, 1),
(13, '2026-05-21-000008', 'App\\Database\\Migrations\\CreateValidationTables', 'default', 'App', 1780617329, 1),
(14, '2026-05-21-000009', 'App\\Database\\Migrations\\CreateRemedialTables', 'default', 'App', 1780617329, 1),
(15, '2026-05-21-000010', 'App\\Database\\Migrations\\CreateLogAndNotificationTables', 'default', 'App', 1780617331, 1),
(16, '2026-06-04_000014', 'App\\Database\\Migrations\\RestructureProfileTablesRelationsNoFK', 'default', 'App', 1780617332, 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` char(10) NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','danger') NOT NULL DEFAULT 'info',
  `reference_type` varchar(100) DEFAULT NULL,
  `reference_id` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `passing_rules`
--

CREATE TABLE `passing_rules` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED DEFAULT NULL,
  `template_id` int(10) UNSIGNED DEFAULT NULL,
  `min_final_score` decimal(6,2) NOT NULL DEFAULT 0.00,
  `min_attendance_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `min_component_score` decimal(6,2) NOT NULL DEFAULT 0.00,
  `allow_remedial` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(100) NOT NULL,
  `module` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `code`, `module`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Lihat dashboard admin', 'dashboard.admin.view', 'dashboard', 'Akses dashboard admin', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(2, 'Lihat dashboard koordinator', 'dashboard.coordinator.view', 'dashboard', 'Akses dashboard koordinator', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(3, 'Lihat dashboard dosen', 'dashboard.lecturer.view', 'dashboard', 'Akses dashboard dosen', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(4, 'Lihat dashboard asisten', 'dashboard.assistant.view', 'dashboard', 'Akses dashboard asisten', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(5, 'Lihat dashboard mahasiswa', 'dashboard.student.view', 'dashboard', 'Akses dashboard mahasiswa', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(6, 'Kelola pengguna', 'users.manage', 'auth', 'Mengelola akun dan profil pengguna', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(7, 'Kelola master akademik', 'academic.master.manage', 'academic', 'Mengelola program studi, tahun akademik, semester, dan kursus', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(8, 'Kelola kelas praktikum', 'practicum.class.manage', 'practicum', 'Mengelola kelas dan kelompok praktikum', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(9, 'Kelola kehadiran', 'attendance.manage', 'attendance', 'Input dan koreksi absensi', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(10, 'Input nilai', 'score.input', 'score', 'Input nilai komponen', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(11, 'Validasi nilai', 'score.validate', 'score', 'Validasi nilai akhir dan komponen', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(12, 'Kunci nilai', 'score.lock', 'score', 'Mengunci nilai final', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(13, 'Kelola revisi nilai', 'score.revision.manage', 'score', 'Request dan approval revisi nilai', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(14, 'Kelola remedial', 'remedial.manage', 'remedial', 'Mengelola periode dan hasil remedial', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(15, 'Import data', 'import.manage', 'import', 'Mengimpor data ke sistem', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(16, 'Export data', 'export.manage', 'export', 'Mengekspor data dari sistem', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(17, 'Generate laporan', 'report.generate', 'report', 'Membuat laporan dan rekap', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(18, 'Lihat audit log', 'audit.view', 'audit', 'Melihat log aktivitas dan audit', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(19, 'Kelola notifikasi', 'notification.manage', 'notification', 'Mengirim dan mengelola notifikasi', '2026-06-05 00:07:22', '2026-06-05 00:07:22');

-- --------------------------------------------------------

--
-- Table structure for table `practicum_classes`
--

CREATE TABLE `practicum_classes` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `academic_year_id` int(10) UNSIGNED NOT NULL,
  `semester_id` int(10) UNSIGNED NOT NULL,
  `laboratory_id` int(10) UNSIGNED DEFAULT NULL,
  `template_id` int(10) UNSIGNED DEFAULT NULL,
  `class_code` varchar(50) NOT NULL,
  `class_name` varchar(150) NOT NULL,
  `status` enum('draft','aktif','selesai','terkunci','diarsipkan') NOT NULL DEFAULT 'draft',
  `deadline_at` datetime DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `practicum_groups`
--

CREATE TABLE `practicum_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `practicum_class_id` int(10) UNSIGNED NOT NULL,
  `group_code` varchar(50) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `capacity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('aktif','tidak_aktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prodi`
--

CREATE TABLE `prodi` (
  `id` int(11) UNSIGNED NOT NULL,
  `kode_prodi` varchar(10) NOT NULL,
  `nama_prodi` varchar(100) NOT NULL,
  `jenjang` varchar(10) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remedial_components`
--

CREATE TABLE `remedial_components` (
  `id` int(10) UNSIGNED NOT NULL,
  `remedial_period_id` int(10) UNSIGNED NOT NULL,
  `component_id` int(10) UNSIGNED NOT NULL,
  `max_score_after_remedial` decimal(6,2) NOT NULL DEFAULT 100.00,
  `weight_adjustment` decimal(6,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remedial_logs`
--

CREATE TABLE `remedial_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `remedial_period_id` int(10) UNSIGNED DEFAULT NULL,
  `remedial_participant_id` int(10) UNSIGNED DEFAULT NULL,
  `event_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` char(10) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remedial_participants`
--

CREATE TABLE `remedial_participants` (
  `id` int(10) UNSIGNED NOT NULL,
  `remedial_period_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `practicum_class_id` int(10) UNSIGNED NOT NULL,
  `final_score_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('eligible','terdaftar','dijadwalkan','sudah_dinilai','validated','tidak_mengikuti','dibatalkan') NOT NULL DEFAULT 'eligible',
  `reason` text DEFAULT NULL,
  `before_score` decimal(6,2) DEFAULT NULL,
  `after_score` decimal(6,2) DEFAULT NULL,
  `max_after_score` decimal(6,2) DEFAULT NULL,
  `validated_by` char(10) DEFAULT NULL,
  `validated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remedial_periods`
--

CREATE TABLE `remedial_periods` (
  `id` int(10) UNSIGNED NOT NULL,
  `remedial_code` varchar(50) NOT NULL,
  `title` varchar(150) NOT NULL,
  `semester_id` int(10) UNSIGNED DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `registration_deadline` date DEFAULT NULL,
  `status` enum('draft','active','closed','archived') NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remedial_results`
--

CREATE TABLE `remedial_results` (
  `id` int(10) UNSIGNED NOT NULL,
  `remedial_participant_id` int(10) UNSIGNED NOT NULL,
  `final_score_before` decimal(6,2) DEFAULT NULL,
  `final_score_after` decimal(6,2) DEFAULT NULL,
  `grade_letter_before` varchar(10) DEFAULT NULL,
  `grade_letter_after` varchar(10) DEFAULT NULL,
  `is_passed` tinyint(1) NOT NULL DEFAULT 0,
  `validation_status` enum('pending','validated','rejected') NOT NULL DEFAULT 'pending',
  `validated_by` char(10) DEFAULT NULL,
  `validated_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remedial_rules`
--

CREATE TABLE `remedial_rules` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED DEFAULT NULL,
  `template_id` int(10) UNSIGNED DEFAULT NULL,
  `min_score_for_remedial` decimal(6,2) NOT NULL DEFAULT 0.00,
  `max_remedial_score` decimal(6,2) NOT NULL DEFAULT 100.00,
  `require_approval` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remedial_scores`
--

CREATE TABLE `remedial_scores` (
  `id` int(10) UNSIGNED NOT NULL,
  `remedial_participant_id` int(10) UNSIGNED NOT NULL,
  `remedial_component_id` int(10) UNSIGNED DEFAULT NULL,
  `score_before` decimal(6,2) DEFAULT NULL,
  `score_after` decimal(6,2) DEFAULT NULL,
  `raw_score` decimal(6,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `entered_by` char(10) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_logs`
--

CREATE TABLE `report_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` char(10) DEFAULT NULL,
  `report_type` varchar(50) NOT NULL,
  `report_name` varchar(150) NOT NULL,
  `parameters_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`parameters_json`)),
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','success','failed') NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `code`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Admin SISFO', 'admin_sisfo', 'Petugas SISFO kampus', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(2, 'Koordinator Praktikum', 'koordinator_praktikum', 'Kepala laboratorium / koordinator praktikum', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(3, 'Dosen', 'dosen', 'Dosen pengampu praktikum', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(4, 'Asisten Praktikum', 'asisten_praktikum', 'Asisten praktikum dan pendamping kelas', '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(5, 'Mahasiswa', 'mahasiswa', 'Peserta praktikum', '2026-06-05 00:07:22', '2026-06-05 00:07:22');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `created_at`) VALUES
(1, 1, 1, '2026-06-05 00:07:22'),
(2, 1, 6, '2026-06-05 00:07:22'),
(3, 1, 7, '2026-06-05 00:07:22'),
(4, 1, 8, '2026-06-05 00:07:22'),
(5, 1, 9, '2026-06-05 00:07:22'),
(6, 1, 10, '2026-06-05 00:07:22'),
(7, 1, 11, '2026-06-05 00:07:22'),
(8, 1, 12, '2026-06-05 00:07:22'),
(9, 1, 13, '2026-06-05 00:07:22'),
(10, 1, 14, '2026-06-05 00:07:22'),
(11, 1, 15, '2026-06-05 00:07:22'),
(12, 1, 16, '2026-06-05 00:07:22'),
(13, 1, 17, '2026-06-05 00:07:22'),
(14, 1, 18, '2026-06-05 00:07:22'),
(15, 1, 19, '2026-06-05 00:07:22'),
(16, 2, 2, '2026-06-05 00:07:22'),
(17, 2, 8, '2026-06-05 00:07:22'),
(18, 2, 9, '2026-06-05 00:07:22'),
(19, 2, 11, '2026-06-05 00:07:22'),
(20, 2, 12, '2026-06-05 00:07:22'),
(21, 2, 13, '2026-06-05 00:07:22'),
(22, 2, 14, '2026-06-05 00:07:22'),
(23, 2, 17, '2026-06-05 00:07:22'),
(24, 2, 18, '2026-06-05 00:07:22'),
(25, 3, 3, '2026-06-05 00:07:22'),
(26, 3, 10, '2026-06-05 00:07:22'),
(27, 3, 11, '2026-06-05 00:07:22'),
(28, 3, 13, '2026-06-05 00:07:22'),
(29, 3, 17, '2026-06-05 00:07:22'),
(30, 4, 4, '2026-06-05 00:07:22'),
(31, 4, 9, '2026-06-05 00:07:22'),
(32, 4, 10, '2026-06-05 00:07:22'),
(33, 4, 17, '2026-06-05 00:07:22'),
(34, 5, 5, '2026-06-05 00:07:22'),
(35, 5, 19, '2026-06-05 00:07:22');

-- --------------------------------------------------------

--
-- Table structure for table `score_change_logs`
--

CREATE TABLE `score_change_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `score_entry_id` int(10) UNSIGNED DEFAULT NULL,
  `final_score_id` int(10) UNSIGNED DEFAULT NULL,
  `changed_by` char(10) DEFAULT NULL,
  `change_type` enum('create','update','delete','input','change','validate','lock','request_revision','approve_revision') NOT NULL,
  `old_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_value`)),
  `new_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_value`)),
  `reason` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `score_details`
--

CREATE TABLE `score_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `score_entry_id` int(10) UNSIGNED NOT NULL,
  `detail_key` varchar(100) NOT NULL,
  `detail_label` varchar(150) DEFAULT NULL,
  `detail_value` decimal(6,2) DEFAULT NULL,
  `weight` decimal(6,2) DEFAULT NULL,
  `max_score` decimal(6,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `score_entries`
--

CREATE TABLE `score_entries` (
  `id` int(10) UNSIGNED NOT NULL,
  `practicum_class_id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `component_id` int(10) UNSIGNED NOT NULL,
  `subcomponent_id` int(10) UNSIGNED DEFAULT NULL,
  `score_value` decimal(6,2) DEFAULT NULL,
  `max_score` decimal(6,2) DEFAULT NULL,
  `status_id` int(10) UNSIGNED DEFAULT NULL,
  `submitted_by` char(10) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `validated_by` char(10) DEFAULT NULL,
  `validated_at` datetime DEFAULT NULL,
  `locked_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `score_notes`
--

CREATE TABLE `score_notes` (
  `id` int(10) UNSIGNED NOT NULL,
  `score_entry_id` int(10) UNSIGNED DEFAULT NULL,
  `final_score_id` int(10) UNSIGNED DEFAULT NULL,
  `note_type` enum('teacher','assistant','system','student') NOT NULL DEFAULT 'system',
  `note_text` text NOT NULL,
  `created_by` char(10) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `score_revision_requests`
--

CREATE TABLE `score_revision_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `final_score_id` int(10) UNSIGNED NOT NULL,
  `requested_by` char(10) NOT NULL,
  `requested_to` char(10) DEFAULT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected','implemented','cancelled') NOT NULL DEFAULT 'pending',
  `approved_by` char(10) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `score_statuses`
--

CREATE TABLE `score_statuses` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(40) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_terminal` tinyint(1) NOT NULL DEFAULT 0,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `score_statuses`
--

INSERT INTO `score_statuses` (`id`, `code`, `name`, `description`, `is_terminal`, `is_locked`, `is_default`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'draft', 'Draft', 'Nilai belum final', 0, 0, 1, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(2, 'submitted', 'Submitted', 'Nilai sudah dikirim', 0, 0, 0, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(3, 'reviewed', 'Reviewed', 'Nilai telah ditinjau', 0, 0, 0, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(4, 'validated', 'Validated', 'Nilai telah divalidasi', 1, 0, 0, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(5, 'locked', 'Locked', 'Nilai terkunci', 1, 1, 0, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(6, 'revision_requested', 'Revision Requested', 'Ada permintaan revisi nilai', 0, 0, 0, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22'),
(7, 'revised', 'Revised', 'Nilai sudah direvisi', 0, 0, 0, 1, '2026-06-05 00:07:22', '2026-06-05 00:07:22');

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `id` int(10) UNSIGNED NOT NULL,
  `academic_year_id` int(10) UNSIGNED NOT NULL,
  `semester_code` varchar(20) NOT NULL,
  `semester_name` varchar(100) NOT NULL,
  `semester_number` tinyint(1) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `user_nim` char(10) NOT NULL,
  `study_program_id` int(10) UNSIGNED DEFAULT NULL,
  `class_year` smallint(5) UNSIGNED DEFAULT NULL,
  `status` enum('aktif','mengulang','mengundurkan_diri','tidak_aktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`user_nim`, `study_program_id`, `class_year`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
('0000000005', 1, 2024, 'aktif', '2026-06-04 23:57:33', '2026-06-04 23:57:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `study_programs`
--

CREATE TABLE `study_programs` (
  `id` int(10) UNSIGNED NOT NULL,
  `program_code` varchar(50) NOT NULL,
  `program_name` varchar(150) NOT NULL,
  `faculty_name` varchar(150) DEFAULT NULL,
  `degree_level` varchar(20) DEFAULT NULL,
  `status` enum('aktif','tidak_aktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tahun_akademik`
--

CREATE TABLE `tahun_akademik` (
  `id` int(11) UNSIGNED NOT NULL,
  `tahun` varchar(9) NOT NULL,
  `semester` enum('Ganjil','Genap') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `template_nilai`
--

CREATE TABLE `template_nilai` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama_template` varchar(100) NOT NULL,
  `bobot_tugas` int(3) NOT NULL DEFAULT 0,
  `bobot_kuis` int(3) NOT NULL DEFAULT 0,
  `bobot_uts` int(3) NOT NULL DEFAULT 0,
  `bobot_uas` int(3) NOT NULL DEFAULT 0,
  `bobot_praktikum` int(3) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` char(10) NOT NULL,
  `login_identifier` char(10) NOT NULL,
  `identifier_type` enum('NIM','NID','NIP') NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login_identifier`, `identifier_type`, `password_hash`, `full_name`, `email`, `phone`, `is_active`, `last_login_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
('0000000001', '0000000001', 'NIP', '$2y$10$qsnLz0L5RlOUkXOOl0LNO..9we9Pq6GqWNCkvtm4PCNMPvEME2.im', 'Administrator SISFO', 'sisfo@example.edu', NULL, 1, '2026-06-05 00:34:19', '2026-06-04 23:56:26', '2026-06-05 00:34:19', NULL),
('0000000002', '0000000002', 'NIP', '$2y$10$jonbDko4SOdW3JPZt89RquqME0JnvRprsQjClngvxgVEvXdmUiUYS', 'Koordinator Praktikum', 'koordinator@example.edu', NULL, 1, NULL, '2026-06-04 23:57:33', '2026-06-04 23:57:33', NULL),
('0000000003', '0000000003', 'NIP', '$2y$10$10hKHW9anQA.teE/aPnGIOmV5EMEhzvoUdUJVQBqKrOriiyeldyZK', 'Dosen Pengampu', 'dosen@example.edu', NULL, 1, NULL, '2026-06-04 23:57:33', '2026-06-04 23:57:33', NULL),
('0000000004', '0000000004', 'NID', '$2y$10$dUVX9FirlnV4yNgrZt4xf.k85spdlF6xvgA2ellwvdTnt6wJeLLPq', 'Asisten Praktikum', 'asisten@example.edu', NULL, 1, NULL, '2026-06-04 23:57:33', '2026-06-04 23:57:33', NULL),
('0000000005', '0000000005', 'NIM', '$2y$10$RBagx.zt5zwP1.lFv0ZOoeZMRs5ET2H06CIvy3.RN2GyzEv.4cA3.', 'Mahasiswa Praktikum', 'mahasiswa@example.edu', NULL, 1, NULL, '2026-06-04 23:57:33', '2026-06-04 23:57:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` char(10) NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`) VALUES
(1, '0000000001', 1, '2026-06-05 00:07:22'),
(2, '0000000002', 2, '2026-06-04 23:57:33'),
(3, '0000000003', 3, '2026-06-04 23:57:33'),
(4, '0000000004', 4, '2026-06-04 23:57:33'),
(5, '0000000005', 5, '2026-06-04 23:57:33');

-- --------------------------------------------------------

--
-- Table structure for table `validation_logs`
--

CREATE TABLE `validation_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `practicum_class_id` int(10) UNSIGNED NOT NULL,
  `score_entry_id` int(10) UNSIGNED DEFAULT NULL,
  `final_score_id` int(10) UNSIGNED DEFAULT NULL,
  `validator_user_id` char(10) NOT NULL,
  `action` enum('validate','approve','reject','lock','unlock') NOT NULL,
  `result` enum('valid','invalid','locked','unlocked') NOT NULL DEFAULT 'valid',
  `notes` text DEFAULT NULL,
  `validated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `year_code` (`year_code`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `action` (`action`),
  ADD KEY `module` (`module`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`user_nip`);

--
-- Indexes for table `assessment_components`
--
ALTER TABLE `assessment_components`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_id_component_code` (`template_id`,`component_code`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `assessment_subcomponents`
--
ALTER TABLE `assessment_subcomponents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `component_id_subcomponent_code` (`component_id`,`subcomponent_code`),
  ADD KEY `component_id` (`component_id`);

--
-- Indexes for table `assessment_templates`
--
ALTER TABLE `assessment_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_code` (`template_code`),
  ADD KEY `study_program_id` (`study_program_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `assistants`
--
ALTER TABLE `assistants`
  ADD PRIMARY KEY (`user_nim`);

--
-- Indexes for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_session_id_student_id` (`attendance_session_id`,`student_id`),
  ADD KEY `attendance_session_id` (`attendance_session_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `attendance_status_id` (`attendance_status_id`);

--
-- Indexes for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `practicum_class_id_group_id_meeting_no` (`practicum_class_id`,`group_id`,`meeting_no`),
  ADD KEY `practicum_class_id` (`practicum_class_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `status` (`status`),
  ADD KEY `session_date` (`session_date`);

--
-- Indexes for table `attendance_statuses`
--
ALTER TABLE `attendance_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `class_assistants`
--
ALTER TABLE `class_assistants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `practicum_class_id_assistant_id_group_id` (`practicum_class_id`,`assistant_id`,`group_id`),
  ADD KEY `practicum_class_id` (`practicum_class_id`),
  ADD KEY `assistant_id` (`assistant_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `class_lecturers`
--
ALTER TABLE `class_lecturers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `practicum_class_id_lecturer_id_role_type` (`practicum_class_id`,`lecturer_id`,`role_type`),
  ADD KEY `practicum_class_id` (`practicum_class_id`),
  ADD KEY `lecturer_id` (`lecturer_id`);

--
-- Indexes for table `class_students`
--
ALTER TABLE `class_students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `practicum_class_id_student_nim` (`practicum_class_id`,`student_nim`),
  ADD KEY `practicum_class_id` (`practicum_class_id`),
  ADD KEY `student_nim` (`student_nim`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `coordinators`
--
ALTER TABLE `coordinators`
  ADD PRIMARY KEY (`user_nid`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`),
  ADD KEY `study_program_id` (`study_program_id`);

--
-- Indexes for table `export_logs`
--
ALTER TABLE `export_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `export_type` (`export_type`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `final_scores`
--
ALTER TABLE `final_scores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `practicum_class_id_student_id` (`practicum_class_id`,`student_id`),
  ADD KEY `practicum_class_id` (`practicum_class_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `status` (`status`),
  ADD KEY `validation_status` (`validation_status`);

--
-- Indexes for table `grade_scales`
--
ALTER TABLE `grade_scales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `scale_code` (`scale_code`),
  ADD KEY `grade_letter` (`grade_letter`);

--
-- Indexes for table `huruf_mutu`
--
ALTER TABLE `huruf_mutu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `import_logs`
--
ALTER TABLE `import_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `import_type` (`import_type`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelas_mata_kuliah_id_foreign` (`mata_kuliah_id`),
  ADD KEY `kelas_tahun_akademik_id_foreign` (`tahun_akademik_id`);

--
-- Indexes for table `laboratories`
--
ALTER TABLE `laboratories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_code` (`room_code`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`user_nid`);

--
-- Indexes for table `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_mk` (`kode_mk`),
  ADD KEY `mata_kuliah_prodi_id_foreign` (`prodi_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_read` (`is_read`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `passing_rules`
--
ALTER TABLE `passing_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `practicum_classes`
--
ALTER TABLE `practicum_classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `class_code` (`class_code`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `semester_id` (`semester_id`),
  ADD KEY `laboratory_id` (`laboratory_id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `practicum_groups`
--
ALTER TABLE `practicum_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `practicum_class_id_group_code` (`practicum_class_id`,`group_code`),
  ADD KEY `practicum_class_id` (`practicum_class_id`);

--
-- Indexes for table `prodi`
--
ALTER TABLE `prodi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_prodi` (`kode_prodi`);

--
-- Indexes for table `remedial_components`
--
ALTER TABLE `remedial_components`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `remedial_period_id_component_id` (`remedial_period_id`,`component_id`),
  ADD KEY `remedial_period_id` (`remedial_period_id`),
  ADD KEY `component_id` (`component_id`);

--
-- Indexes for table `remedial_logs`
--
ALTER TABLE `remedial_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `remedial_logs_created_by_foreign` (`created_by`),
  ADD KEY `remedial_period_id` (`remedial_period_id`),
  ADD KEY `remedial_participant_id` (`remedial_participant_id`),
  ADD KEY `event_type` (`event_type`);

--
-- Indexes for table `remedial_participants`
--
ALTER TABLE `remedial_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `remedial_period_id_student_id_practicum_class_id` (`remedial_period_id`,`student_id`,`practicum_class_id`),
  ADD KEY `remedial_period_id` (`remedial_period_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `practicum_class_id` (`practicum_class_id`),
  ADD KEY `final_score_id` (`final_score_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `remedial_periods`
--
ALTER TABLE `remedial_periods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `remedial_code` (`remedial_code`),
  ADD KEY `semester_id` (`semester_id`);

--
-- Indexes for table `remedial_results`
--
ALTER TABLE `remedial_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `remedial_participant_id` (`remedial_participant_id`),
  ADD KEY `remedial_results_validated_by_foreign` (`validated_by`),
  ADD KEY `validation_status` (`validation_status`);

--
-- Indexes for table `remedial_rules`
--
ALTER TABLE `remedial_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `remedial_scores`
--
ALTER TABLE `remedial_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `remedial_scores_entered_by_foreign` (`entered_by`),
  ADD KEY `remedial_participant_id` (`remedial_participant_id`),
  ADD KEY `remedial_component_id` (`remedial_component_id`);

--
-- Indexes for table `report_logs`
--
ALTER TABLE `report_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `report_type` (`report_type`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_id_permission_id` (`role_id`,`permission_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `score_change_logs`
--
ALTER TABLE `score_change_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `score_entry_id` (`score_entry_id`),
  ADD KEY `final_score_id` (`final_score_id`),
  ADD KEY `changed_by` (`changed_by`),
  ADD KEY `change_type` (`change_type`);

--
-- Indexes for table `score_details`
--
ALTER TABLE `score_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `score_entry_id` (`score_entry_id`);

--
-- Indexes for table `score_entries`
--
ALTER TABLE `score_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `practicum_class_id_student_id_component_id_subcomponent_id` (`practicum_class_id`,`student_id`,`component_id`,`subcomponent_id`),
  ADD KEY `practicum_class_id` (`practicum_class_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `component_id` (`component_id`),
  ADD KEY `subcomponent_id` (`subcomponent_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `score_notes`
--
ALTER TABLE `score_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `score_entry_id` (`score_entry_id`),
  ADD KEY `final_score_id` (`final_score_id`);

--
-- Indexes for table `score_revision_requests`
--
ALTER TABLE `score_revision_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `final_score_id` (`final_score_id`),
  ADD KEY `requested_by` (`requested_by`),
  ADD KEY `requested_to` (`requested_to`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `score_statuses`
--
ALTER TABLE `score_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `academic_year_id_semester_number` (`academic_year_id`,`semester_number`),
  ADD UNIQUE KEY `semester_code` (`semester_code`),
  ADD KEY `academic_year_id` (`academic_year_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`user_nim`);

--
-- Indexes for table `study_programs`
--
ALTER TABLE `study_programs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `program_code` (`program_code`);

--
-- Indexes for table `tahun_akademik`
--
ALTER TABLE `tahun_akademik`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `template_nilai`
--
ALTER TABLE `template_nilai`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login_identifier` (`login_identifier`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `identifier_type` (`identifier_type`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id_role_id` (`user_id`,`role_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `validation_logs`
--
ALTER TABLE `validation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `practicum_class_id` (`practicum_class_id`),
  ADD KEY `score_entry_id` (`score_entry_id`),
  ADD KEY `final_score_id` (`final_score_id`),
  ADD KEY `validator_user_id` (`validator_user_id`),
  ADD KEY `action` (`action`),
  ADD KEY `result` (`result`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assessment_components`
--
ALTER TABLE `assessment_components`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assessment_subcomponents`
--
ALTER TABLE `assessment_subcomponents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assessment_templates`
--
ALTER TABLE `assessment_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_records`
--
ALTER TABLE `attendance_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_statuses`
--
ALTER TABLE `attendance_statuses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `class_assistants`
--
ALTER TABLE `class_assistants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_lecturers`
--
ALTER TABLE `class_lecturers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_students`
--
ALTER TABLE `class_students`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `export_logs`
--
ALTER TABLE `export_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `final_scores`
--
ALTER TABLE `final_scores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grade_scales`
--
ALTER TABLE `grade_scales`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `huruf_mutu`
--
ALTER TABLE `huruf_mutu`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `import_logs`
--
ALTER TABLE `import_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laboratories`
--
ALTER TABLE `laboratories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `passing_rules`
--
ALTER TABLE `passing_rules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `practicum_classes`
--
ALTER TABLE `practicum_classes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `practicum_groups`
--
ALTER TABLE `practicum_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prodi`
--
ALTER TABLE `prodi`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remedial_components`
--
ALTER TABLE `remedial_components`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remedial_logs`
--
ALTER TABLE `remedial_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remedial_participants`
--
ALTER TABLE `remedial_participants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remedial_periods`
--
ALTER TABLE `remedial_periods`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remedial_results`
--
ALTER TABLE `remedial_results`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remedial_rules`
--
ALTER TABLE `remedial_rules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remedial_scores`
--
ALTER TABLE `remedial_scores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report_logs`
--
ALTER TABLE `report_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `score_change_logs`
--
ALTER TABLE `score_change_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `score_details`
--
ALTER TABLE `score_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `score_entries`
--
ALTER TABLE `score_entries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `score_notes`
--
ALTER TABLE `score_notes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `score_revision_requests`
--
ALTER TABLE `score_revision_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `score_statuses`
--
ALTER TABLE `score_statuses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `study_programs`
--
ALTER TABLE `study_programs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tahun_akademik`
--
ALTER TABLE `tahun_akademik`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `template_nilai`
--
ALTER TABLE `template_nilai`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `validation_logs`
--
ALTER TABLE `validation_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_user_nip_foreign` FOREIGN KEY (`user_nip`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assessment_components`
--
ALTER TABLE `assessment_components`
  ADD CONSTRAINT `assessment_components_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `assessment_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assessment_subcomponents`
--
ALTER TABLE `assessment_subcomponents`
  ADD CONSTRAINT `assessment_subcomponents_component_id_foreign` FOREIGN KEY (`component_id`) REFERENCES `assessment_components` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assessment_templates`
--
ALTER TABLE `assessment_templates`
  ADD CONSTRAINT `assessment_templates_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `assessment_templates_study_program_id_foreign` FOREIGN KEY (`study_program_id`) REFERENCES `study_programs` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints for table `assistants`
--
ALTER TABLE `assistants`
  ADD CONSTRAINT `assistants_user_nim_foreign` FOREIGN KEY (`user_nim`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `coordinators`
--
ALTER TABLE `coordinators`
  ADD CONSTRAINT `coordinators_user_nid_foreign` FOREIGN KEY (`user_nid`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_study_program_id_foreign` FOREIGN KEY (`study_program_id`) REFERENCES `study_programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `export_logs`
--
ALTER TABLE `export_logs`
  ADD CONSTRAINT `export_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `import_logs`
--
ALTER TABLE `import_logs`
  ADD CONSTRAINT `import_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `kelas_mata_kuliah_id_foreign` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_tahun_akademik_id_foreign` FOREIGN KEY (`tahun_akademik_id`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD CONSTRAINT `lecturers_user_nid_foreign` FOREIGN KEY (`user_nid`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  ADD CONSTRAINT `mata_kuliah_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `passing_rules`
--
ALTER TABLE `passing_rules`
  ADD CONSTRAINT `passing_rules_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `passing_rules_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `assessment_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `practicum_groups`
--
ALTER TABLE `practicum_groups`
  ADD CONSTRAINT `practicum_groups_practicum_class_id_foreign` FOREIGN KEY (`practicum_class_id`) REFERENCES `practicum_classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `remedial_components`
--
ALTER TABLE `remedial_components`
  ADD CONSTRAINT `remedial_components_component_id_foreign` FOREIGN KEY (`component_id`) REFERENCES `assessment_components` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `remedial_components_remedial_period_id_foreign` FOREIGN KEY (`remedial_period_id`) REFERENCES `remedial_periods` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `remedial_logs`
--
ALTER TABLE `remedial_logs`
  ADD CONSTRAINT `remedial_logs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `remedial_logs_remedial_participant_id_foreign` FOREIGN KEY (`remedial_participant_id`) REFERENCES `remedial_participants` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `remedial_logs_remedial_period_id_foreign` FOREIGN KEY (`remedial_period_id`) REFERENCES `remedial_periods` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints for table `remedial_periods`
--
ALTER TABLE `remedial_periods`
  ADD CONSTRAINT `remedial_periods_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints for table `remedial_results`
--
ALTER TABLE `remedial_results`
  ADD CONSTRAINT `remedial_results_remedial_participant_id_foreign` FOREIGN KEY (`remedial_participant_id`) REFERENCES `remedial_participants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `remedial_results_validated_by_foreign` FOREIGN KEY (`validated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints for table `remedial_rules`
--
ALTER TABLE `remedial_rules`
  ADD CONSTRAINT `remedial_rules_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `remedial_rules_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `assessment_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `remedial_scores`
--
ALTER TABLE `remedial_scores`
  ADD CONSTRAINT `remedial_scores_entered_by_foreign` FOREIGN KEY (`entered_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `remedial_scores_remedial_component_id_foreign` FOREIGN KEY (`remedial_component_id`) REFERENCES `remedial_components` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `remedial_scores_remedial_participant_id_foreign` FOREIGN KEY (`remedial_participant_id`) REFERENCES `remedial_participants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `report_logs`
--
ALTER TABLE `report_logs`
  ADD CONSTRAINT `report_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `score_change_logs`
--
ALTER TABLE `score_change_logs`
  ADD CONSTRAINT `score_change_logs_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `score_change_logs_final_score_id_foreign` FOREIGN KEY (`final_score_id`) REFERENCES `final_scores` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `score_change_logs_score_entry_id_foreign` FOREIGN KEY (`score_entry_id`) REFERENCES `score_entries` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints for table `score_details`
--
ALTER TABLE `score_details`
  ADD CONSTRAINT `score_details_score_entry_id_foreign` FOREIGN KEY (`score_entry_id`) REFERENCES `score_entries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `score_revision_requests`
--
ALTER TABLE `score_revision_requests`
  ADD CONSTRAINT `score_revision_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `score_revision_requests_final_score_id_foreign` FOREIGN KEY (`final_score_id`) REFERENCES `final_scores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `score_revision_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `score_revision_requests_requested_to_foreign` FOREIGN KEY (`requested_to`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints for table `semesters`
--
ALTER TABLE `semesters`
  ADD CONSTRAINT `semesters_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_user_nim_foreign` FOREIGN KEY (`user_nim`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `validation_logs`
--
ALTER TABLE `validation_logs`
  ADD CONSTRAINT `validation_logs_final_score_id_foreign` FOREIGN KEY (`final_score_id`) REFERENCES `final_scores` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `validation_logs_practicum_class_id_foreign` FOREIGN KEY (`practicum_class_id`) REFERENCES `practicum_classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `validation_logs_score_entry_id_foreign` FOREIGN KEY (`score_entry_id`) REFERENCES `score_entries` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `validation_logs_validator_user_id_foreign` FOREIGN KEY (`validator_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
