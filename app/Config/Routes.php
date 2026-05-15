<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->match(['get', 'post'], '/', 'Login::index');
$routes->match(['get', 'post'], 'login', 'Login::index');

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
