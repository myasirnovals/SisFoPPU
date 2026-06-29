<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassLecturerModel extends Model
{
    protected $table = 'class_lecturers';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'practicum_class_id',
        'lecturer_id',
        'role_type',
        'created_at',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    /**
     * Get lecturers for a class
     */
    public function getByClassId(int $classId): array
    {
        return $this->where('practicum_class_id', $classId)->findAll();
    }

    /**
     * Assign lecturer to class
     */
    public function assignLecturer(int $classId, string $lecturerId, string $roleType = 'pengampu'): bool
    {
        // Remove existing lecturer with same role
        $this->where('practicum_class_id', $classId)
            ->where('role_type', $roleType)
            ->delete();

        return $this->insert([
            'practicum_class_id' => $classId,
            'lecturer_id' => $lecturerId,
            'role_type' => $roleType,
            'created_at' => date('Y-m-d H:i:s'),
        ]) !== false;
    }
}
