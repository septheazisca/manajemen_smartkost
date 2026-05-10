<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::login');
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::attemptLogin');
$routes->get('/logout', 'AuthController::logout');

$routes->get('/unauthorized', 'AuthController::unauthorized');

$routes->group('admin', ['filter' => 'role:admin'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    
    // Fasilitas
    $routes->get('fasilitas', 'FasilitasController::index');
    $routes->post('fasilitas/store', 'FasilitasController::store');
    $routes->post('fasilitas/update/(:num)', 'FasilitasController::update/$1');
    $routes->get('fasilitas/delete/(:num)', 'FasilitasController::delete/$1');

    // Kamar
    $routes->get('kamar', 'KamarController::index');
    $routes->post('kamar/store', 'KamarController::store');
    $routes->post('kamar/update/(:num)', 'KamarController::update/$1');
    $routes->get('kamar/delete/(:num)', 'KamarController::delete/$1');

    // nanti extend fitur
    $routes->get('rooms', 'Room::index');
    $routes->get('tenants', 'Tenant::index');
    $routes->get('payments', 'Payment::index');
    $routes->get('reports', 'Report::index');
});

$routes->group('pj', ['filter' => 'role:pj'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('maintenance', 'Maintenance::index');
    $routes->get('reports', 'Report::pjReport');
});

$routes->group('tenant', ['filter' => 'role:penyewa'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('room', 'Tenant::room');
    $routes->get('payment', 'Tenant::payment');
    $routes->get('complaint', 'Complaint::index');
});
