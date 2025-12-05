<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description', 'created_at', 'updated_at'];

    public function getAvailableCourses($user_id)
    {
        return $this->db->table('courses c')
            ->select('c.*')
            ->whereNotIn('c.id', function($builder) use ($user_id) {
                return $builder->select('course_id')
                              ->from('enrollments')
                              ->where('user_id', $user_id);
            })
            ->get()
            ->getResultArray();
    }
}