<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturerModel extends Model
{
    protected $table = 'lecturers';
    protected $primaryKey = 'user_nid';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_nid',
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
     * Get lecturers with user info
     */
    public function getLecturersWithUserInfo(array $filters = []): array
    {
        $builder = $this->db->table('lecturers l');
        $builder->select([
            'l.user_nid',
            'l.study_program_id',
            'l.status as lecturer_status',
            'u.full_name',
            'u.email',
            'u.phone',
            'u.is_active',
            'sp.program_name',
        ]);
        $builder->join('users u', 'u.id = l.user_nid', 'inner');
        $builder->join('study_programs sp', 'sp.id = l.study_program_id', 'left');
        $builder->where('l.deleted_at', null);
        $builder->where('u.deleted_at', null);

        if (!empty($filters['study_program_id'])) {
            $builder->where('l.study_program_id', $filters['study_program_id']);
        }
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('u.full_name', $filters['search'])
                ->orLike('l.user_nid', $filters['search'])
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

    /**
     * Get lecturer ID (user_nid) by user ID
     */
    public function getLecturerIdByUserId(string $userId): ?string
    {
        $row = $this->where('user_nid', $userId)
            ->where('deleted_at', null)
            ->first();

        return $row['user_nid'] ?? null;
    }

    /**
     * Get lecturer profile with user info
     */
    public function getLecturerProfile(string $userNid): ?array
    {
        $builder = $this->db->table('lecturers l');
        $builder->select([
            'l.user_nid',
            'l.study_program_id',
            'l.status',
            'u.full_name',
            'u.email',
            'u.phone',
        ]);
        $builder->join('users u', 'u.id = l.user_nid', 'inner');
        $builder->where('l.user_nid', $userNid);
        $builder->where('l.deleted_at', null);

        return $builder->get()->getRowArray() ?: null;
    }
}
