<?php

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Set default controller and method
$routes->setDefaultController('AuthController');
$routes->setDefaultMethod('index');
$routes->set404Override('App\Controllers\Error404::index');

/*
| --------------------------------------------------------------------
| ADMIN ROUTES
| --------------------------------------------------------------------
*/
$routes->get('pradeep', 'HomeController::pradeep');
// Home or login page
$routes->get('/', 'AuthController::index');
// Login page
$routes->get('login', 'AuthController::index');
// Verify login credentials
$routes->post('verify-login', 'AuthController::verifyLogin');
// OTP page
$routes->get('verify-otp', 'AuthController::otpForm');
// Resent otp
$routes->get('resend-otp', 'AuthController::resendOtp');
// Verify otp
$routes->post('verify-otp', 'AuthController::verifyOtp');
// Logout (auth filter)
$routes->get('logout', 'AuthController::logout', ['filter' => 'auth']);
// User profile (auth filter)
$routes->get('profile', 'AuthController::profile', ['filter' => 'auth']);
// Edit profile form
$routes->get('edit-profile', 'AuthController::editProfile', ['filter' => 'auth']);
// Update profile
$routes->post('update-profile', 'AuthController::updateProfile', ['filter' => 'auth']);
// Dashboard (auth filter)
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
// Change password (auth filter)
$routes->get('change-password', 'AuthController::changePassword', ['filter' => 'auth']);
// Update password (auth filter)
$routes->post('update-password', 'AuthController::UpdatePassword', ['filter' => 'auth']);

$routes->group('users', ['filter' => 'auth'], function ($routes) {

    $routes->get('/', 'UsersController::index');
    $routes->get('add', 'UsersController::create');
    $routes->post('save', 'UsersController::save');
    $routes->get('edit/(:num)', 'UsersController::edit/$1');
    $routes->post('delete', 'UsersController::delete');
    $routes->get('preview/(:num)', 'UsersController::showDetails/$1');
});

$routes->group('wallets', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'WalletController::index');
    $routes->get('preview/(:num)', 'WalletController::preview/$1');
    $routes->get('delete/(:num)', 'WalletController::delete/$1');
});

// Wallet Transactions Routes
$routes->group('wallet-transactions', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'TransactionController::index');
    $routes->get('view/(:num)', 'TransactionController::view/$1');
});

// Notifications Routes
$routes->group('notifications', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'NotificationController::index');
    $routes->get('view/(:num)', 'NotificationController::view/$1');
    $routes->get('mark-read/(:num)', 'NotificationController::markRead/$1');
    $routes->get('delete/(:num)', 'NotificationController::delete/$1');
});

$routes->group('companies', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'CompanyController::index');
    $routes->get('create', 'CompanyController::create');
    $routes->post('save', 'CompanyController::store');
    $routes->get('edit/(:num)', 'CompanyController::edit/$1');
    $routes->get('preview/(:num)', 'CompanyController::preview/$1');
    $routes->post('delete', 'CompanyController::delete');
    $routes->post('update-status/(:num)', 'CompanyController::updateStatus/$1');
    $routes->post('get-managers', 'CompanyController::getManagersByCompany');
});

$routes->group('employees', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'EmployeeController::index');
    $routes->get('create', 'EmployeeController::create');
    $routes->post('save', 'EmployeeController::store');
    $routes->get('edit/(:num)', 'EmployeeController::edit/$1');
    $routes->get('preview/(:num)', 'EmployeeController::preview/$1');
    $routes->post('delete', 'EmployeeController::delete');
    $routes->get('attendance-calendar', 'EmployeeController::getAttendance');
    $routes->get('get-employees-by-company/(:num)', 'EmployeeController::getEmployeesByCompany/$1');
});

$routes->group('employee-attendance', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'AttendanceController::index');
    $routes->get('view/(:num)', 'AttendanceController::getEmployeeAttendance/$1');
});

$routes->get('employee-locations', 'TrackingController::index');
$routes->get('employee-locations/view/(:num)', 'TrackingController::getEmployeeLocation/$1');
$routes->post('employee-locations/delete', 'TrackingController::delete');

$routes->group('employee-tasks', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'TaskController::index');
    $routes->get('create', 'TaskController::create');
    $routes->get('edit/(:num)', 'TaskController::edit/$1');
    $routes->post('save', 'TaskController::save');
    $routes->get('preview/(:num)', 'TaskController::preview/$1');
    $routes->post('delete', 'TaskController::delete');
    $routes->post('update-status', 'TaskController::updateStatus');
});

