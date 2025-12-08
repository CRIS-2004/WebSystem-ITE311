<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            log_message('debug', 'User not logged in, redirecting to login');
            return redirect()->to('/login');
        }
        
        // Check if user has admin role (case-insensitive)
        $userRole = strtolower(session()->get('role') ?? '');
        if ($userRole !== 'admin') {
            log_message('debug', 'User role is not admin. Current role: ' . session()->get('role'));
            return redirect()->to('/login');
        }
        
        // If we return null, the request will proceed normally
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after the controller runs
        return $response;
    }
}
