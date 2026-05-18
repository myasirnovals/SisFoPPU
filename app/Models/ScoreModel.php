<?php

namespace App\Models;

use CodeIgniter\Model;

class ScoreModel extends Model
{
    protected $table = 'score_entries';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'class_id', 'group_id', 'student_id', 'component_id', 'subcomponent_id', 'score_value', 'submitted_by', 'submitted_at', 'notes', 'created_at', 'updated_at',
    ];
    protected $useTimestamps = true;
}