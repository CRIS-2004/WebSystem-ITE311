<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class Admin extends BaseController
{
    use ResponseTrait;
    
    protected $userModel;
    protected $validation;
    protected $db;

    public function __construct()
    {
        // Initialize services first
        $this->userModel = new \App\Models\UserModel();
        $this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect();
        
        // Debug: Log session data
        log_message('debug', 'Session data: ' . print_r(session()->get(), true));
        
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            log_message('debug', 'User not logged in, redirecting to login');
            header('Location: /login');
            exit();
        }
        
        // Check if user has admin role (case-insensitive)
        $userRole = strtolower(session()->get('role') ?? '');
        if ($userRole !== 'admin') {
            log_message('debug', 'User role is not admin. Current role: ' . session()->get('role'));
            header('Location: /login');
            exit();
        }
    }

    public function dashboard()
{
    $db = \Config\Database::connect();
    
    $data = [
        'title' => 'Admin Dashboard',
        'totalUsers' => $db->table('users')->countAllResults(),
        'totalTeachers' => $db->table('users')->where('role', 'Teacher')->countAllResults(),
        'totalStudents' => $db->table('users')->where('role', 'Student')->countAllResults(),
        'totalCourses' => $db->table('courses')->countAllResults(),
        'recentCourses' => $db->table('courses')
            ->select('courses.*, users.name as instructor_name')
            ->join('users', 'users.id = courses.course_instructor')
            ->orderBy('courses.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray()
    ];
    return view('admin/dashboard', $data);
}
    
    // List all users
    public function users()
    {
    $db = \Config\Database::connect();
    $users = $db->table('users')
               ->orderBy('created_at', 'DESC')
               ->get()
               ->getResultArray();
    
    $data = [   
        'title' => 'Manage Users',
        'users' => $users
    ];
    
    return view('admin/users/index', $data);
}
    
    // Show create user form
  public function newUser()
{
    $data = [
        'title' => 'Add New User',
        'user' => [  // Initialize user as an empty array with all required keys
            'name' => '',
            'email' => '',
            'role' => ''
        ]
    ];
    
    return view('admin/users/form', $data);
}

    // Store new user
    public function storeUser()
    {
        $request = service('request');
        
        $rules = [
            'name' => [
                'rules' => 'required|min_length[3]|max_length[100]|regex_match[/^[a-zA-Z0-9\s\-\'.,]+$/]',
                'errors' => [
                    'regex_match' => 'Name contains invalid characters. Only letters, numbers, spaces, hyphens, apostrophes, periods, and commas are allowed.'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]|max_length[100]|regex_match[/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/]',
                'errors' => [
                    'regex_match' => 'Please enter a valid email address.',
                    'is_unique' => 'This email is already registered.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[8]|max_length[255]',
                'errors' => [
                    'min_length' => 'Password must be at least 8 characters long.'
                ]
            ],
            'password_confirm' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'matches' => 'The passwords do not match.'
                ]
            ],
            'role' => [
                'rules' => 'required|in_list[Admin,Teacher,Student]',
                'errors' => [
                    'in_list' => 'Please select a valid role.'
                ]
            ]
        ];
        
        // Set validation rules
        $this->validation->setRules($rules);

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $userData = [
            'name' => $request->getPost('name'),
            'email' => $request->getPost('email'),
            'password' => $request->getPost('password'),
            'role' => $request->getPost('role'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        try {
            $this->userModel->insert($userData);
            return redirect()->to('/admin/users')->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }
    
    // Show edit user form
    public function editUser($id)
    {
        try {
            // Debug: Check if model is loaded
            if (!isset($this->userModel)) {
                log_message('error', 'UserModel is not loaded');
                throw new \RuntimeException('User model not initialized');
            }
            
            // Debug: Log the ID being searched for
            log_message('debug', 'Searching for user with ID: ' . $id);
            
            // Try to find the user
            $user = $this->userModel->find($id);
            
            if (!$user) {
                log_message('info', 'User not found with ID: ' . $id);
                return redirect()->to('/admin/users')->with('error', 'User not found.');
            }
            
            // Debug: Log successful user retrieval
            log_message('debug', 'Found user: ' . print_r($user, true));
            
            $data = [
                'title' => 'Edit User',
                'user' => $user
            ];
            
            return view('admin/users/form', $data);
            
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            log_message('error', 'Database error in editUser: ' . $e->getMessage());
            log_message('error', 'Last query: ' . $this->userModel->getLastQuery());
            return redirect()->to('/admin/users')->with('error', 'Database error occurred. Please try again later.');
            
        } catch (\Exception $e) {
            log_message('error', 'Unexpected error in editUser: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->to('/admin/users')->with('error', 'An unexpected error occurred. Please try again.');
        }
    }
    
    // Update user
    public function updateUser($id)
    {
        // Get the request service
        $request = \Config\Services::request();
        
        // Debug: Log all POST data
        log_message('debug', 'Raw POST data: ' . print_r($request->getPost(), true));
        
        // Set validation rules for update
        $rules = [
            'name' => [
                'rules' => 'required|min_length[3]|max_length[100]|regex_match[/^[a-zA-Z0-9\s\-\'.,]+$/]',
                'errors' => [
                    'regex_match' => 'Name contains invalid characters. Only letters, numbers, spaces, hyphens, apostrophes, periods, and commas are allowed.'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email,id,' . $id . ']|max_length[100]|regex_match[/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/]',
                'errors' => [
                    'regex_match' => 'Please enter a valid email address.',
                    'is_unique' => 'This email is already registered.'
                ]
            ],
            'role' => [
                'rules' => 'required|in_list[Admin,Teacher,Student]',
                'errors' => [
                    'in_list' => 'Please select a valid role.'
                ]
            ]
        ];
        
        // Add password validation if password is being updated
        if (!empty($request->getPost('password'))) {
            $rules['password'] = [
                'rules' => 'min_length[8]|max_length[255]',
                'errors' => [
                    'min_length' => 'Password must be at least 8 characters long.'
                ]
            ];
            $rules['password_confirm'] = [
                'rules' => 'matches[password]',
                'errors' => [
                    'matches' => 'The passwords do not match.'
                ]
            ];
        }
        
        // Set validation rules
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Get and sanitize form data
        $name = htmlspecialchars($request->getPost('name'), ENT_QUOTES, 'UTF-8');
        $email = filter_var($request->getPost('email'), FILTER_SANITIZE_EMAIL);
        $role = $request->getPost('role');
        $password = $request->getPost('password');
        
        // Prepare user data
        $data = [
            'name' => $name,
            'email' => $email,
            'role' => $role
        ];
        
        // Only update password if provided
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        // Debug: Log the data being used for update
        log_message('debug', 'Data being updated: ' . print_r($data, true));
        
        try {
            // Temporarily disable model validation to prevent issues
            $this->userModel->skipValidation(true);
            
            // Update the user
            $result = $this->userModel->update($id, $data);
            
            // Re-enable validation
            $this->userModel->skipValidation(false);
            
            // Debug: Log success
            log_message('debug', 'User updated successfully');
            
            return redirect()->to('/admin/users')->with('message', 'User updated successfully');
            
        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'Error updating user: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to update user: ' . $e->getMessage())
                ->withInput();
        }
}
    
    // Delete user
    public function deleteUser($id)
    {
        // Prevent deleting own account
        if ($id == session()->get('userID')) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }
        
        try {
            $this->userModel->delete($id);
            return redirect()->to('/admin/users')->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
   public function toggleStatus($id)
{
    // Check if it's an AJAX request by checking the X-Requested-With header
    $xRequestedWith = $this->request->getHeaderLine('X-Requested-With');
    if (strtolower($xRequestedWith) !== 'xmlhttprequest') {
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
    }

    $userModel = new \App\Models\UserModel();
    
    // Get the current status
    $user = $userModel->find($id);
    if (!$user) {
        return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
    }
    
    // Prevent toggling own status
    if ($id == session()->get('userID')) {
        return $this->response->setJSON(['success' => false, 'message' => 'You cannot change your own status']);
    }

    try {
        // Toggle status
        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        $userModel->update($id, ['status' => $newStatus]);
        
        return $this->response->setJSON([
            'success' => true,
            'newStatus' => $newStatus,
            'statusText' => ucfirst($newStatus)
        ]);
    } catch (\Exception $e) {
        log_message('error', 'Error toggling user status: ' . $e->getMessage());
        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status']);
    }
}
public function courses()
{
    $db = \Config\Database::connect();
    
    $courses = $db->table('courses')
        ->select('courses.*, users.name as instructor_name')
        ->join('users', 'users.id = courses.course_instructor')
        ->orderBy('courses.created_at', 'DESC')
        ->get()
        ->getResultArray();

    $data = [
        'title' => 'All Courses',
        'courses' => $courses
    ];
    
    return view('admin/courses/index', $data);
}

public function viewCourse($courseId = null)
{
    if (!$courseId) {
        return redirect()->to('/admin/courses')->with('error', 'Invalid course ID');
    }

    $db = \Config\Database::connect();
    
    // Get course details
    $course = $db->table('courses')
    ->select('courses.*, users.name as instructor_name, users.email as instructor_email')
    ->join('users', 'users.id = courses.course_instructor')
    ->where('courses.course_id', $courseId)
    ->get()
    ->getRowArray();

    if (!$course) {
        return redirect()->to('/admin/courses')->with('error', 'Course not found');
    }

    // Get enrolled students
    // In viewCourse method, update the enrolledStudents query
    $enrolledStudents = $db->table('enrollments')
    ->select('enrollments.*, users.name as student_name, users.email as student_email, users.id as user_id')
    ->join('users', 'users.id = enrollments.user_id')
    ->where('enrollments.course_id', $courseId)
    ->get()
    ->getResultArray();

    // Then update the array_column to use 'user_id' instead of 'student_id'
    $enrolledStudentIds = array_column($enrolledStudents, 'user_id');
    
    // If no students are enrolled, we need to handle that case
    $availableStudents = [];
    if (!empty($enrolledStudentIds)) {
        $availableStudents = $db->table('users')
            ->where('role', 'Student')
            ->whereNotIn('id', $enrolledStudentIds)
            ->get()
            ->getResultArray();
    } else {
        // If no students are enrolled yet, get all students
        $availableStudents = $db->table('users')
            ->where('role', 'Student')
            ->get()
            ->getResultArray();
    }

    $data = [
        'title' => 'Course Details',
        'course' => $course,
        'enrolledStudents' => $enrolledStudents,
        'availableStudents' => $availableStudents
    ];

    return view('admin/courses/view', $data);
}
public function editCourse($courseId)
{
    $db = \Config\Database::connect();
    
    $course = $db->table('courses')
        ->select('courses.*, users.name as instructor_name')
        ->join('users', 'users.id = courses.course_instructor')
        ->where('courses.course_id', $courseId)
        ->get()
        ->getRowArray();

    if (!$course) {
        return redirect()->to('/admin/courses')->with('error', 'Course not found');
    }

    // Get all teachers for the dropdown
    $teachers = $db->table('users')
        ->where('role', 'Teacher')
        ->get()
        ->getResultArray();

    $data = [
        'title' => 'Edit Course',
        'course' => $course,
        'teachers' => $teachers,
        'validation' => \Config\Services::validation()
    ];
    
    return view('admin/courses/edit', $data);
}

public function updateCourse($courseId)
{
    $db = \Config\Database::connect();
    $request = \Config\Services::request();
    
    // Validation rules
    $rules = [
        'course_code' => 'required|min_length[3]|max_length[50]',
        'course_name' => 'required|min_length[3]|max_length[255]',
        'description' => 'required|min_length[10]',
        'course_instructor' => 'required|numeric',
        'cn_number' => 'permit_empty|max_length[50]',
        'schedule_date' => 'permit_empty|valid_date',
        'schedule_time' => 'permit_empty',
        'room' => 'permit_empty|max_length[100]',
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    // Check if course exists
    $course = $db->table('courses')
        ->where('course_id', $courseId)
        ->get()
        ->getRowArray();

    if (!$course) {
        return redirect()->to('/admin/courses')->with('error', 'Course not found');
    }

    // Update course data
    $data = [
        'course_code' => $request->getPost('course_code'),
        'course_name' => $request->getPost('course_name'),
        'description' => $request->getPost('description'),
        'course_instructor' => $request->getPost('course_instructor'),
        'cn_number' => $request->getPost('cn_number'),
        'schedule_date' => $request->getPost('schedule_date'),
        'schedule_time' => $request->getPost('schedule_time'),
        'room' => $request->getPost('room'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    $db->table('courses')
        ->where('course_id', $courseId)
        ->update($data);

    return redirect()->to('/admin/courses')->with('message', 'Course updated successfully');
}
public function createCourse()
{
    $db = \Config\Database::connect();
    
    // Get all teachers for the dropdown
    $teachers = $db->table('users')
        ->where('role', 'Teacher')
        ->get()
        ->getResultArray();

    $data = [
        'title' => 'Create New Course',
        'teachers' => $teachers,
        'validation' => \Config\Services::validation()
    ];
    
    return view('admin/courses/create', $data);
}

public function storeCourse()
{
    $db = \Config\Database::connect();
    $request = \Config\Services::request();
    
    // Validation rules
    $rules = [
        'course_code' => 'required|min_length[3]|max_length[50]|is_unique[courses.course_code]',
        'course_name' => 'required|min_length[3]|max_length[255]',
        'description' => 'required|min_length[10]',
        'course_instructor' => 'required|numeric',
        'cn_number' => 'permit_empty|max_length[50]',
        'schedule_date' => 'permit_empty|valid_date',
        'schedule_time' => 'permit_empty',
        'room' => 'permit_empty|max_length[100]',
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    // Insert course data
    $data = [
        'course_code' => $request->getPost('course_code'),
        'course_name' => $request->getPost('course_name'),
        'description' => $request->getPost('description'),
        'course_instructor' => $request->getPost('course_instructor'),
        'cn_number' => $request->getPost('cn_number'),
        'schedule_date' => $request->getPost('schedule_date'),
        'schedule_time' => $request->getPost('schedule_time'),
        'room' => $request->getPost('room'),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    $db->table('courses')->insert($data);

    return redirect()->to('/admin/courses')->with('message', 'Course created successfully');
}
public function addStudent($courseId)
{
    $db = \Config\Database::connect();
    $request = \Config\Services::request();
    
    // Debug: Log the POST data
    log_message('debug', 'POST data: ' . print_r($request->getPost(), true));
    
    $studentId = $request->getPost('user_id');
    
    // Debug: Log the student ID and course ID
    log_message('debug', "Attempting to add student ID: $studentId to course ID: $courseId");

    // Verify course exists
    $course = $db->table('courses')
        ->where('course_id', $courseId)
        ->countAllResults();
        
    if (!$course) {
        log_message('error', "Course not found: $courseId");
        return redirect()->back()->with('error', 'Course not found');
    }

    // Verify student exists and is a student
    $student = $db->table('users')
        ->where('id', $studentId)
        ->where('role', 'Student')
        ->get()
        ->getRowArray();

    if (!$student) {
        log_message('error', "Invalid student selected: $studentId");
        return redirect()->back()->with('error', 'Invalid student selected');
    }

    // Check if already enrolled
    $enrollment = $db->table('enrollments')
        ->where('course_id', $courseId)
        ->where('user_id', $studentId)
        ->countAllResults();

    if ($enrollment > 0) {
        log_message('info', "Student $studentId is already enrolled in course $courseId");
        return redirect()->back()->with('error', 'Student is already enrolled in this course');
    }

    // Enroll student
    try {
        $db->table('enrollments')->insert([
            'course_id' => $courseId,
            'user_id' => $studentId
        ]);
        log_message('info', "Successfully enrolled student $studentId in course $courseId");
        return redirect()->back()->with('message', 'Student added to course successfully');
    } catch (\Exception $e) {
        log_message('error', "Error enrolling student: " . $e->getMessage());
        return redirect()->back()->with('error', 'Error enrolling student: ' . $e->getMessage());
    }
}

public function removeStudent($courseId, $studentId)
{
    $db = \Config\Database::connect();
    
    // Remove enrollment
    $db->table('enrollments')
        ->where('course_id', $courseId)
        ->where('user_id', $studentId)  // Changed from user_id to student_id
        ->delete();

    return redirect()->back()->with('message', 'Student removed from course successfully');
}

public function deleteCourse($courseId = null)
{
    $db = \Config\Database::connect();
    
    // Verify course exists
    $course = $db->table('courses')
        ->where('course_id', $courseId)
        ->get()
        ->getRowArray();

    if (!$course) {
        log_message('error', "Attempted to delete non-existent course: $courseId");
        return redirect()->back()->with('error', 'Course not found');
    }

    try {
        // Start transaction
        $db->transStart();
        
        // First delete all enrollments for this course
        $db->table('enrollments')
            ->where('course_id', $courseId)
            ->delete();
            
        // Then delete the course
        $db->table('courses')
            ->where('course_id', $courseId)
            ->delete();
            
        // Complete transaction
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            throw new \RuntimeException('Transaction failed');
        }
        
        log_message('info', "Successfully deleted course ID: $courseId");
        return redirect()->to('/admin/courses')->with('message', 'Course deleted successfully');
        
    } catch (\Exception $e) {
        log_message('error', "Error deleting course $courseId: " . $e->getMessage());
        return redirect()->back()->with('error', 'Error deleting course: ' . $e->getMessage());
    }
}
}
