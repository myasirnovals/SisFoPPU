<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'study_program_id', 'course_code', 'course_name', 'credits', 'is_active', 'created_at', 'updated_at',
    ];
    protected $useTimestamps = true;
}
