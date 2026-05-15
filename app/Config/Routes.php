<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'AuthController::login');
$routes->match(['get', 'post'], 'login', 'AuthController::login', ['filter' => ['csrf', 'auth:guest']]);
$routes->get('logout', 'AuthController::logout', ['filter' => 'auth:protected']);

$routes->group('admin', ['filter' => ['auth:protected', 'role:admin']], static function ($routes) {
	$routes->get('dashboard', 'Admin\Dashboard::index');
	$routes->get('matakuliah', 'Admin\MataKuliah::index');
	$routes->get('template', 'Admin\Template::index');
	$routes->get('pengguna', 'Admin\Pengguna::index');
});

$routes->group('koordinator', ['filter' => ['auth:protected', 'role:koordinator']], static function ($routes) {
	$routes->get('dashboard', 'Coordinator\DashboardController::index');
	$routes->get('classes', 'Coordinator\DashboardController::classes');
	$routes->get('attention', 'Coordinator\DashboardController::attention');
	$routes->get('remedial', 'Coordinator\DashboardController::remedial');
	$routes->get('validation', 'Coordinator\DashboardController::validation');
	$routes->get('activity', 'Coordinator\DashboardController::activity');
});

$routes->group('dosen', ['filter' => ['auth:protected', 'role:dosen']], static function ($routes) {
	$routes->get('dashboard', 'Dosen\Dashboard::index');
});

$routes->group('asisten', ['filter' => ['auth:protected', 'role:asisten']], static function ($routes) {
	$routes->get('dashboard', 'Asisten\Dashboard::index');
});

$routes->group('mahasiswa', ['filter' => ['auth:protected', 'role:mahasiswa']], static function ($routes) {
	$routes->get('dashboard', 'Mahasiswa\Dashboard::index');
});
