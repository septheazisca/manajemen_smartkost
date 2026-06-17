<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// =====================
// FRONT PAGE & DETAIL
// =====================
$routes->get('/', 'HomeController::index');
$routes->get('/kamar/(:num)', 'HomeController::detail/$1');

// =====================
// AUTH
// =====================
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::attemptLogin');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/unauthorized', 'AuthController::unauthorized');
$routes->get('/change-password', 'AuthController::changePassword', ['filter' => 'role:admin,pj,penyewa']);
$routes->post('/change-password', 'AuthController::updatePassword', ['filter' => 'role:admin,pj,penyewa']);

// =====================
// ADMIN
// =====================
$routes->group('admin', ['filter' => 'role:admin'], function ($routes) {

    $routes->get('dashboard', 'DashboardController::index');

    // fasilitas
    $routes->get('fasilitas', 'FasilitasController::index');
    $routes->post('fasilitas/store', 'FasilitasController::store');
    $routes->post('fasilitas/update/(:num)', 'FasilitasController::update/$1');
    $routes->get('fasilitas/delete/(:num)', 'FasilitasController::delete/$1');
    
    // fasilitas bersama (shared)
    $routes->post('fasilitas-bersama/store', 'FasilitasController::storeShared');
    $routes->post('fasilitas-bersama/update/(:num)', 'FasilitasController::updateShared/$1');
    $routes->get('fasilitas-bersama/delete/(:num)', 'FasilitasController::deleteShared/$1');

    // kamar
    $routes->get('kamar', 'KamarController::index');
    $routes->post('kamar/store', 'KamarController::store');
    $routes->post('kamar/update/(:num)', 'KamarController::update/$1');
    $routes->get('kamar/delete/(:num)', 'KamarController::delete/$1');

    // penyewa
    $routes->get('penyewa', 'PenyewaController::index');
    $routes->post('penyewa/store', 'PenyewaController::store');
    $routes->post('penyewa/update/(:num)', 'PenyewaController::update/$1');
    $routes->get('penyewa/toggle-status/(:num)', 'PenyewaController::toggleStatus/$1');
    $routes->get('penyewa/reset-password/(:num)', 'PenyewaController::resetPassword/$1');
    $routes->get('penyewa/checkout/(:num)', 'PenyewaController::checkout/$1');

    // tagihan - spesifik dulu, dynamic di bawah
    $routes->get('tagihan', 'TagihanController::index');
    $routes->post('tagihan/generate', 'TagihanController::generate');
    $routes->post('tagihan/approve/(:num)', 'TagihanController::approve/$1');
    $routes->post('tagihan/tolak/(:num)', 'TagihanController::tolak/$1');
    $routes->post('tagihan/tandai-menunggak/(:num)', 'TagihanController::tandaiMenunggak/$1');
    $routes->get('tagihan/(:num)', 'TagihanController::show/$1');
    $routes->get('tagihan/export-excel', 'TagihanController::exportExcel');

    // maintenance - spesifik dulu, dynamic di bawah
    $routes->get('maintenance', 'MaintenanceController::index');
    $routes->post('maintenance/assign/(:num)', 'MaintenanceController::assign/$1');
    $routes->get('maintenance/delete/(:num)', 'MaintenanceController::delete/$1');
    $routes->get('maintenance/(:num)', 'MaintenanceController::detail/$1');

    // penanggung jawab
    $routes->get('pj', 'PenanggungJawabController::index');
    $routes->post('pj/store', 'PenanggungJawabController::store');
    $routes->post('pj/update/(:num)', 'PenanggungJawabController::update/$1');
    $routes->get('pj/toggle-status/(:num)', 'PenanggungJawabController::toggleStatus/$1');
    $routes->get('pj/reset-password/(:num)', 'PenanggungJawabController::resetPassword/$1');
    $routes->post('pj/bayar-gaji/(:num)', 'PenanggungJawabController::bayarGaji/$1');
    $routes->get('pj/riwayat-gaji/(:num)', 'PenanggungJawabController::riwayatGaji/$1');
    $routes->get('pj/export-gaji/(:num)', 'PenanggungJawabController::exportGaji/$1');


    // pengeluaran
    $routes->get('pengeluaran', 'PengeluaranController::index');
    $routes->post('pengeluaran/store', 'PengeluaranController::store');
    $routes->post('pengeluaran/update/(:num)', 'PengeluaranController::update/$1');
    $routes->get('pengeluaran/delete/(:num)', 'PengeluaranController::delete/$1');
    $routes->get('pengeluaran/rekap', 'PengeluaranController::rekap');
    $routes->get('pengeluaran/export-excel', 'PengeluaranController::exportExcel');

    // laporan
    $routes->get('laporan', 'LaporanController::index');
    $routes->get('laporan/tagihan', 'LaporanController::tagihan');
    $routes->get('laporan/maintenance', 'LaporanController::maintenance');
    $routes->get('laporan/export-pdf', 'LaporanController::exportPdf');
    $routes->get('laporan/export-excel', 'LaporanController::exportExcel');

    // notifikasi
    $routes->get('notifikasi', 'NotifikasiController::index');
    $routes->get('notifikasi/log', 'NotifikasiController::log');
    $routes->post('notifikasi/kirim-custom', 'NotifikasiController::kirimCustom');
    $routes->post('notifikasi/kirim-reminder-tagihan', 'NotifikasiController::kirimReminderTagihan');
    $routes->post('notifikasi/kirim-reminder-tunggakan', 'NotifikasiController::kirimReminderTunggakan');
    $routes->post('notifikasi/kirim-info', 'NotifikasiController::kirimInfo');

    // detail kost
    $routes->get('detail-kost', 'DetailKostController::index');
    $routes->post('detail-kost/update', 'DetailKostController::update');

    // profil admin
    $routes->get('profile', 'AdminProfileController::index');
    $routes->post('profile/update', 'AdminProfileController::update');
});