$routes->group('salary', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'SalaryController::index');
    $routes->get('create', 'SalaryController::create');
    $routes->post('store', 'SalaryController::store');
    $routes->get('edit/(:num)', 'SalaryController::edit/$1');
    $routes->post('delete', 'SalaryController::delete');
    $routes->get('slip/preview/(:num)', 'SalaryController::preview/$1');
    $routes->get('slip/download/(:num)', 'SalaryController::download/$1');
});

$routes->group('settings', ['filter' => 'auth'], function ($routes) {

    $routes->get('company-info', 'CompanyInformationController::index');
    $routes->post('company/save', 'CompanyInformationController::save');
    
});

$routes->get('feedback', 'CommonController::feedbacks');
$routes->post('feedback/delete', 'CommonController::deleteFeedback');

$routes->group('request-letters', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'RequestLetterController::index');
    $routes->get('create', 'RequestLetterController::create');
    $routes->get('edit', 'RequestLetterController::edit');
    $routes->post('save', 'RequestLetterController::save');
    $routes->get('preview', 'RequestLetterController::preview');
    $routes->post('delete', 'RequestLetterController::delete');
});

$routes->group('api', ['namespace' => 'App\Controllers\API'], function ($routes) {

    $routes->get('companies', 'CompanyController::getCompanyList');
    $routes->get('companies/(:num)', 'CompanyController::getCompanyById/$1');
    $routes->put('companies/(:num)', 'CompanyController::updateCompanyDetails/$1');

    $routes->get('payment', 'PackageController::getPaymentUrl');
    $routes->get('payment/success', 'PackageController::success');
    $routes->get('payment/cancel', 'PackageController::cancel');
    $routes->get('payment/failure', 'PackageController::failure');

    // Create new location record
    $routes->post('assign-task', 'TaskController::assignTask');
    $routes->post('submit-task', 'TaskController::submitTask');
    $routes->get('get-assign-tasks', 'TaskController::getTasksByDate');
    $routes->get('get-juniors-employees', 'EmployeeController::getJuniorsEmployees');
    $routes->post('employee-locations', 'LocationController::create');
    $routes->get('employee-locations', 'LocationController::getEmployeeLocations');
    $routes->get('get-employee-last-locations', 'LocationController::getLastEmployeeLocation');
    $routes->post('employee/register-or-get', 'EmployeeController::registerOrGetEmployee');
    $routes->get('employee-locations/last/(:num)', 'LocationController::getLastEmployeeLocation/$1');
    $routes->get('payslip/serve/(:num)/(:num)/(:num)', 'PayslipController::servePayslip/$1/$2/$3');

    $routes->get('attendance', 'AttendanceController::getAttendance');
    $routes->get('attendance/summary', 'AttendanceController::getAttendanceSummary');
    $routes->get('employee-id-card', 'EmployeeController::getEmployeeIdCard');
    $routes->get('payslip/status', 'PayslipController::getPayslipStatus');
    $routes->post('payslip/generate', 'PayslipController::generatePayslip');
    $routes->get('employee-profile', 'EmployeeController::getProfileDetails');
    $routes->get('employees/geo-tracking', 'EmployeeController::getGeoTrackingStatus');
    $routes->post('employees/geo-tracking', 'EmployeeController::updateGeoTrackingStatus');
    $routes->get('employees/juniors', 'EmployeeController::getJuniorsEmployees');

    $routes->get('employee/get-status', 'EmployeeController::getEmployeeOnlineStatus');
    $routes->post('employee/update-status', 'EmployeeController::updateEmployeeOnlineStatus');

    $routes->get('get/available/balance', 'RechargeController::checkBalance');
    $routes->post('recharge', 'RechargeController::createRecharge');
    $routes->get('recharge/status', 'RechargeController::checkRechargeStatus');

    $routes->get('recharge/complaint', 'RechargeController::raiseComplaint');
    $routes->get('recharge/callback', 'RechargeController::rechargeCallback');
    $routes->get('recharge/complaint-callback', 'RechargeController::complaintCallback');

    $routes->post('createRequestLetter', 'RequestLetterController::createRequestLetter');
    $routes->get('getRequestLetterHistory', 'RequestLetterController::getRequestLetterHistory');
    $routes->get('employees/details', 'EmployeeController::getEmployeeByCode/$1');
    $routes->get('get-reporting-managers', 'ManagerController::getReportingManagersByCompany');

    // Punch in
    $routes->post('punch-in', 'AttendanceController::punchIn');
    // Punch out
    $routes->post('punch-out', 'AttendanceController::punchOut');
    // Get today's attendance status
    $routes->get('attendance-status', 'AttendanceController::getAttendanceStatus');
});
