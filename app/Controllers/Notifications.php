<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\API\ResponseTrait;

class Notifications extends BaseController
{
    use ResponseTrait;

    protected $notificationModel;
    protected $session;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->session = \Config\Services::session();
    }

    public function get()
{
    if (!$this->session->get('isLoggedIn')) {
        log_message('error', 'User not logged in when trying to fetch notifications');
        return $this->failUnauthorized('User not logged in');
    }

    $userId = $this->session->get('user_id');
    log_message('debug', 'Fetching notifications for user ID: ' . $userId);
    
    $unreadCount = $this->notificationModel->getUnreadCount($userId);
    $notifications = $this->notificationModel->getNotificationsForUser($userId);
    
    log_message('debug', 'Found ' . $unreadCount . ' unread notifications');
    log_message('debug', 'Returning notifications: ' . print_r($notifications, true));

    return $this->respond([
        'success' => true,
        'unreadCount' => $unreadCount,
        'notifications' => $notifications
    ]);
}

    public function mark_as_read($id = null)
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->failUnauthorized('User not logged in');
        }

        if ($id === null) {
            return $this->fail('Notification ID is required', 400);
        }

        $userId = $this->session->get('user_id');
        $success = $this->notificationModel->markAsRead($id, $userId);

        if ($success) {
            $unreadCount = $this->notificationModel->getUnreadCount($userId);
            return $this->respond([
                'success' => true,
                'unreadCount' => $unreadCount
            ]);
        }

        return $this->fail('Failed to mark notification as read', 500);
    }

    /**
     * Test endpoint to add a notification
     * Accessible at: /notifications/test
     */
    public function test()
    {
        $userId = $this->session->get('user_id');
        
        if (!$userId) {
            // If no user is logged in, try to get the first user
            $db = \Config\Database::connect();
            $user = $db->table('users')->select('id')->limit(1)->get()->getRowArray();
            
            if (!$user) {
                return $this->fail('No users found in the database', 404);
            }
            
            $userId = $user['id'];
        }

        $data = [
            'user_id' => $userId,
            'message' => 'This is a test notification - ' . date('Y-m-d H:i:s'),
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $db = \Config\Database::connect();
        $db->table('notifications')->insert($data);
        $insertId = $db->insertID();

        if ($insertId) {
            return $this->respond([
                'success' => true,
                'message' => 'Test notification added successfully',
                'notification_id' => $insertId,
                'user_id' => $userId
            ]);
        }

        return $this->fail('Failed to add test notification', 500);
    }
    public function mark_all_read()
{
    if (!$this->session->get('isLoggedIn')) {
        return $this->failUnauthorized('User not logged in');
    }

    $userId = $this->session->get('user_id');
    $this->notificationModel->where('user_id', $userId)
                           ->where('is_read', 0)
                           ->set(['is_read' => 1])
                           ->update();

    return $this->respond(['success' => true]);
}
}
