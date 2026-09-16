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
// CUSTOMERS ROUTES
// =============================================
$routes->get('fms-customers', 'FMS_Customer::index', ['filter' => 'myauthuser']);
$routes->post('fms-customers', 'FMS_Customer::index', ['filter' => 'myauthuser']);

// =============================================
// DRIVERS ROUTES
// =============================================
$routes->get('fms-drivers', 'FMS_Driver::index', ['filter' => 'myauthuser']);
$routes->post('fms-drivers', 'FMS_Driver::index', ['filter' => 'myauthuser']);

// =============================================
// HELPERS ROUTES
// =============================================
$routes->get('fms-helpers', 'FMS_Helper::index', ['filter' => 'myauthuser']);
$routes->post('fms-helpers', 'FMS_Helper::index', ['filter' => 'myauthuser']);

// =============================================
// TRUCK ROUTES
// =============================================
$routes->get('fms-trucks', 'FMS_Truck::index', ['filter' => 'myauthuser']);
$routes->post('fms-trucks', 'FMS_Truck::index', ['filter' => 'myauthuser']);

// =============================================
// VENDOR ROUTES
// =============================================
$routes->get('fms-vendors', 'FMS_Vendor::index', ['filter' => 'myauthuser']);
$routes->post('fms-vendors', 'FMS_Vendor::index', ['filter' => 'myauthuser']);

// =============================================
// TRIPS ROUTES
// =============================================
$routes->get('fms-trips', 'FMS_Trip::index', ['filter' => 'myauthuser']);
$routes->post('fms-trips', 'FMS_Trip::index', ['filter' => 'myauthuser']);

// =============================================
// DISPATCH ROUTES
// =============================================
$routes->get('fms-dispatch', 'FMS_Dispatch::index', ['filter' => 'myauthuser']);
$routes->post('fms-dispatch', 'FMS_Dispatch::index', ['filter' => 'myauthuser']);

// =============================================
// DELIVERY RECEIPT ROUTES
// =============================================
$routes->get('fms-delivery-receipt', 'FMS_DeliveryReceipt::index', ['filter' => 'myauthuser']);
$routes->post('fms-delivery-receipt', 'FMS_DeliveryReceipt::index', ['filter' => 'myauthuser']);