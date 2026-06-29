<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassAssistantModel extends Model
{
    protected $table            = 'class_assistants';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'practicum_class_id',
        'assistant_id',
        'group_id',
        'is_main',
        'duty_note',
        'created_at',
    ];

    // MATIKAN timestamps otomatis
    protected $useTimestamps = false;

    public function getByClassId(int $classId): array
    {
        return $this->where('practicum_class_id', $classId)->findAll();
    }

    public function assignAssistant(int $classId, ?string $assistantId, bool $isMain = true, ?string $dutyNote = null): bool
    {
        // Hapus asisten utama yang ada
        if ($isMain) {
            $this->where('practicum_class_id', $classId)
                ->where('is_main', 1)
                ->delete();
        }

        // Jika tidak ada asisten yang dipilih, cukup hapus
        if (empty($assistantId)) {
            return true;
        }

        return (bool) $this->insert([
            'practicum_class_id' => $classId,
            'assistant_id'       => $assistantId,
            'group_id'           => null,
            'is_main'            => $isMain ? 1 : 0,
            'duty_note'          => $dutyNote,
            'created_at'         => date('Y-m-d H:i:s'),
        ]);
    }
}
