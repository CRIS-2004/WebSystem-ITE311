<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['user_id', 'course_id', 'enrollment_date'];

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function enrollUser($data)
    {
        try {
            $builder = $this->db->table($this->table);
            $result = $builder->insert($data);
            
            if ($result) {
                return $this->db->insertID();
            }
            return false;
        } catch (\Exception $e) {
            log_message('error', 'Error in enrollUser: ' . $e->getMessage());
            return false;
        }
    }

    public function getUserEnrollments($user_id)
    {
        return $this->select('enrollments.*, courses.course_name, courses.description, 
                         users.name as instructor_name, users.email as instructor_email')
                ->join('courses', 'courses.course_id = enrollments.course_id')
                ->join('users', 'users.id = courses.course_instructor')
                ->where('enrollments.user_id', $user_id)
                ->findAll();
    }

    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                   ->where('course_id', $course_id)
                   ->countAllResults() > 0;
    }
    
    public function getEnrollment($userId, $courseId)
    {
        return $this->where('user_id', $userId)
                   ->where('course_id', $courseId)
                   ->first();
    }
}