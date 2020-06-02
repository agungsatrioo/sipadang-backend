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

	$routes->group('tanggal', function($routes) {
		$routes->add('', 'Management::tanggal');
		$routes->add('add', 'Management::tanggal_form');	
		$routes->add('(:num)/edit', 'Management::tanggal_form/$1');
		$routes->add('(:num)/delete', 'Management::tanggal_form/$1');
	});

	$routes->group('jadwal', function($routes) {
		$routes->add('', 'Management::jadwal');
		$routes->add('add', 'Management::jadwal_form');	
		$routes->add('(:num)/edit', 'Management::jadwal_form/$1');
		$routes->add('(:num)/delete', 'Management::jadwal_form/$1');
	});

	$routes->group('ruangan', function($routes) {
		$routes->add('', 'Management::ruangan');
		$routes->add('add', 'Management::ruangan_form');	
		$routes->add('(:num)/edit', 'Management::ruangan_form/$1');
		$routes->add('(:num)/delete', 'Management::ruangan_form/$1');
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
