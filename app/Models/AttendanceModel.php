<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table = 'attendance_sessions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'class_id', 'group_id', 'meeting_no', 'session_date', 'status', 'is_locked', 'created_at', 'updated_at',
    ];
    protected $useTimestamps = true;
}