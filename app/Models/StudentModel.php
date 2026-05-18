<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'student_number', 'full_name', 'email', 'study_program_id', 'is_active', 'created_at', 'updated_at',
    ];
    protected $useTimestamps = true;
}
