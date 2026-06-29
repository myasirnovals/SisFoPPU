<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'study_program_id',
        'course_code',
        'course_name',
        'credits',
        'is_practicum',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;

    /**
     * Get active courses for dropdown
     */
    public function getActiveCourses(): array
    {
        return $this->where('status', 'aktif')
            ->where('is_practicum', 1)
            ->where('deleted_at', null)
            ->findAll();
    }

    public function countAll(): int
    {
        return $this->where('deleted_at', null)->countAllResults();
    }
}
