<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'message', 'is_read', 'created_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';

    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
                   ->where('is_read', 0)
                   ->countAllResults();
    }

    public function getNotificationsForUser($userId, $limit = 5)
{
    log_message('debug', 'Getting notifications for user ID: ' . $userId);
    
    $notifications = $this->where('user_id', $userId)
                         ->orderBy('created_at', 'DESC')
                         ->findAll($limit);
                         
    log_message('debug', 'Found notifications: ' . print_r($notifications, true));
    return $notifications;
}

    public function markAsRead($notificationId, $userId)
    {
        return $this->where('id', $notificationId)
                   ->where('user_id', $userId)
                   ->set('is_read', 1)
                   ->update();
    }
}