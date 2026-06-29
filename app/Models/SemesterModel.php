<?php

namespace App\Models;

use CodeIgniter\Model;

class SemesterModel extends Model
{
    protected $table            = 'semesters';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'academic_year_id',
        'semester_code',
        'semester_name',
        'semester_number',
        'start_date',
        'end_date',
        'is_active',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Get active semesters with academic year info
     */
    public function getActive(): array
    {
        $builder = $this->db->table('semesters s');
        $builder->select([
            's.id',
            's.semester_code',
            's.semester_name',
            's.semester_number',
            's.start_date',
            's.end_date',
            's.is_active',
            'ay.year_code',
        ]);
        $builder->join('academic_years ay', 'ay.id = s.academic_year_id', 'inner');
        $builder->where('s.is_active', 1);
        $builder->orderBy('s.start_date', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get semesters by academic year
     */
    public function getByAcademicYear(int $academicYearId): array
    {
        return $this->where('academic_year_id', $academicYearId)
            ->where('is_active', 1)
            ->orderBy('semester_number', 'ASC')
            ->findAll();
    }
}
