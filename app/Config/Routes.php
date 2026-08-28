<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->get('myclientlogin', 'ClientHome::index');
$routes->add('mylogin-auth', 'MyLogIn::auth');
$routes->add('mylogout', 'MyLogIn::logout');
$routes->get('myadmindashboard', 'MyAdminDashboard::index',['filter' => 'myauthuser']);
$routes->add('myclientlogout', 'MyClientDashboard::logout');

// =============================================
// TRANSACTIONS ROUTES
// =============================================
$routes->get('transactions', 'TransactionsController::index', ['filter' => 'myauthuser']);
$routes->post('transactions', 'TransactionsController::index', ['filter' => 'myauthuser']);

// =============================================
// RANGE ASSISTANTS ROUTES
// =============================================
$routes->get('rangeassistants', 'RangeAssistantsController::index', ['filter' => 'myauthuser']);
$routes->post('rangeassistants', 'RangeAssistantsController::index', ['filter' => 'myauthuser']);

// =============================================
// BAY STATUS ROUTES
// =============================================
$routes->get('baystatus', 'BayStatusController::index', ['filter' => 'myauthuser']);
$routes->post('baystatus', 'BayStatusController::index', ['filter' => 'myauthuser']);

// =============================================
// RANGE REPORTS ROUTES
// =============================================
$routes->get('rangereports', 'RangeReportsController::index', ['filter' => 'myauthuser']);
$routes->post('rangereports', 'RangeReportsController::index', ['filter' => 'myauthuser']);