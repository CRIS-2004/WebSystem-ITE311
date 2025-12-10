<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\UserModel;

class Teacher extends BaseController
{
    protected $courseModel;
    protected $userModel;

     public function __construct()
    {
        // Load the models first
        $this->courseModel = new \App\Models\CourseModel();
        $this->userModel = new \App\Models\UserModel();
        
        // Then check authentication
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            return redirect()->to('/login');
        }
        
        helper(['form', 'url']);
    }

    public function dashboard()
    {
        try {
            $teacherId = session()->get('userID');
            
            $data = [
                'title' => 'Teacher Dashboard',
                'courses' => $this->courseModel->where('course_instructor', $teacherId)->findAll(),
                'user' => [
                    'name' => session()->get('name'),
                    'email' => session()->get('email')
                ]
            ];
            
            return view('teacher/dashboard', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error in Teacher dashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading the dashboard');
        }
    }
    
    public function createCourse()
    {
        $data = [
            'title' => 'Create New Course',
            'validation' => \Config\Services::validation()
        ];
        
        return view('teacher/courses/create', $data);
    }
    
    public function storeCourse()
    {
        $rules = [
            'course_code' => 'required|min_length[3]|max_length[50]|is_unique[courses.course_code]',
            'course_name' => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]',
            'cn_number' => 'permit_empty|max_length[50]',
            'schedule_date' => 'permit_empty|valid_date',
            'schedule_time' => 'permit_empty',
            'room' => 'permit_empty|max_length[100]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'course_code' => $this->request->getPost('course_code'),
            'cn_number' => $this->request->getPost('cn_number'),
            'course_name' => $this->request->getPost('course_name'),
            'description' => $this->request->getPost('description'),
            'schedule_date' => $this->request->getPost('schedule_date'),
            'schedule_time' => $this->request->getPost('schedule_time'),
            'room' => $this->request->getPost('room'),
            'course_instructor' => session()->get('userID'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->courseModel->save($data)) {
            return redirect()->to('/teacher/dashboard')->with('message', 'Course created successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create course');
        }
    }
    
    public function editCourse($id = null)
    {
        $course = $this->courseModel->find($id);
        
        if (!$course || $course['course_instructor'] != session()->get('userID')) {
            return redirect()->to('/teacher/dashboard')->with('error', 'Course not found or access denied');
        }
        
        $data = [
            'title' => 'Edit Course',
            'course' => $course,
            'validation' => \Config\Services::validation()
        ];
        
        return view('teacher/courses/edit', $data);
    }
    
    public function updateCourse($id = null)
    {
        $course = $this->courseModel->find($id);
        
        if (!$course || $course['course_instructor'] != session()->get('userID')) {
            return redirect()->to('/teacher/dashboard')->with('error', 'Course not found or access denied');
        }
        
        $rules = [
            'course_code' => 'required|min_length[3]|max_length[50]',
            'course_name' => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]',
            'cn_number' => 'permit_empty|max_length[50]',
            'schedule_date' => 'permit_empty|valid_date',
            'schedule_time' => 'permit_empty',
            'room' => 'permit_empty|max_length[100]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'course_id' => $id,
            'course_code' => $this->request->getPost('course_code'),
            'cn_number' => $this->request->getPost('cn_number'),
            'course_name' => $this->request->getPost('course_name'),
            'description' => $this->request->getPost('description'),
            'schedule_date' => $this->request->getPost('schedule_date'),
            'schedule_time' => $this->request->getPost('schedule_time'),
            'room' => $this->request->getPost('room'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->courseModel->save($data)) {
            return redirect()->to('/teacher/dashboard')->with('message', 'Course updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update course');
        }
    }
    
    public function deleteCourse($id = null)
    {
        $course = $this->courseModel->find($id);
        
        if (!$course || $course['course_instructor'] != session()->get('userID')) {
            return redirect()->to('/teacher/dashboard')->with('error', 'Course not found or access denied');
        }
        
        if ($this->courseModel->delete($id)) {
            return redirect()->to('/teacher/dashboard')->with('message', 'Course deleted successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to delete course');
        }
    }
    public function students($courseId)
    {
        // Get database connection
        $db = \Config\Database::connect();
        
        // Get course details
        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return redirect()->back()->with('error', 'Course not found');
        }

        // Get all students not yet enrolled
        $enrolledUserIds = $db->table('enrollments')
            ->where('course_id', $courseId)
            ->get()
            ->getResultArray();
        
        $enrolledIds = array_column($enrolledUserIds, 'user_id');

        $availableStudents = $db->table('users')
            ->where('role', 'student');
            
        if (!empty($enrolledIds)) {
            $availableStudents->whereNotIn('id', $enrolledIds);
        }
        
        $availableStudents = $availableStudents->get()->getResultArray();

        // Get enrolled students with their details
        $enrolledStudents = $db->table('enrollments')
            ->select('enrollments.*, users.id, users.name, users.email, enrollments.enrollment_date as enrolled_at')
            ->join('users', 'users.id = enrollments.user_id')
            ->where('enrollments.course_id', $courseId)
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Manage Students - ' . $course['course_name'],
            'course' => $course,
            'availableStudents' => $availableStudents,
            'enrolledStudents' => $enrolledStudents
        ];

        return view('teacher/courses/students', $data);
    }

    public function manageStudents($courseId)
    {
        // Get database connection
        $db = \Config\Database::connect();
        
        // Get course details
        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return redirect()->back()->with('error', 'Course not found');
        }

        // Get all students not yet enrolled
        $enrolledUserIds = $db->table('enrollments')
            ->where('course_id', $courseId)
            ->get()
            ->getResultArray();
        
        $enrolledIds = array_column($enrolledUserIds, 'user_id');

        $availableStudents = $db->table('users')
            ->where('role', 'student');
            
        if (!empty($enrolledIds)) {
            $availableStudents->whereNotIn('id', $enrolledIds);
        }
        
        $availableStudents = $availableStudents->get()->getResultArray();

        // Get enrolled students with their details
        $enrolledStudents = $db->table('enrollments')
            ->select('enrollments.*, users.id, users.name, users.email, enrollments.enrollment_date as enrolled_at')
            ->select('users.id as student_id')  // Add this line to include student_id
            ->join('users', 'users.id = enrollments.user_id')
            ->where('enrollments.course_id', $courseId)
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Manage Students',
            'course' => $course,
            'availableStudents' => $availableStudents,
            'enrolledStudents' => $enrolledStudents
        ];

        return view('teacher/courses/students', $data);
    }

    public function addStudent($courseId, $studentId)
    {
        try {
            // Get the database connection
            $db = \Config\Database::connect();
            
            // Check if already enrolled
            $isEnrolled = $db->table('enrollments')
                ->where('course_id', $courseId)
                ->where('user_id', $studentId)
                ->countAllResults() > 0;

            if ($isEnrolled) {
                return redirect()->back()->with('error', 'Student is already enrolled in this course');
            }

            // Add enrollment
            $enrollmentData = [
                'course_id' => $courseId,
                'user_id' => $studentId,
                'enrollment_date' => date('Y-m-d H:i:s')
            ];
            
            $db->table('enrollments')->insert($enrollmentData);

            return redirect()->back()->with('success', 'Student added to course successfully');
        } catch (\Exception $e) {
            log_message('error', 'Error adding student to course: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add student to course: ' . $e->getMessage());
        }
    }

    public function removeStudent($courseId, $studentId)
    {
        try {
            $db = \Config\Database::connect();
            $db->table('enrollments')
                ->where('course_id', $courseId)
                ->where('user_id', $studentId)
                ->delete();

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Student removed from course successfully'
                ]);
            }

            return redirect()->back()->with('success', 'Student removed from course successfully');
        } catch (\Exception $e) {
            log_message('error', 'Error removing student: ' . $e->getMessage());
            
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to remove student: ' . $e->getMessage()
                ]);
            }

            return redirect()->back()->with('error', 'Failed to remove student: ' . $e->getMessage());
        }
    }
}
