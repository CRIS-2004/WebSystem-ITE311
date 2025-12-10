<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class Student extends BaseController
{
    protected $courseModel;
    protected $enrollmentModel;

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

        // Load models
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    public function dashboard()
    {
        $userId = session()->get('userID');
        
        
        // Get all available courses with instructor names
        $db = \Config\Database::connect();
        $availableCourses = $db->table('courses')
            ->select('courses.*, users.name as instructor_name')
            ->join('users', 'users.id = courses.course_instructor', 'left')
            ->get()
            ->getResultArray();

        // Get enrolled courses
        $enrolledCourses = $this->enrollmentModel->getUserEnrollments($userId);

        // Prepare data for the view
        $data = [
            'title' => 'Student Dashboard',
            'user' => [
                'name' => session()->get('name'),
                'email' => session()->get('email'),
                'role' => session()->get('role'),
                'id' => $userId
            ],
            'availableCourses' => $availableCourses,
            'enrolledCourses' => $enrolledCourses
        ];
        
        return view('student/dashboard', $data);
    }
}