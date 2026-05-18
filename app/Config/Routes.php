<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/admin/dashboard', 'Admin\Dashboard::index');
$routes->get('/admin/matakuliah', 'Admin\MataKuliah::index');
$routes->get('/admin/template', 'Admin\Template::index');
$routes->get('admin/pengguna', 'Admin\Pengguna::index');
$routes->get('admin/kelas', 'Admin\Kelas::index');
$routes->get('admin/hurufmutu', 'Admin\HurufMutu::index');
$routes->get('admin/akademik', 'Admin\Akademik::index');
