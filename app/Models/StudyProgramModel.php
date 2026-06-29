<?php

namespace App\Models;

use CodeIgniter\Model;

class StudyProgramModel extends Model
{
    protected $table            = 'study_programs'; // Sesuaikan dengan nama tabel DB kamu
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['program_code', 'program_name', 'faculty_name', 'degree_level', 'status', 'created_at', 'updated_at', 'deleted_at'];
    protected $useTimestamps    = true;
}
