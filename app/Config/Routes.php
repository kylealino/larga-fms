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

// =============================================
// PREVENTIVE MAINTENANCE
// =============================================
$routes->get('fms-maintenance', 'FMS_Maintenance::index', ['filter' => 'myauthuser']);
$routes->post('fms-maintenance', 'FMS_Maintenance::index', ['filter' => 'myauthuser']);

// =============================================
// TIRE MANAGEMENT
// =============================================
$routes->get('fms-tire', 'FMS_Tire::index', ['filter' => 'myauthuser']);
$routes->post('fms-tire', 'FMS_Tire::index', ['filter' => 'myauthuser']);

// =============================================
// SUPPLIES MANAGEMENT
// =============================================
$routes->get('fms-supply', 'FMS_Supply::index', ['filter' => 'myauthuser']);
$routes->post('fms-supply', 'FMS_Supply::index', ['filter' => 'myauthuser']);

// =============================================
// TOOL MANAGEMENT
// =============================================
$routes->get('fms-tool', 'FMS_Tool::index', ['filter' => 'myauthuser']);
$routes->post('fms-tool', 'FMS_Tool::index', ['filter' => 'myauthuser']);

// =============================================
// BILLING GENERATION
// =============================================
$routes->get('billing', 'FMS_Billing::index', ['filter' => 'myauthuser']);
$routes->post('billing', 'FMS_Billing::index', ['filter' => 'myauthuser']);

// =============================================
// INVOICE GENERATION
// =============================================
$routes->get('invoice', 'FMS_Invoice::index', ['filter' => 'myauthuser']);
$routes->post('invoice', 'FMS_Invoice::index', ['filter' => 'myauthuser']);

// =============================================
// PAYMENT RECORDING
// =============================================
$routes->get('payment', 'FMS_Payment::index', ['filter' => 'myauthuser']);
$routes->post('payment', 'FMS_Payment::index', ['filter' => 'myauthuser']);

// =============================================
// ACCOUNTS RECEIVABLE
// =============================================
$routes->get('accountsreceivable', 'FMS_AR::index', ['filter' => 'myauthuser']);
$routes->post('accountsreceivable', 'FMS_AR::index', ['filter' => 'myauthuser']);

// =============================================
// STATEMENT OF ACCOUNT
// =============================================
$routes->get('statementofaccount', 'FMS_SOA::index', ['filter' => 'myauthuser']);
$routes->post('statementofaccount', 'FMS_SOA::index', ['filter' => 'myauthuser']);

// =============================================
// OPERATIONS REPORTS
// =============================================
$routes->get('operationsreports', 'FMS_OperationsReports::index', ['filter' => 'myauthuser']);
$routes->post('operationsreports', 'FMS_OperationsReports::index', ['filter' => 'myauthuser']);

// =============================================
// DELIVERY REPORTS
// =============================================
$routes->get('deliveryreports', 'FMS_DeliveryReports::index', ['filter' => 'myauthuser']);
$routes->post('deliveryreports', 'FMS_DeliveryReports::index', ['filter' => 'myauthuser']);

// =============================================
// BILLING REPORTS
// =============================================
$routes->get('billingreports', 'FMS_BillingReports::index', ['filter' => 'myauthuser']);
$routes->post('billingreports', 'FMS_BillingReports::index', ['filter' => 'myauthuser']);

// =============================================
// MAINTENANCE REPORTS
// =============================================
$routes->get('maintenancereports', 'FMS_MaintenanceReports::index', ['filter' => 'myauthuser']);
$routes->post('maintenancereports', 'FMS_MaintenanceReports::index', ['filter' => 'myauthuser']);