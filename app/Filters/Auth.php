<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Debug session data
        log_message('debug', '=== AUTH FILTER ===');
        log_message('debug', 'Session data: ' . print_r(session()->get(), true));
        log_message('debug', 'Requested URL: ' . current_url());
        log_message('debug', 'Request method: ' . $request->getMethod());
        log_message('debug', 'Required roles: ' . print_r($arguments, true));

        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
        return redirect()->to('/login');
    }
        // If no roles are specified, just check if logged in
        if (empty($arguments)) {
            log_message('debug', 'No roles required, access granted');
            return;
        }

        // Check if user has required role
        $userRole = strtolower(session()->get('role') ?? '');
        log_message('debug', 'User role: ' . $userRole);
        
        if (!in_array($userRole, $arguments)) {
            log_message('debug', 'Access denied - User role not in required roles');
            return redirect()->to('/login')->with('error', 'You do not have permission to access this page');
        }
        
        log_message('debug', 'Access granted');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after the response
    }
}
