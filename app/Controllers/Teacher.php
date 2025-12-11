<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\UserModel;

class Teacher extends BaseController
{
    protected $courseModel;
    protected $userModel;
    protected $materialModel;


     public function __construct()
{
    $this->materialModel = new \App\Models\MaterialModel();
    $this->courseModel = new \App\Models\CourseModel();
    // ... other model initializations
    helper(['form', 'url', 'file']);
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
        $db = \Config\Database::connect();
        helper('notification');
        
        $course = $db->table('courses')
            ->where('course_id', $courseId)
            ->get()
            ->getRowArray();
            
        $teacherName = session()->get('name') ?? 'Teacher';
        
        // Check if already enrolled
        $isEnrolled = $db->table('enrollments')
            ->where('course_id', $courseId)
            ->where('user_id', $studentId)
            ->countAllResults() > 0;

        if ($isEnrolled) {
            if ($course) {
                notify_duplicate_enrollment($studentId, $course['course_name']);
            }
            return redirect()->back()->with('error', 'Student is already enrolled in this course');
        }

        // Add enrollment
        $enrollmentData = [
            'course_id' => $courseId,
            'user_id' => $studentId,
            'enrollment_date' => date('Y-m-d H:i:s')
        ];
        
        $db->table('enrollments')->insert($enrollmentData);
        
        if ($course) {
            notify_student_enrollment($studentId, $course['course_name'], $teacherName);
        }

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
        helper('notification');
        
        $course = $db->table('courses')
            ->where('course_id', $courseId)
            ->get()
            ->getRowArray();
            
        $teacherName = session()->get('name') ?? 'Teacher';
        
        // Delete the enrollment
        $db->table('enrollments')
            ->where('course_id', $courseId)
            ->where('user_id', $studentId)
            ->delete();

        // Add notification for the student
        if ($course) {
            notify_enrollment_removal($studentId, $course['course_name'], $teacherName);
        }

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

    /**
     * View course materials
     */
    public function viewMaterials($courseId = null)
    {
        try {
            // Debug: Log the course ID
            log_message('debug', 'Viewing materials for course ID: ' . $courseId);
            
            // Load the course
            $course = $this->courseModel->find($courseId);
            
            // Debug: Log course data
            log_message('debug', 'Course data: ' . print_r($course, true));
            
            // Verify course exists and teacher is assigned to it
            if (!$course) {
                log_message('error', 'Course not found with ID: ' . $courseId);
                return redirect()->to('/teacher/dashboard')->with('error', 'Course not found');
            }
            
            if ($course['course_instructor'] != session()->get('userID')) {
                log_message('error', 'Access denied: User ' . session()->get('userID') . ' is not the instructor for course ' . $courseId);
                return redirect()->to('/teacher/dashboard')->with('error', 'Access denied');
            }
            
            // Get all materials for the course using the model's method
            log_message('debug', 'Attempting to get materials for course ID: ' . $courseId);
            $materials = $this->materialModel->getMaterialsByCourse($courseId);
            
            // Debug: Log materials data
            log_message('debug', 'Retrieved materials: ' . print_r($materials, true));
            
           $data = [
                'course' => $this->courseModel->find($courseId),
                'materials' => $this->materialModel->where('course_id', $courseId)->findAll()
            ];
            return view('teacher/materials', $data);
            
        } catch (\Exception $e) {
            $errorMessage = 'Error in viewMaterials: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
            log_message('error', $errorMessage);
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'An error occurred while loading course materials: ' . $e->getMessage());
        }
}

    /**
     * Handle file upload
     */
    public function uploadMaterial($courseId = null)
{
    try {
        $file = $this->request->getFile('material');
        
        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $uploadPath = 'uploads/materials/' . date('Y/m/d');
            
            // Create directory if it doesn't exist
            if (!is_dir(WRITEPATH . $uploadPath)) {
                mkdir(WRITEPATH . $uploadPath, 0777, true);
            }
            
            $file->move(WRITEPATH . $uploadPath, $newName);
            $filePath = $uploadPath . '/' . $newName;

            // Save to database
            $data = [
                'course_id' => $courseId,
                'file_name' => $file->getClientName(),
                'file_path' => $filePath,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->materialModel->insert($data);

            return redirect()->back()->with('success', 'File uploaded successfully');
        }
        
        return redirect()->back()->with('error', 'Failed to upload file');
        
    } catch (\Exception $e) {
        log_message('error', 'Upload error: ' . $e->getMessage());
        return redirect()->back()->with('error', $e->getMessage());
    }
}

    /**
     * Delete a material
     */
    public function deleteMaterial($materialId = null)
    {
        try {
            $material = $this->materialModel->find($materialId);
            
            if (!$material) {
                return redirect()->back()->with('error', 'Material not found');
            }

            $course = $this->courseModel->find($material['course_id']);
            
            // Verify course exists and teacher is assigned to it
            if (!$course || $course['course_instructor'] != session()->get('userID')) {
                return redirect()->back()->with('error', 'You are not authorized to delete this material');
            }

            $filePath = WRITEPATH . str_replace('writable/', '', $material['file_path']);
            
            // Delete the file if it exists
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete the database record
            $this->materialModel->delete($materialId);

            return redirect()->back()->with('success', 'Material deleted successfully');
        } catch (\Exception $e) {
            log_message('error', 'Error in deleteMaterial: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the material');
        }
    }
}
