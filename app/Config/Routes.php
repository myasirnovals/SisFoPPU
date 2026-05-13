<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/admin/dashboard', 'Admin\Dashboard::index');
$routes->get('/admin/matakuliah', 'Admin\MataKuliah::index');
$routes->get('/admin/template', 'Admin\Template::index');
