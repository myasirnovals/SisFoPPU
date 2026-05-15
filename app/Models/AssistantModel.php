<?php

namespace App\Models;

use CodeIgniter\Model;

class AssistantModel extends Model
{
    protected $table = 'assistants';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id', 'assistant_code', 'assistant_name', 'study_program_id', 'is_active', 'created_at', 'updated_at',
    ];
    protected $useTimestamps = true;
}
