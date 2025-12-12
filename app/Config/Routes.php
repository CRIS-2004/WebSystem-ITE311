<?php

use CodeIgniter\Router\RouteCollection;
$routes->get('debug/student/course/(:num)/materials', 'Student::viewCourse/$1');

// Course Search Routes
$routes->get('courses/search', 'Course::search');
$routes->post('courses/search', 'Course::search');
$routes->get('courses', 'Course::index');
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
    // Materials Management
    $routes->get('materials/download/(:num)', 'Materials::download/$1', ['as' => 'admin.materials.download']);
    $routes->post('courses/(:num)/materials/delete/(:num)', 'Materials::delete/$1/$2', ['as' => 'admin.courses.materials.delete']);
    // Course Management
    $routes->get('courses', 'Admin::courses');
    $routes->get('courses/view/(:num)', 'Admin::viewCourse/$1');
    $routes->get('courses/edit/(:num)', 'Admin::editCourse/$1');
    $routes->post('courses/update/(:num)', 'Admin::updateCourse/$1');
    $routes->get('courses/create', 'Admin::createCourse');
    $routes->post('courses/store', 'Admin::storeCourse');
    $routes->post('courses/add-student/(:num)', 'Admin::addStudent/$1');
    $routes->delete('courses/remove-student/(:num)/(:num)', 'Admin::removeStudent/$1/$2');
    $routes->delete('courses/delete/(:num)', 'Admin::deleteCourse/$1');
    $routes->post('courses/(:num)/materials/upload', 'Materials::upload/$1', ['as' => 'admin.courses.materials.upload']);
    $routes->post('courses/(:num)/materials/delete/(:num)', 'Materials::delete/$1/$2', ['as' => 'admin.courses.materials.delete']);
    $routes->delete('courses/(:num)/materials/delete/(:num)', 'Materials::delete/$1/$2', ['as' => 'admin.courses.materials.delete']);
    
    // Course Materials
    $routes->get('courses/(:num)/materials', 'Materials::view/$1', ['as' => 'admin.courses.materials.view']);
    
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
     $routes->post('courses/materials/upload/(:num)', 'Teacher::uploadMaterial/$1', ['as' => 'teacher.courses.materials.upload']);
        
    
    // Course Management
    $routes->group('courses', function($routes) {
        $routes->get('create', 'Teacher::createCourse');
        $routes->post('store', 'Teacher::storeCourse');
        $routes->get('edit/(:num)', 'Teacher::editCourse/$1');
        $routes->match(['put', 'post'], 'update/(:num)', 'Teacher::updateCourse/$1');
        $routes->post('delete/(:num)', 'Teacher::deleteCourse/$1');
        $routes->post('courses/materials/upload/(:num)', 'Teacher::uploadMaterial/$1', ['as' => 'teacher.courses.materials.upload']);
        
        // Student Management
        $routes->get('students/(:num)', 'Teacher::students/$1', ['as' => 'teacher.courses.students']);
        $routes->post('students/add/(:num)/(:num)', 'Teacher::addStudent/$1/$2', ['as' => 'teacher.courses.students.add']);
        $routes->match(['delete', 'post'], 'students/remove/(:num)/(:num)', 'Teacher::removeStudent/$1/$2', ['as' => 'teacher.courses.students.remove']);
        
        // Materials Management
        $routes->get('(:num)/materials', 'Teacher::viewMaterials/$1', ['as' => 'teacher.courses.materials.view']);
        $routes->post('materials/upload/(:num)', 'Teacher::uploadMaterial/$1', ['as' => 'teacher.courses.materials.upload']);
        $routes->delete('materials/delete/(:num)', 'Teacher::deleteMaterial/$1', ['as' => 'teacher.materials.delete']);
    });
});

// Student 
$routes->group('student', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Student::dashboard');

    $routes->get('courses/(:num)', 'Student::viewCourse/$1', ['as' => 'student.courses.view']);
    $routes->get('course/(:num)/materials', 'Student::viewCourse/$1', ['as' => 'student.course.materials']);
    
    $routes->get('materials/download/(:num)', 'Materials::download/$1', ['as' => 'student.materials.download']);
    $routes->get('course/view/(:num)', 'Student::view/$1', ['as' => 'student.course.view']);
    
    $routes->get('materials/(:num)', 'Student::viewCourse/$1', ['as' => 'student.materials']);
});

//  Dashboard 
$routes->get('dashboard', 'Auth::dashboard', ['filter' => 'auth']);

// Course 
$routes->group('', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    $routes->post('course/enroll', 'Course::enroll');
    $routes->get('course/view/(:num)', 'Course::view/$1', ['as' => 'course.view']);
    $routes->get('course/(:num)', 'Course::view/$1');
   $routes->get('materials/download/(:num)', 'Materials::download/$1', ['filter' => 'auth']);
    $routes->get('course/view/(:num)', 'Student::view/$1', ['as' => 'student.course.view']);
});

// Public materials view route (accessible to all authenticated users)
$routes->group('', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    $routes->get('materials/download/(:num)', 'Materials::download/$1', ['as' => 'materials.download']);
    $routes->get('courses/(:num)/materials', 'Materials::view/$1', ['as' => 'materials.view']);
    $routes->get('direct-upload', 'Materials::directUpload', ['as' => 'materials.direct.upload']);
    $routes->post('direct-upload', 'Materials::handleDirectUpload');
});

// Notification routes
$routes->group('', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    $routes->group('notifications', function($routes) {
        $routes->get('', 'Notifications::get');
        $routes->post('mark_read/(:num)', 'Notifications::mark_as_read/$1');
        $routes->post('mark_all_read', 'Notifications::mark_all_read');
        $routes->get('test', 'Notifications::test');
    });
    
    // Test route - can be removed in production
    $routes->get('test-notifications', function() {
        $notificationModel = new \App\Models\NotificationModel();
        $userId = session('user_id') ?? 1; // Test with user ID 1 or your test user
        
        $notifications = $notificationModel->where('user_id', $userId)->findAll();
        
        echo '<h1>Test Notifications</h1>';
        echo '<pre>' . print_r($notifications, true) . '</pre>';
        echo '<p><a href="/">Back to site</a></p>';
    });
});