<?php

namespace App\Models;

use CodeIgniter\Model;

class RemedialParticipantModel extends Model
{
    protected $table = 'remedial_participants';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'remedial_period_id', 'student_id', 'class_id', 'status', 'reason', 'remedial_date',
        'validated_at', 'validated_by', 'created_at', 'updated_at',
    ];
    protected $useTimestamps = true;
}
