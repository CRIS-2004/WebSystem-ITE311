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
        $searchTerm = $this->request->getGet('search_term');
        
        // Get all available courses with instructor names
        $db = \Config\Database::connect();
        $query = $db->table('courses')
            ->select('courses.*, users.name as instructor_name')
            ->join('users', 'users.id = courses.course_instructor', 'left');
            
        // Add search functionality if search term exists
        if (!empty($searchTerm)) {
            $query->groupStart()
                ->like('courses.course_name', $searchTerm)
                ->orLike('courses.course_code', $searchTerm)
                ->orLike('courses.description', $searchTerm)
                ->orLike('users.name', $searchTerm)
                ->groupEnd();
        }
        
        $availableCourses = $query->get()->getResultArray();

        // Get enrolled courses
        $enrolledCourses = $this->enrollmentModel->getUserEnrollments($userId);

        // Check if it's an AJAX request
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'availableCourses' => $availableCourses,
                'searchTerm' => $searchTerm
            ]);
        }

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
            'enrolledCourses' => $enrolledCourses,
            'searchTerm' => $searchTerm ?? ''
        ];
        
        return view('student/dashboard', $data);
    }
    public function viewCourse($courseId = null)
{
    try {
        log_message('debug', '1. viewCourse method called with courseId: ' . $courseId);
        
        if (!$courseId) {
            log_message('error', 'No course ID provided to viewCourse');
            return redirect()->to('/student/dashboard')->with('error', 'Course ID is required');
        }

        $studentId = session()->get('userID');
        log_message('debug', "2. Student ID from session: $studentId");

        // Get course details with instructor name
        $course = $this->courseModel->select('courses.*, users.name as instructor_name')
                                  ->join('users', 'users.id = courses.course_instructor')
                                  ->find($courseId);

        log_message('debug', '3. Course data: ' . print_r($course, true));

        if (!$course) {
            log_message('error', "4. Course not found: $courseId");
            return redirect()->to('/student/dashboard')->with('error', 'Course not found');
        }

        // Check if user is enrolled in this course
        $enrollment = $this->enrollmentModel->where([
            'student_id' => $studentId,
            'course_id' => $courseId
        ])->first();

        log_message('debug', '5. Enrollment data: ' . print_r($enrollment, true));

        if (!$enrollment) {
            log_message('error', "6. Student $studentId not enrolled in course $courseId");
            $isEnrolled = false;
            // Option 1: Redirect to dashboard with error
            // return redirect()->to('/student/dashboard')->with('error', 'You are not enrolled in this course');
            // Option 2: Continue but show a message in the view
        } else {
            $isEnrolled = true;
        }

        log_message('debug', "7. Is enrolled: " . ($isEnrolled ? 'Yes' : 'No'));

        // Get materials for this course
        $materials = $this->materialModel->where('course_id', $courseId)
                                       ->orderBy('created_at', 'DESC')
                                       ->findAll();

        log_message('debug', '8. Materials found: ' . count($materials));

        // Prepare data for the view
        $data = [
            'title' => 'Course Materials - ' . $course['course_name'],
            'course' => $course,
            'materials' => $materials,
            'materialsCount' => count($materials),
            'isEnrolled' => $isEnrolled,
            'progress' => $enrollment['progress'] ?? 0,
            'enrollment' => $enrollment ?? null
        ];

        // Debug output
        log_message('debug', '9. Final data being passed to view: ' . print_r([
            'course_id' => $courseId,
            'materials_count' => count($materials),
            'has_materials' => !empty($materials),
            'is_enrolled' => $isEnrolled
        ], true));

        // Debug: Check if view file exists
        $viewPath = APPPATH . 'Views/student/materials.php';
        log_message('debug', '10. View file exists: ' . (file_exists($viewPath) ? 'Yes' : 'No'));
        log_message('debug', '11. View path: ' . $viewPath);

        // Return the materials view
        return view('student/materials', $data);
        
    } catch (\Exception $e) {
        log_message('error', 'Error in Student::viewCourse - ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        return redirect()->to('/student/dashboard')->with('error', 'An error occurred while loading course materials');
    }
}
public function view($courseId = null)
{
    try {
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

        $materialsCount = count($materials);
        
        // Debug: Log the materials count
        log_message('debug', "Materials count for course {$courseId}: " . $materialsCount);
        log_message('debug', 'Course data: ' . print_r($course, true));
        
        // Debug: Check if materials were found
        log_message('debug', 'Materials found: ' . print_r($materials, true));

        // Load modules if they exist
        $modules = []; // Initialize empty modules array
        // Uncomment and modify the following if you have a modules model
        // if (isset($this->moduleModel)) {
        //     $modules = $this->moduleModel->where('course_id', $courseId)->findAll();
        // }

        // Prepare data - ensure all required variables are set
        $data = [
            'title' => $course['course_name'] . ' - Course Details',
            'course' => $course,
            'materials' => $materials,
            'isEnrolled' => true,
            'progress' => $enrollment['progress'] ?? 0,
            'materialsCount' => $materialsCount, // Ensure this is set
            'modules' => $modules, // Now properly initialized
            'enrollment' => $enrollment // Pass the full enrollment data
        ];
        
        // Debug the data being passed to the view
        log_message('debug', 'View data: ' . print_r([
            'materials_count' => $materialsCount,
            'has_materials' => !empty($materials),
            'course_id' => $courseId,
            'course_data_keys' => array_keys($course)
        ], true));

        return view('student/course/view', $data);

    } catch (\Exception $e) {
        log_message('error', 'Error in Student::view - ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        return redirect()->back()->with('error', 'An error occurred while loading course details');
    }
}
}