<?php

namespace App\Models;

use CodeIgniter\Model;

class PracticumGroupModel extends Model
{
    protected $table = 'practicum_groups';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'class_id', 'group_code', 'group_name', 'assistant_id', 'status', 'created_at', 'updated_at', 'deleted_at',
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
}