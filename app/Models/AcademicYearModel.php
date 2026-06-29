<?php

namespace App\Models;

use CodeIgniter\Model;

class AcademicYearModel extends Model
{
    protected $table            = 'academic_years';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    // Sesuai dengan kolom di screenshot phpMyAdmin kamu
    protected $allowedFields    = ['year_code', 'start_date', 'end_date', 'is_active'];
    protected $useTimestamps    = true;
}
