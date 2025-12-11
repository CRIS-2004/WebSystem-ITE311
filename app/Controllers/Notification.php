<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class Notification extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    public function get()
{
    if (!session()->has('user_id')) {
        return $this->response->setJSON(['error' => 'Not logged in'])->setStatusCode(401);
    }

    $userId = session('user_id');
    
    // Get unread count
    $unreadCount = $this->notificationModel
        ->where('user_id', $userId)
        ->where('is_read', 0)
        ->countAllResults();
    
    // Get latest notifications
    $notifications = $this->notificationModel
        ->where('user_id', $userId)
        ->orderBy('created_at', 'DESC')
        ->findAll(10); // Get last 10 notifications

    return $this->response->setJSON([
        'success' => true,
        'unreadCount' => $unreadCount,
        'notifications' => $notifications
    ]);
}

    public function mark_read($id)
    {
        if (!session()->has('user_id')) {
            return $this->response->setJSON(['error' => 'Not logged in'])->setStatusCode(401);
        }

        $success = $this->notificationModel
            ->where('id', $id)
            ->where('user_id', session('user_id'))
            ->set(['is_read' => 1])
            ->update();

        return $this->response->setJSON(['success' => (bool)$success]);
    }
}