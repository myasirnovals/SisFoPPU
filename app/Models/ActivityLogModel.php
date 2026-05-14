<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id', 'role_name', 'module_name', 'action_name', 'description', 'metadata', 'created_at',
    ];
    protected $useTimestamps = true;
}
