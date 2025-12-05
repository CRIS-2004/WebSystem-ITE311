<?php

namespace App\Controllers;

class Auth extends BaseController
{
    protected $session;
    protected $validation;
    protected $db;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect();
    }

    // Registration
    public function register()
    {
        // Only redirect on GET requests if already logged in
        if ($this->request->getMethod() === 'get' && $this->session->get('isLoggedIn') === true) {
            return redirect()->to(base_url('dashboard'));
        }

        if ($this->request->getMethod() === 'POST') {

            $rules = [
                'name'             => 'required|min_length[3]|max_length[100]',
                'email'            => 'required|valid_email|is_unique[users.email]',
                'password'         => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]',
                'role'             => 'required'
            ];

            $messages = [
                'name' => [
                    'required'   => 'Name is required.',
                    'min_length' => 'Name must be at least 3 characters.',
                    'max_length' => 'Name cannot exceed 100 characters.'
                ],
                'email' => [
                    'required'    => 'Email is required.',
                    'valid_email' => 'Invalid email.',
                    'is_unique'   => 'Email already exists.'
                ],
                'password' => [
                    'required'   => 'Password is required.',
                    'min_length' => 'Password must be at least 6 characters.'
                ],
                'password_confirm' => [
                    'required' => 'Password confirmation required.',
                    'matches'  => 'Passwords do not match.'
                ]
            ];

            if ($this->validate($rules, $messages)) {
                $hashedPassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

                $userData = [
                    'name'       => $this->request->getPost('name'),
                    'email'      => $this->request->getPost('email'),
                    'password'   => $hashedPassword,
                    'role'       => $this->request->getPost('role'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                try {
                    $builder = $this->db->table('users');
                    $builder->insert($userData);

                    $this->session->setFlashdata('success', 'Registration successful! Please login.');
                    return redirect()->to(base_url('login'));

                } catch (\Exception $e) {
                    $this->session->setFlashdata('error', 'Registration failed: ' . $e->getMessage());
                    return redirect()->back()->withInput();
                }

            } else {
                $this->session->setFlashdata('errors', $this->validation->getErrors());
                return redirect()->back()->withInput();
            }
        }

        return view('auth/register', ['title' => 'Register']);
    }

    // Login
    public function login()
    {
        // If this is a POST request, process the login
        if ($this->request->getMethod() === 'POST') {
            $login = $this->request->getPost('login');
            $password = $this->request->getPost('password');

            // Basic validation
            if (empty($login) || empty($password)) {
                $this->session->setFlashdata('error', 'Please enter both login and password.');
                return redirect()->to(base_url('login'))->withInput();
            }

            // Hardcoded admin login (for testing only - remove in production)
            if ($login === 'admin' && $password === 'admin123') {
                $this->session->set([
                    'userID'     => 1,
                    'name'       => 'Admin',
                    'email'      => 'admin@rmmc.com',
                    'role'       => 'admin',
                    'isLoggedIn' => true
                ]);
                $this->session->setFlashdata('success', 'Welcome back, Administrator!');
                return redirect()->to(base_url('admin/dashboard'));
            }

            // Check DB for user
            $builder = $this->db->table('users');
            $user = $builder->where('email', $login)
                          ->orWhere('name', $login)
                          ->get()
                          ->getRowArray();

            if ($user && password_verify($password, $user['password'])) {
                $userData = [
                    'userID'     => $user['id'],
                    'name'       => $user['name'],
                    'email'      => $user['email'],
                    'role'       => $user['role'],
                    'isLoggedIn' => true
                ];
                
                // Set session data
                $this->session->set($userData);
                
                // Log the session data for debugging
                log_message('debug', 'User logged in: ' . print_r($userData, true));
                
                $redirectUrl = $this->getDashboardUrl($user['role']);
                log_message('debug', 'Redirecting to: ' . $redirectUrl);
                
                return redirect()->to($redirectUrl);
            } else {
                $this->session->setFlashdata('error', 'Invalid login credentials.');
                return redirect()->to(base_url('login'))->withInput();
            }
        }

        return view('auth/login', ['title' => 'Login']);
    }

    // Logout
    public function logout()
    {
        $this->session->destroy();
        return redirect()->to(base_url('login'));
    }

    // Dashboard
    public function dashboard()
    {
        if ($this->session->get('isLoggedIn') !== true) {
            $this->session->setFlashdata('error', 'Please login to access the dashboard.');
            return redirect()->to(base_url('login'));
        }

        $userData = [
            'userID' => $this->session->get('userID'),
            'name'   => $this->session->get('name'),
            'email'  => $this->session->get('email'),
            'role'   => $this->session->get('role')
        ];

        return view('auth/dashboard', [
            'user'  => $userData,
            'title' => 'LMS - Dashboard'
        ]);
    }


 // app/Controllers/Auth.php
protected function getDashboardUrl($role)
{
    if (empty($role)) {
        log_message('error', 'No role provided for dashboard redirection');
        return base_url('login');
    }
    
    $role = strtolower(trim($role));
    log_message('debug', "Getting dashboard URL for role: $role");
    
    switch ($role) {
        case 'admin':
            return base_url('admin/dashboard');
        case 'teacher':
            return base_url('teacher/dashboard');
        case 'student':
            return base_url('student/dashboard');
        default:
            log_message('error', "Unknown role for dashboard: $role");
            return base_url('login');
    }
}
}