// =====================
// PENANGGUNG JAWAB
// =====================
$routes->group('pj', ['filter' => 'role:pj'], function ($routes) {

    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('export-gaji', 'PenanggungJawabController::exportGajiSelf');

    // maintenance - spesifik dulu, dynamic di bawah
    $routes->get('maintenance', 'MaintenanceController::indexPj');
    $routes->get('maintenance/ambil/(:num)', 'MaintenanceController::ambil/$1');
    $routes->post('maintenance/selesai/(:num)', 'MaintenanceController::selesai/$1');
    $routes->get('maintenance/(:num)', 'MaintenanceController::detail/$1');
});

// =====================
// PENYEWA / TENANT
// =====================
$routes->group('tenant', ['filter' => 'role:penyewa'], function ($routes) {

    $routes->get('dashboard', 'DashboardController::index');

    // profil
    $routes->get('profile', 'PenyewaController::profile');
    $routes->post('profile/update', 'PenyewaController::updateProfile');

    // tagihan - spesifik dulu, dynamic di bawah
    $routes->get('tagihan', 'TagihanController::tagihanSaya');
    $routes->get('tagihan/export', 'TagihanController::exportExcelSaya');
    $routes->get('tagihan/detail/(:num)', 'TagihanController::detailTagihan/$1');
    $routes->post('tagihan/upload-bukti/(:num)', 'TagihanController::uploadBukti/$1');

    // maintenance - spesifik dulu, dynamic di bawah
    $routes->get('maintenance', 'MaintenanceController::laporanSaya');
    $routes->post('maintenance/lapor', 'MaintenanceController::lapor');
    $routes->get('maintenance/(:num)', 'MaintenanceController::detailTenant/$1');

    // rating & testimoni
    $routes->post('rating/save', 'PenyewaController::saveRating');
    $routes->get('rating/toggle', 'PenyewaController::toggleRatingVisibility');
});
