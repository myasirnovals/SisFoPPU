<?php

namespace App\Models;

use CodeIgniter\Model;

class PracticumClassModel extends Model
{
    protected $table = 'practicum_classes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'course_id', 'academic_year_id', 'semester_id', 'class_code', 'class_name', 'lecturer_id',
        'assistant_id', 'status', 'deadline_at', 'notes', 'created_at', 'updated_at',
    ];
    protected $useTimestamps = true;
}
