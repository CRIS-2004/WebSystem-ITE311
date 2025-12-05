<?php

namespace App\Controllers;

class Student extends BaseController
{
    public function __construct()
    {
        // Debug session data
        $session = session();
        $sessionData = $session->get();
        log_message('debug', 'Session data in Student controller: ' . print_r($sessionData, true));
        
        // Check if user is logged in and has the correct role (case-insensitive)
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'student') {
            log_message('debug', 'Access denied - isLoggedIn: ' . ($session->get('isLoggedIn') ? 'true' : 'false') . 
                       ', role: ' . $session->get('role') . 
                       ', expected role: student');
            return redirect()->to('/login')->with('error', 'Access denied. Please log in as a student.')->send();
        }
    }

    public function dashboard()
    {
        $data = [
            'title' => 'Student Dashboard',
            'user' => [
                'name' => session()->get('name'),
                'email' => session()->get('email'),
                'role' => session()->get('role')
            ]
        ];
        return view('student/dashboard', $data);
    }
}