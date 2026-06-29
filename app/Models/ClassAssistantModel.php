<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassAssistantModel extends Model
{
    protected $table = 'class_assistants';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'practicum_class_id',
        'assistant_id',
        'group_id',
        'is_main',
        'duty_note',
        'created_at',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    /**
     * Get assistants for a class
     */
    public function getByClassId(int $classId): array
    {
        return $this->where('practicum_class_id', $classId)->findAll();
    }

    /**
     * Assign assistant to class
     */
    public function assignAssistant(int $classId, string $assistantId, bool $isMain = true, ?string $dutyNote = null): bool
    {
        // Remove existing main assistant
        if ($isMain) {
            $this->where('practicum_class_id', $classId)
                ->where('is_main', 1)
                ->delete();
        }

        return $this->insert([
            'practicum_class_id' => $classId,
            'assistant_id' => $assistantId,
            'is_main' => $isMain ? 1 : 0,
            'duty_note' => $dutyNote,
            'created_at' => date('Y-m-d H:i:s'),
        ]) !== false;
    }
}
