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
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
            'role' => 'required|in_list[Admin,Teacher,Student]'
        ];
        
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
        
        // Manually get the form data
        $name = $request->getPost('name');
        $email = $request->getPost('email');
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
}
