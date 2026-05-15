<?php

namespace App\Models;

use CodeIgniter\Model;

class FinalScoreModel extends Model
{
    protected $table = 'final_scores';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'class_id', 'student_id', 'final_score', 'grade_letter', 'status', 'validation_status',
        'validated_by', 'validated_at', 'locked_at', 'notes', 'created_at', 'updated_at',
    ];
    protected $useTimestamps = true;
}
