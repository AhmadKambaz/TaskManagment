<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

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
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->post('/auth/register', 'User::register');
$routes->post('/auth/login', 'User::login');
$routes->get('/auth/logout', 'User::logout');
$routes->group('', ['filter' => 'jwtauth'], function ($routes) {
    //$routes->group('tasks', function($routes) {
        $routes->get('/tasks', 'TaskController::index');
        $routes->get('/tasks/(:num)', 'TaskController::show/$1');
        $routes->post('/tasks', 'TaskController::create');
        $routes->put('/tasks/(:num)', 'TaskController::update/$1');
        $routes->patch('/tasks/(:num)', 'TaskController::update/$1'); 
        $routes->delete('/tasks/(:num)', 'TaskController::delete/$1');
   });
//});


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
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
