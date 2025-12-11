<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\MaterialModel;

class Student extends BaseController
{
    protected $materialModel;

    protected $courseModel;
    protected $enrollmentModel;

    public function __construct()
    {
        // Load models first
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->materialModel = new MaterialModel();
        helper(['url', 'form', 'file']);
       
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

        // Rest of the constructor remains the same
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
    public function viewCourse($courseId = null)
{
    try {
        $studentId = session()->get('userID');
        log_message('debug', "Viewing course $courseId for student $studentId");

        // Check if user is enrolled in this course
        $enrollment = $this->enrollmentModel->where([
            'student_id' => $studentId,
            'course_id' => $courseId
        ])->first();

        if (!$enrollment) {
            log_message('error', "Student $studentId not enrolled in course $courseId");
            return redirect()->to('/student/dashboard')->with('error', 'You are not enrolled in this course');
        }

        // Get course details
        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return redirect()->back()->with('error', 'Course not found');
        }

        // Get materials for this course
        $materials = $this->materialModel->where('course_id', $courseId)
                                       ->orderBy('created_at', 'DESC')
                                       ->findAll();

        $data = [
            'title' => 'Course Materials - ' . $course['course_name'],
            'course' => $course,
            'materials' => $materials
        ];

        // Debug output
        log_message('debug', 'Materials data: ' . print_r([
            'course_id' => $courseId,
            'materials_count' => count($materials),
            'materials' => $materials
        ], true));

        return view('student/materials', $data);

    } catch (\Exception $e) {
        log_message('error', 'Error in viewCourse: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        return redirect()->back()->with('error', 'An error occurred while loading course materials');
    }
}
public function view($courseId = null)
{
    try {
        // Debug
        log_message('debug', 'Student::view() called with courseId: ' . $courseId);
        
        if (!$courseId) {
            return redirect()->to('/student/dashboard')->with('error', 'Course ID is required');
        }

        // Load course details
        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return redirect()->to('/student/dashboard')->with('error', 'Course not found');
        }

        // Check enrollment
        $enrollment = $this->enrollmentModel
            ->where('user_id', session()->get('userID'))
            ->where('course_id', $courseId)
            ->first();

        if (!$enrollment) {
            return redirect()->to('/student/dashboard')->with('error', 'You are not enrolled in this course');
        }

        // Load materials with proper path
        $materials = $this->materialModel
            ->where('course_id', $courseId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Debug materials
        log_message('debug', 'Materials found: ' . print_r($materials, true));

        // Prepare data
        $data = [
            'title' => $course['title'] . ' - Course Details',
            'course' => $course,
            'materials' => $materials,
            'isEnrolled' => true,
            'progress' => $enrollment['progress'] ?? 0
        ];

        return view('student/course/view', $data);

    } catch (\Exception $e) {
        log_message('error', 'Error in Student::view - ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        return redirect()->back()->with('error', 'An error occurred while loading course details');
    }
}
}