<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/', function () {
    return redirect()->to('/login');
});

$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::attemptLogin');
$routes->get('/logout', 'AuthController::logout');

$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('/dashboard', 'DashboardController::index');
});

/*
 * Contoh route berdasarkan role
 */
$routes->group('admin', ['filter' => 'role:admin'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');
});

$routes->group('dosen', ['filter' => 'role:dosen,koordinator,admin'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');
});

$routes->group('asisten', ['filter' => 'role:asisten,dosen,koordinator,admin'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');
});

$routes->group('mahasiswa', ['filter' => 'role:mahasiswa'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');
});

$routes->group('admin', ['filter' => 'admin.access'], static function ($routes) {
	$routes->get('dashboard', 'Admin\Dashboard::index');
	$routes->get('matakuliah', 'Admin\MataKuliah::index');
	$routes->get('template', 'Admin\Template::index');
	$routes->get('pengguna', 'Admin\Pengguna::index');
});

$routes->group('coordinator', static function ($routes) {
	$routes->get('dashboard', 'Coordinator\DashboardController::index');
	$routes->get('classes', 'Coordinator\DashboardController::classes');
	$routes->get('attention', 'Coordinator\DashboardController::attention');
	$routes->get('remedial', 'Coordinator\DashboardController::remedial');
	$routes->get('validation', 'Coordinator\DashboardController::validation');
	$routes->get('activity', 'Coordinator\DashboardController::activity');
});