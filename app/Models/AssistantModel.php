<?php

namespace App\Models;

use CodeIgniter\Model;

class AssistantModel extends Model
{
    protected $table = 'assistants';
    protected $primaryKey = 'user_nim';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_nim',
        'study_program_id',
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
     * Get assistants with user info
     */
    public function getAssistantsWithUserInfo(array $filters = []): array
    {
        $builder = $this->db->table('assistants a');
        $builder->select([
            'a.user_nim',
            'a.study_program_id',
            'a.status as assistant_status',
            'u.full_name',
            'u.email',
            'u.phone',
            'u.is_active',
            'sp.program_name',
        ]);
        $builder->join('users u', 'u.id = a.user_nim', 'inner');
        $builder->join('study_programs sp', 'sp.id = a.study_program_id', 'left');
        $builder->where('a.deleted_at', null);
        $builder->where('u.deleted_at', null);

        if (!empty($filters['study_program_id'])) {
            $builder->where('a.study_program_id', $filters['study_program_id']);
        }
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('u.full_name', $filters['search'])
                ->orLike('a.user_nim', $filters['search'])
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
