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
    // app/Config/Routes.php
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Admin Dashboard
    $routes->get('admin/dashboard', 'Admin::dashboard');
    
    // Teacher Dashboard
    $routes->get('teacher/dashboard', 'Teacher::dashboard');
    
    // Student Dashboard
    $routes->get('student/dashboard', 'Student::dashboard');
    
    // Default Dashboard (for backward compatibility)
    $routes->get('dashboard', 'Auth::dashboard');
});



