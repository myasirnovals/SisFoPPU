<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassStudentModel extends Model
{
    protected $table            = 'class_students';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'practicum_class_id',
        'student_nim',
        'group_id',
        'enrollment_status',
        'enrolled_at',
        'created_at',
        'updated_at',
    ];

    // MATIKAN timestamps otomatis — kita handle manual di method
    protected $useTimestamps = false;

    /**
     * Get students in a class with user info
     */
    public function getByClassId(int $classId): array
    {
        $builder = $this->db->table('class_students cs');
        $builder->select([
            'cs.id',
            'cs.student_nim',
            'cs.group_id',
            'cs.enrollment_status',
            'u.full_name',
            'u.email',
            'pg.group_name',
            'pg.group_code',
        ]);
        $builder->join('users u', 'u.id = cs.student_nim', 'left');  // LEFT JOIN agar aman
        $builder->join('practicum_groups pg', 'pg.id = cs.group_id', 'left');
        $builder->where('cs.practicum_class_id', $classId);
        $builder->orderBy('u.full_name', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Add student to class
     */
    public function addStudent(int $classId, string $studentNim, ?int $groupId = null): bool
    {
        // Check if already enrolled
        $exists = $this->where('practicum_class_id', $classId)
            ->where('student_nim', $studentNim)
            ->first();

        if ($exists) {
            return false; // Already enrolled
        }

        $now = date('Y-m-d H:i:s');

        return $this->insert([
            'practicum_class_id' => $classId,
            'student_nim'        => $studentNim,
            'group_id'           => $groupId,
            'enrollment_status'  => 'aktif',
            'enrolled_at'        => $now,
            'created_at'         => $now,
            'updated_at'         => $now,
        ]) !== false;
    }

    /**
     * Remove student from class (hard delete)
     */
    public function removeStudent(int $classId, string $studentNim): bool
    {
        return $this->where('practicum_class_id', $classId)
            ->where('student_nim', $studentNim)
            ->delete();
    }

    /**
     * Count students in class
     */
    public function countByClassId(int $classId): int
    {
        return $this->where('practicum_class_id', $classId)->countAllResults();
    }
}
