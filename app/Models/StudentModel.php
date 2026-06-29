<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'user_nim';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_nim',
        'study_program_id',
        'class_year',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $useSoftDeletes = true;

    /**
     * Get students with user info
     */
    public function getStudentsWithUserInfo(array $filters = []): array
    {
        $builder = $this->db->table('students s');
        $builder->select([
            's.user_nim',
            's.study_program_id',
            's.class_year',
            's.status as student_status',
            'u.full_name',
            'u.email',
            'u.phone',
            'u.is_active',
            'sp.program_name',
        ]);
        $builder->join('users u', 'u.id = s.user_nim', 'inner');
        $builder->join('study_programs sp', 'sp.id = s.study_program_id', 'left');
        $builder->where('s.deleted_at', null);
        $builder->where('u.deleted_at', null);

        if (!empty($filters['study_program_id'])) {
            $builder->where('s.study_program_id', $filters['study_program_id']);
        }
        if (!empty($filters['class_year'])) {
            $builder->where('s.class_year', $filters['class_year']);
        }
        if (!empty($filters['status'])) {
            $builder->where('s.status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('u.full_name', $filters['search'])
                ->orLike('s.user_nim', $filters['search'])
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    public function countActive(): int
    {
        return $this->where('status', 'aktif')
            ->where('deleted_at', null)
            ->countAllResults();
    }
}
