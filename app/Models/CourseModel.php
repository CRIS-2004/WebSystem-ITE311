<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'course_id';
    protected $allowedFields = [ 'course_code', 'course_name', 'cn_number','schedule_date', 'schedule_time', 'room', 'course_instructor', 'description', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function __construct($db = null)
    {
        parent::__construct();
        $this->db = $db ?? \Config\Database::connect();
    }

    public function getAvailableCourses($user_id)
    {
        return $this->db->table($this->table)
            ->select('*')
            ->whereNotIn('course_id', function($builder) use ($user_id) {
                return $builder->select('course_id')
                              ->from('enrollments')
                              ->where('user_id', $user_id);
            })
            ->get()
            ->getResultArray();
    }
    
   public function getCourseWithInstructor($courseId)
{
    return $this->select('courses.*, users.name as instructor_name')
               ->join('users', 'users.id = courses.course_instructor', 'left')
               ->where('courses.course_id', $courseId)
               ->first();
}
    
    public function getCourseModules($courseId)
    {
        // This is a placeholder - you'll need to implement this based on your modules table structure
        return [];
    }
    
    public function getUserProgress($userId, $courseId)
    {
        // This is a placeholder - implement based on your progress tracking
        return 0;
    }
}