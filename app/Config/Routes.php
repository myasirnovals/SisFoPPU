<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ═══════════════════════════════════════════════════════════════════════════
// AUTH
// ═══════════════════════════════════════════════════════════════════════════

$routes->get('/', 'AuthController::login');
$routes->get('login',  'AuthController::login',        ['filter' => ['auth:guest']]);
$routes->post('login', 'AuthController::attemptLogin', ['filter' => ['csrf', 'auth:guest']]);
$routes->get('logout', 'AuthController::logout',       ['filter' => 'auth:protected']);

// ═══════════════════════════════════════════════════════════════════════════
// ADMIN
// ═══════════════════════════════════════════════════════════════════════════

$routes->group('admin', ['filter' => ['auth:protected', 'role:admin']], static function ($routes) {
    $routes->get('dashboard',  'Admin\Dashboard::index');
    $routes->get('matakuliah', 'Admin\MataKuliah::index');
    $routes->get('template',   'Admin\Template::index');
    $routes->get('pengguna',   'Admin\Pengguna::index');
    $routes->get('kelas',      'Admin\Kelas::index');
    $routes->get('hurufmutu',  'Admin\HurufMutu::index');

    $routes->get('akademik',   'Admin\Akademik::index');
    $routes->post('akademik/prodi/store', 'Admin\Akademik::storeProdi');
    $routes->post('akademik/prodi/update', 'Admin\Akademik::updateProdi');
    $routes->post('akademik/prodi/delete/(:num)', 'Admin\Akademik::deleteProdi/$1');
    $routes->post('akademik/tahun/store', 'Admin\Akademik::storeTahun');
    $routes->post('akademik/tahun/set-active/(:num)', 'Admin\Akademik::setActiveTahun/$1');
    $routes->post('akademik/tahun/delete/(:num)', 'Admin\Akademik::deleteTahun/$1');
});

// ═══════════════════════════════════════════════════════════════════════════
// KOORDINATOR
// ═══════════════════════════════════════════════════════════════════════════

$routes->group('coordinator', ['filter' => ['auth:protected', 'role:koordinator']], static function ($routes) {
    $routes->get('dashboard',  'Coordinator\DashboardController::index');
    $routes->get('classes',    'Coordinator\DashboardController::classes');
    $routes->get('attention',  'Coordinator\DashboardController::attention');
    $routes->get('remedial',   'Coordinator\DashboardController::remedial');
    $routes->get('validation', 'Coordinator\DashboardController::validation');
    $routes->get('activity',   'Coordinator\DashboardController::activity');
});

// ═══════════════════════════════════════════════════════════════════════════
// DOSEN
// ═══════════════════════════════════════════════════════════════════════════

$routes->group('dosen', ['filter' => ['auth:protected', 'role:dosen,admin,koordinator']], static function ($routes) {
    $routes->get('dashboard', 'Dosen\DashboardController::index');
});

// ═══════════════════════════════════════════════════════════════════════════
// ASISTEN
// ═══════════════════════════════════════════════════════════════════════════

$routes->group('assistant', ['filter' => ['auth:protected', 'role:asisten']], static function ($routes) {

    // Dashboard Utama
    $routes->get('dashboard', 'Assistant\DashboardController::index');

    // Sub-halaman Dashboard Asisten
    $routes->get('kelas',     'Assistant\DashboardController::kelas');
    $routes->get('absensi',   'Assistant\DashboardController::absensi');
    $routes->get('nilai',     'Assistant\DashboardController::nilai');
    $routes->get('remedial',  'Assistant\DashboardController::remedial');
    $routes->get('aktivitas', 'Assistant\DashboardController::aktivitas');
});

// ═══════════════════════════════════════════════════════════════════════════
// MAHASISWA
// ═══════════════════════════════════════════════════════════════════════════

$routes->group('mahasiswa', ['filter' => ['auth:protected', 'role:mahasiswa']], static function ($routes) {

    // ── Dashboard Utama / Ringkasan ──────────────────────────────────────
    $routes->get('dashboard', 'Mahasiswa\Dashboard::index');

    // ── Sub-halaman Dashboard ────────────────────────────────────────────
    $routes->get('dashboard/praktikum',  'Mahasiswa\Dashboard::praktikum');
    $routes->get('dashboard/kehadiran',  'Mahasiswa\Dashboard::kehadiran');
    $routes->get('dashboard/nilai',      'Mahasiswa\Dashboard::nilai');
    $routes->get('dashboard/remedial',   'Mahasiswa\Dashboard::remedial');
    $routes->get('dashboard/notifikasi', 'Mahasiswa\Dashboard::notifikasi');

    // ── Detail Praktikum ─────────────────────────────────────────────────
    $routes->get('praktikum/(:num)/detail', 'Mahasiswa\Dashboard::detail/$1');
});
