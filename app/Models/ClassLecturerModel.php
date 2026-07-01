<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassLecturerModel extends Model
{
    protected $table            = 'class_lecturers';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'practicum_class_id',
        'lecturer_id',
        'role_type',
        'created_at',
    ];

    // MATIKAN timestamps otomatis — kita handle manual
    protected $useTimestamps = false;

    /**
     * Get lecturers for a class
     */
    public function getByClassId(int $classId): array
    {
        $builder = $this->db->table('class_lecturers cl');
        $builder->select([
            'cl.id',
            'cl.practicum_class_id',
            'cl.lecturer_id',
            'cl.role_type',
            'cl.created_at',
            'u.full_name as lecturer_name',
            'u.id as user_nid',
        ]);
        // Join ke users karena lecturer_id (INT) = CAST(users.id AS UNSIGNED)
        $builder->join('users u', 'CAST(u.id AS UNSIGNED) = cl.lecturer_id', 'left');
        $builder->where('cl.practicum_class_id', $classId);

        return $builder->get()->getResultArray();
    }

    /**
     * Assign lecturer to class
     */
    public function assignLecturer(int $classId, ?string $lecturerId, string $roleType = 'pengampu'): bool
    {
        // Hapus dosen dengan role sama di kelas ini
        $this->where('practicum_class_id', $classId)
            ->where('role_type', $roleType)
            ->delete();

        // Jika tidak ada dosen yang dipilih, cukup hapus (return true)
        if (empty($lecturerId)) {
            return true;
        }

        return (bool) $this->insert([
            'practicum_class_id' => $classId,
            'lecturer_id'        => $lecturerId,
            'role_type'          => $roleType,
            'created_at'         => date('Y-m-d H:i:s'),
        ]);
    }
}
