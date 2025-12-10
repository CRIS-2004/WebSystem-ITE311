<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\CourseModel;

class Course extends BaseController
{
    protected $enrollmentModel;
    protected $courseModel;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->courseModel = new CourseModel();
        helper(['form', 'url', 'date']);
    }
    
    public function test($id = null)
    {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Test route is working',
            'course_id' => $id
        ]);
    }

    public function enroll()
    {
        try {
            log_message('debug', 'Enroll method called');
            
            if (!$this->request->isAJAX()) {
                log_message('error', 'Non-AJAX request attempted');
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
            }

            if (!session()->get('isLoggedIn')) {
                log_message('error', 'Unauthenticated enrollment attempt');
                return $this->response->setJSON(['status' => 'error', 'message' => 'Please login to enroll']);
            }

            $course_id = $this->request->getPost('course_id');
            $user_id = session()->get('userID');
            
            log_message('debug', 'Attempting enrollment - User ID: ' . $user_id . ', Course ID: ' . $course_id);

            // Check if already enrolled
            if ($this->enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
                log_message('info', 'User already enrolled - User ID: ' . $user_id . ', Course ID: ' . $course_id);
                return $this->response->setJSON(['status' => 'error', 'message' => 'You are already enrolled in this course']);
            }

            // Enroll the user
            $data = [
                'user_id' => $user_id,
                'course_id' => $course_id,
                'enrollment_date' => date('Y-m-d H:i:s')
            ];

            log_message('debug', 'Enrollment data: ' . print_r($data, true));

            $result = $this->enrollmentModel->enrollUser($data);
            log_message('debug', 'Enrollment result: ' . ($result ? 'true' : 'false'));

            if ($result) {
                log_message('info', 'Successfully enrolled user - User ID: ' . $user_id . ', Course ID: ' . $course_id);
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Successfully enrolled in the course!'
                ]);
            } else {
                $error = $this->enrollmentModel->errors() ?? 'Unknown error';
                log_message('error', 'Enrollment failed - ' . print_r($error, true));
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to enroll in the course. Please try again.',
                    'error' => $error
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Enrollment exception: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function view($courseId = null)
    {
        try {
            if (!$courseId || !is_numeric($courseId)) {
                return redirect()->to('/dashboard')->with('error', 'Invalid course ID');
            }

            $userId = session()->get('userID');
            if (!$userId) {
                return redirect()->to('/login')->with('error', 'Please login to view this course');
            }

            // Get course details with instructor info
            $course = $this->courseModel->getCourseWithInstructor($courseId);
            if (!$course) {
                return redirect()->to('/dashboard')->with('error', 'Course not found');
            }

            // Check if user is enrolled
            $isEnrolled = $this->enrollmentModel->isAlreadyEnrolled($userId, $courseId);
            
            // Get enrollment details if enrolled
            $enrollment = $isEnrolled ? $this->enrollmentModel->getEnrollment($userId, $courseId) : null;

            // Get course modules/lessons
            $modules = $this->courseModel->getCourseModules($courseId);
            
            // Get user progress
            $progress = $isEnrolled ? $this->courseModel->getUserProgress($userId, $courseId) : 0;

            $data = [
                'title' => $course['course_name'] . ' - Course Details',
                'course' => $course,
                'isEnrolled' => $isEnrolled,
                'enrollment' => $enrollment,
                'modules' => $modules,
                'progress' => $progress,
                'lastAccessed' => $enrollment['last_accessed'] ?? null
            ];

            return view('student/course/view', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Course::view - ' . $e->getMessage());
            log_message('error', $e->getTraceAsString());
            return redirect()->back()->with('error', 'An error occurred while loading the course. Please try again.');
        }
    }
}