<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturerModel extends Model
{
    protected $table = 'lecturers';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id', 'nidn', 'lecturer_name', 'study_program_id', 'is_active', 'created_at', 'updated_at',
    ];
    protected $useTimestamps = true;
}
