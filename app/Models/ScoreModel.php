<?php

namespace App\Models;

use CodeIgniter\Model;

class ScoreModel extends Model
{
    protected $table = 'score_entries';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'practicum_class_id',
        'group_id',
        'student_id',
        'component_id',
        'subcomponent_id',
        'score_value',
        'submitted_by',
        'submitted_at',
        'notes',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps = true;

    public function saveScore(array $data): bool
    {
        $existing = $this->where([
            'practicum_class_id' => $data['practicum_class_id'],
            'student_id'      => $data['student_id'],
            'component_id'    => $data['component_id'],
            'subcomponent_id' => $data['subcomponent_id'] ?? null,
        ])->first();

        if ($existing) {
            return $this->update($existing['id'], [
                'score_value'   => $data['score_value'],
                'submitted_by'  => $data['submitted_by'] ?? null,
                'submitted_at'  => date('Y-m-d H:i:s'),
                'notes'         => $data['notes'] ?? null,
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        $insertData = [
            'practicum_class_id' => $data['practicum_class_id'],
            'student_id'         => $data['student_id'],
            'component_id'       => $data['component_id'],
            'subcomponent_id'    => $data['subcomponent_id'] ?? null,
            'score_value'        => $data['score_value'],
            'submitted_by'       => $data['submitted_by'] ?? null,
            'submitted_at'       => date('Y-m-d H:i:s'),
            'notes'              => $data['notes'] ?? null,
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        return $this->insert($insertData);
    }
}
