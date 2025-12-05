<?php

namespace App\Controllers;

class Teacher extends BaseController
{
    public function __construct()
    {
        // Check if user is logged in and is a teacher
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            return redirect()->to('/login');
        }
    }

   public function dashboard()
    {
        $data = [
            'title' => 'Teacher Dashboard'
        ];
        return view('teacher/dashboard', $data);
    }
}
