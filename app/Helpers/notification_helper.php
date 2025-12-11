<?php

use App\Models\NotificationModel;

if (!function_exists('add_notification')) {
    function add_notification($userId, $message) {
        $notificationModel = new NotificationModel();
        
        $data = [
            'user_id' => $userId,
            'message' => $message,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $notificationModel->insert($data) !== false;
    }
}

if (!function_exists('notify_student_enrollment')) {
    function notify_student_enrollment($studentId, $courseName, $enrolledBy) {
        $message = "You have been enrolled in course '{$courseName}' by {$enrolledBy}";
        return add_notification($studentId, $message);
    }
}

if (!function_exists('notify_enrollment_removal')) {
    function notify_enrollment_removal($studentId, $courseName, $removedBy) {
        $message = "You have been removed from course '{$courseName}' by {$removedBy}";
        return add_notification($studentId, $message);
    }
}

if (!function_exists('notify_duplicate_enrollment')) {
    function notify_duplicate_enrollment($userId, $courseName) {
        $message = "You are already enrolled in course '{$courseName}'";
        return add_notification($userId, $message);
    }
}