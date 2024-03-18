<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// Scan
$routes->get('/', 'Scan::index');

$routes->group('scan', function (RouteCollection $routes) {
   $routes->get('', 'Scan::index');
   $routes->get('masuk', 'Scan::index/Masuk');
   $routes->get('pulang', 'Scan::index/Pulang');

   $routes->post('cek', 'Scan::cekKode');
});

// Admin
$routes->group('admin', function (RouteCollection $routes) {
   // Admin dashboard
   $routes->get('', 'Admin\Dashboard::index');
   $routes->get('dashboard', 'Admin\Dashboard::index');

   // admin lihat data perangkat
   $routes->get('perangkat', 'Admin\DataPerangkat::index');
   $routes->post('perangkat', 'Admin\DataPerangkat::ambilDataPerangkat');
   // admin tambah data perangkat
   $routes->get('perangkat/create', 'Admin\DataPerangkat::formTambahPerangkat');
   $routes->post('perangkat/create', 'Admin\DataPerangkat::savePerangkat');
   // admin edit data perangkat
   $routes->get('perangkat/edit/(:any)', 'Admin\DataPerangkat::formEditPerangkat/$1');
   $routes->post('perangkat/edit', 'Admin\DataPerangkat::updatePerangkat');
   // admin hapus data perangkat
   $routes->delete('perangkat/delete/(:any)', 'Admin\DataPerangkat::delete/$1');


   // admin lihat data guru
   $routes->get('guru', 'Admin\DataGuru::index');
   $routes->post('guru', 'Admin\DataGuru::ambilDataGuru');
   // admin tambah data guru
   $routes->get('guru/create', 'Admin\DataGuru::formTambahGuru');
   $routes->post('guru/create', 'Admin\DataGuru::saveGuru');
   // admin edit data guru
   $routes->get('guru/edit/(:any)', 'Admin\DataGuru::formEditGuru/$1');
   $routes->post('guru/edit', 'Admin\DataGuru::updateGuru');
   // admin hapus data guru
   $routes->delete('guru/delete/(:any)', 'Admin\DataGuru::delete/$1');


   // admin lihat data absen perangkat
   $routes->get('absen-perangkat', 'Admin\DataAbsenPerangkat::index');
   $routes->post('absen-perangkat', 'Admin\DataAbsenPerangkat::ambilDataPerangkat'); // ambil perangkat berdasarkan kelas dan tanggal
   $routes->post('absen-perangkat/kehadiran', 'Admin\DataAbsenPerangkat::ambilKehadiran'); // ambil kehadiran perangkat
   $routes->post('absen-perangkat/edit', 'Admin\DataAbsenPerangkat::ubahKehadiran'); // ubah kehadiran perangkat

   $routes->post('tambah-kelas', 'Admin\DataAbsenPerangkat::tambahKelas'); // tambah data kelas

   // admin lihat data absen guru
   $routes->get('absen-guru', 'Admin\DataAbsenGuru::index');
   $routes->post('absen-guru', 'Admin\DataAbsenGuru::ambilDataGuru'); // ambil guru berdasarkan tanggal
   $routes->post('absen-guru/kehadiran', 'Admin\DataAbsenGuru::ambilKehadiran'); // ambil kehadiran guru
   $routes->post('absen-guru/edit', 'Admin\DataAbsenGuru::ubahKehadiran'); // ubah kehadiran guru

   // admin generate QR
   $routes->get('generate', 'Admin\GenerateQR::index');
   $routes->post('generate/perangkat-by-kelas', 'Admin\GenerateQR::getPerangkatByKelas');

   $routes->post('generate/perangkat', 'Admin\QRGenerator::generateQrPerangkat');
   $routes->post('generate/guru', 'Admin\QRGenerator::generateQrGuru');

   // admin buat laporan
   $routes->get('laporan', 'Admin\GenerateLaporan::index');
   $routes->post('laporan/perangkat', 'Admin\GenerateLaporan::generateLaporanPerangkat');
   $routes->post('laporan/guru', 'Admin\GenerateLaporan::generateLaporanGuru');

   // superadmin lihat data petugas
   $routes->get('petugas', 'Admin\DataPetugas::index');
   $routes->post('petugas', 'Admin\DataPetugas::ambilDataPetugas');
   // superadmin tambah data petugas
   $routes->get('petugas/register', 'Admin\DataPetugas::registerPetugas');
   // superadmin edit data petugas
   $routes->get('petugas/edit/(:any)', 'Admin\DataPetugas::formEditPetugas/$1');
   $routes->post('petugas/edit', 'Admin\DataPetugas::updatePetugas');
   // superadmin hapus data petugas
   $routes->delete('petugas/delete/(:any)', 'Admin\DataPetugas::delete/$1');
});


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
   require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
