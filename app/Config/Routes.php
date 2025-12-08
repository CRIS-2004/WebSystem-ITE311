<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Nav Bars
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Auth Routes
$routes->group('', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/register', 'Auth::register');     
    $routes->post('/register', 'Auth::register'); 
    $routes->get('/login', 'Auth::login');           
    $routes->post('/login', 'Auth::login');         
    $routes->get('/logout', 'Auth::logout');
});

// Admin Routes with proper grouping and method types
$routes->group('admin', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Admin::dashboard');
    
    // User Management
     $routes->group('users', function($routes) {
        $routes->get('', 'Admin::users', ['as' => 'admin.users']);
        $routes->get('new', 'Admin::newUser', ['as' => 'admin.users.new']);
        $routes->post('store', 'Admin::storeUser', ['as' => 'admin.users.store']);
        $routes->get('edit/(:num)', 'Admin::editUser/$1', ['as' => 'admin.users.edit']);
        $routes->match(['put', 'post'], 'update/(:num)', 'Admin::updateUser/$1', ['as' => 'admin.users.update']);
        $routes->get('delete/(:num)', 'Admin::deleteUser/$1', ['as' => 'admin.users.delete']);
    });
});

// Teacher Routes
$routes->group('teacher', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
});

// Student Routes
$routes->group('student', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Student::dashboard');
});

// Default Dashboard (for backward compatibility)
$routes->get('dashboard', 'Auth::dashboard', ['filter' => 'auth']);



