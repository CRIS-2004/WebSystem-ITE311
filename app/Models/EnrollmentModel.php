<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = ['user_id', 'course_id', 'enrollment_date'];

    // Enroll a user in a course
    public function enrollUser($data)
    {
        return $this->insert($data);
    }

    // Get all courses a user is enrolled in
    public function getUserEnrollments($user_id)
    {
        return $this->db->table('enrollments e')
            ->select('c.*, e.enrollment_date')
            ->join('courses c', 'c.id = e.course_id')
            ->where('e.user_id', $user_id)
            ->get()
            ->getResultArray();
    }

    // Check if user is already enrolled in a course
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                   ->where('course_id', $course_id)
                   ->countAllResults() > 0;
    }
}