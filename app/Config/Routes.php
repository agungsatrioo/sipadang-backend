<?php namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

$routes->get('inputapi/mahasiswa', 'Inputapi::mahasiswa');
$routes->get('inputapi/mahasiswa/(:segment)', 'Inputapi::mahasiswa/$1');

$routes->get('inputapi/is_first_run/(:segment)', 'Inputapi::is_first_run/$1');

$routes->get('inputapi/revisi', 'Inputapi::get_revisi');
$routes->post('inputapi/revisi', 'Inputapi::input_revisi');
$routes->put('inputapi/revisi', 'Inputapi::input_revisi/true');
$routes->put('inputapi/delrevisi', 'Inputapi::delete_revisi');

$routes->post('inputapi/nilai', 'Inputapi::input_nilai');
$routes->put('inputapi/nilai', 'Inputapi::input_nilai/true');

$routes->post('inputapi/auth', 'Inputapi::auth');
$routes->post('inputapi/verify', 'Inputapi::auth/verify');

$routes->add('webauth', 'WebAuth::index');
$routes->add('webauth/do_auth', 'WebAuth::do_auth');

$routes->group('management', function($routes){
	$routes->add('/', 'Management::index');
	$routes->add('logout', 'Management::logout');

	$routes->add('judul_munaqosah/(:alphanum)/edit', 'Management::ganti_judul/munaqosah/$1');
	$routes->add('judul_proposal/(:alphanum)/edit', 'Management::ganti_judul/proposal/$1');

	$routes->add('proposal/penguji/(:alphanum)/(:alphanum)/edit', "Management::ganti_dosen/proposal/penguji/$1/$2");
	$routes->add('kompre/penguji/(:alphanum)/(:alphanum)/edit', "Management::ganti_dosen/kompre/penguji/$1/$2");
	$routes->add('munaqosah/penguji/(:alphanum)/(:alphanum)/edit', "Management::ganti_dosen/munaqosah/penguji/$1/$2");
	
	$routes->add('munaqosah/pembimbing/(:alphanum)/(:alphanum)/edit', "Management::ganti_dosen/munaqosah/pembimbing/$1/$2");

	$routes->group('tanggal', function($routes) {
		$routes->add('', 'Management::tanggal');
		$routes->add('add', 'Management::tanggal_form');	
		$routes->add('(:num)/edit', 'Management::tanggal_form/$1');
		$routes->add('(:num)/delete', 'Management::tanggal_delete/$1');
	});

	$routes->group('jadwal', function($routes) {
		$routes->add('', 'Management::jadwal');
		$routes->add('add', 'Management::jadwal_form');	
		$routes->add('(:num)/edit', 'Management::jadwal_form/$1');
		$routes->add('(:num)/delete', 'Management::jadwal_delete/$1');
	});

	$routes->group('ruangan', function($routes) {
		$routes->add('', 'Management::ruangan');
		$routes->add('add', 'Management::ruangan_form');	
		$routes->add('(:num)/edit', 'Management::ruangan_form/$1');
		$routes->add('(:num)/delete', 'Management::ruangan_delete/$1');
	});

	$routes->group('mahasiswa', function($routes) {
		$routes->add('', 'Management::list_mhs');
		$routes->add('add', 'Management::mhs_form');	
		$routes->add('(:num)/edit', 'Management::mhs_form/$1');
		$routes->add('(:num)/delete', 'Management::mhs_delete/$1');
	});
	
	$routes->group('dosen', function($routes) {
		$routes->add('', 'Management::list_dosen');
		$routes->add('add', 'Management::dosen_form');	
		$routes->add('(:segment)/edit', 'Management::dosen_form/$1');
		$routes->add('(:segment)/delete', 'Management::dosen_delete/$1');
	});

	$routes->group('users', function($routes) {
		$routes->add('', 'Management::list_user');
		$routes->add('add', 'Management::user_form');	
		$routes->add('(:segment)/reset_password', 'Management::user_reset/$1');
		$routes->add('(:segment)/(:segment)/delete', 'Management::user_delete/$1/$2');
	});

}); 


/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need to it be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
