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

// Admin Routes 
$routes->group('admin', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('courses', 'Admin::courses');
    $routes->get('courses/view/(:num)', 'Admin::viewCourse/$1');
    $routes->get('courses/edit/(:num)', 'Admin::editCourse/$1');
    $routes->post('courses/update/(:num)', 'Admin::updateCourse/$1');
    $routes->get('courses/create', 'Admin::createCourse');
    $routes->post('courses/store', 'Admin::storeCourse');
    $routes->post('courses/add-student/(:num)', 'Admin::addStudent/$1');
    $routes->delete('courses/remove-student/(:num)/(:num)', 'Admin::removeStudent/$1/$2');
    $routes->delete('courses/delete/(:num)', 'Admin::deleteCourse/$1');
    
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

// Teacher 
$routes->group('teacher', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
    $routes->delete('courses/delete/(:num)', 'Teacher::deleteCourse/$1');
    
    // Course Management
    $routes->group('courses', function($routes) {
        $routes->get('create', 'Teacher::createCourse');
        $routes->post('store', 'Teacher::storeCourse');
        $routes->get('edit/(:num)', 'Teacher::editCourse/$1');
        $routes->match(['put', 'post'], 'update/(:num)', 'Teacher::updateCourse/$1');
        $routes->post('delete/(:num)', 'Teacher::deleteCourse/$1');
        
        // Student Management
        $routes->get('students/(:num)', 'Teacher::students/$1', ['as' => 'teacher.courses.students']);
        $routes->post('students/add/(:num)/(:num)', 'Teacher::addStudent/$1/$2', ['as' => 'teacher.courses.students.add']);
        $routes->match(['delete', 'post'], 'students/remove/(:num)/(:num)', 'Teacher::removeStudent/$1/$2', ['as' => 'teacher.courses.students.remove']);
    });
});

// Student 
$routes->group('student', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Student::dashboard');
});

//  Dashboard 
$routes->get('dashboard', 'Auth::dashboard', ['filter' => 'auth']);

// Course 
$routes->group('', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    $routes->post('course/enroll', 'Course::enroll');
    $routes->get('course/view/(:num)', 'Course::view/$1', ['as' => 'course.view']);
    $routes->get('course/(:num)', 'Course::view/$1');
});
