<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;

class Course extends BaseController
{
    protected $enrollmentModel;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        helper(['form', 'url']);
    }

    public function enroll()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Please login to enroll']);
        }

        $course_id = $this->request->getPost('course_id');
        $user_id = session()->get('userID');

        // Check if already enrolled
        if ($this->enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'You are already enrolled in this course']);
        }

        // Enroll the user
        $data = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrollment_date' => date('Y-m-d H:i:s')
        ];

        if ($this->enrollmentModel->enrollUser($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Successfully enrolled in the course!'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to enroll in the course'
            ]);
        }
    }
}