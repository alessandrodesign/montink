<?php

use CodeIgniter\Router\RouteCollection;

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('HomeController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

$routes->get('/', 'HomeController::index');
$routes->get('/about', 'HomeController::about');
$routes->get('/contact', 'HomeController::contact');

// Authentication routes
$routes->group('auth', ['namespace' => 'App\Controllers'], static function (RouteCollection $routes) {
    $routes->match(['get', 'post'], 'login', 'AuthController::login');
    $routes->match(['get', 'post'], 'register', 'AuthController::register');
    $routes->get('logout', 'AuthController::logout');
    $routes->match(['get', 'post'], 'profile', 'AuthController::profile');
    $routes->match(['get', 'post'], 'forgot-password', 'AuthController::forgotPassword');
    $routes->match(['get', 'post'], 'reset-password/(:segment)', 'AuthController::resetPassword/$1');
});

// Product routes
$routes->group('products', ['namespace' => 'App\Controllers'], static function (RouteCollection $routes) {
    $routes->get('/', 'ProductController::index');
    $routes->get('view/(:num)', 'ProductController::view/$1');
    $routes->match(['get', 'post'], 'create', 'ProductController::create', ['filter' => 'admin']);
    $routes->match(['get', 'post'], 'edit/(:num)', 'ProductController::edit/$1', ['filter' => 'admin']);
    $routes->get('delete/(:num)', 'ProductController::delete/$1', ['filter' => 'admin']);
    $routes->match(['get', 'post'], 'add-variation/(:num)', 'ProductController::addVariation/$1', ['filter' => 'admin']);
    $routes->match(['get', 'post'], 'edit-variation/(:num)', 'ProductController::editVariation/$1', ['filter' => 'admin']);
    $routes->get('delete-variation/(:num)', 'ProductController::deleteVariation/$1', ['filter' => 'admin']);
    $routes->match(['get', 'post'], 'update-stock/(:num)', 'ProductController::updateStock/$1', ['filter' => 'admin']);
    $routes->match(['get', 'post'], 'update-stock/(:num)/(:num)', 'ProductController::updateStock/$1/$2', ['filter' => 'admin']);
});

// Cart routes
$routes->group('cart', ['namespace' => 'App\Controllers'], static function (RouteCollection $routes) {
    $routes->get('/', 'CartController::index');
    $routes->post('add', 'CartController::add');
    $routes->post('update', 'CartController::update');
    $routes->get('remove/(:any)', 'CartController::remove/$1');
    $routes->get('clear', 'CartController::clear');
    $routes->post('apply-coupon', 'CartController::applyCoupon');
    $routes->get('remove-coupon', 'CartController::removeCoupon');
    $routes->match(['get', 'post'], 'checkout', 'CartController::checkout', ['filter' => 'auth']);
});

// Order routes
$routes->group('orders', ['namespace' => 'App\Controllers'], static function (RouteCollection $routes) {
    $routes->get('/', 'OrderController::index', ['filter' => 'auth']);
    $routes->get('view/(:any)', 'OrderController::view/$1', ['filter' => 'auth']);
    $routes->post('process', 'OrderController::process', ['filter' => 'auth']);
    $routes->get('success/(:any)', 'OrderController::success/$1', ['filter' => 'auth']);
    $routes->get('cancel/(:num)', 'OrderController::cancel/$1', ['filter' => 'auth']);
});

// Admin routes
$routes->group('admin', ['namespace' => 'App\Controllers'], static function (RouteCollection $routes) {
    $routes->get('/', 'AdminController::index', ['filter' => 'admin']);
    $routes->get('sales-report', 'AdminController::salesReport', ['filter' => 'admin']);
    $routes->get('product-sales-report', 'AdminController::productSalesReport', ['filter' => 'admin']);
    $routes->get('stock-report', 'AdminController::stockReport', ['filter' => 'admin']);
    $routes->get('low-stock-report', 'AdminController::lowStockReport', ['filter' => 'admin']);

    // Coupon routes
    $routes->group('coupons', ['namespace' => 'App\Controllers'], static function (RouteCollection $routes) {
        $routes->get('/', 'CouponController::index', ['filter' => 'admin']);
        $routes->match(['get', 'post'], 'create', 'CouponController::create', ['filter' => 'admin']);
        $routes->match(['get', 'post'], 'edit/(:num)', 'CouponController::edit/$1', ['filter' => 'admin']);
        $routes->get('delete/(:num)', 'CouponController::delete/$1', ['filter' => 'admin']);
    });

    // Order management routes
    $routes->group('orders', ['namespace' => 'App\Controllers'], static function (RouteCollection $routes) {
        $routes->get('/', 'OrderController::adminIndex', ['filter' => 'admin']);
        $routes->match(['get', 'post'], 'update-status/(:num)', 'OrderController::updateStatus/$1', ['filter' => 'admin']);
        $routes->match(['get', 'post'], 'update-payment-status/(:num)', 'OrderController::updatePaymentStatus/$1', ['filter' => 'admin']);
    });
});

// API routes
$routes->group('api', ['namespace' => 'App\Controllers'], static function (RouteCollection $routes) {
    $routes->post('login', 'ApiController::login');
    $routes->get('products', 'ApiController::getProducts');
    $routes->get('products/(:num)', 'ApiController::getProduct/$1');
    $routes->get('cart', 'ApiController::getCart');
    $routes->post('cart/add', 'ApiController::addToCart');
    $routes->post('cart/update', 'ApiController::updateCart');
    $routes->get('cart/remove/(:any)', 'ApiController::removeFromCart/$1');
    $routes->get('cart/clear', 'ApiController::clearCart');
    $routes->post('cart/apply-coupon', 'ApiController::applyCoupon');
    $routes->post('orders/create', 'ApiController::createOrder');
    $routes->get('orders', 'ApiController::getOrders');
    $routes->get('orders/(:any)', 'ApiController::getOrder/$1');
    $routes->get('orders/cancel/(:num)', 'ApiController::cancelOrder/$1');
});

$routes->post('webhook/payment', 'WebhookController::paymentNotification');

$routes->get('(:any)', 'HomeController::view/$1');