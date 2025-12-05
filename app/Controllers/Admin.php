<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function __construct()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }
    }

    public function dashboard()
    {
        $data = [
            'title' => 'Admin Dashboard'
        ];
        return view('admin/dashboard', $data);
    }
}
