<?php

namespace App\Models;

use CodeIgniter\Model;

class ValidationLogModel extends Model
{
    protected $table = 'validation_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'class_id', 'score_status', 'validation_status', 'validated_by', 'submitted_at', 'validated_at',
        'revision_count', 'notes', 'created_at', 'updated_at',
    ];
    protected $useTimestamps = true;
}
