<?php

namespace App\Models;

use CodeIgniter\Model;

class AssessmentComponentModel extends Model
{
    protected $table            = 'assessment_components';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'template_id',
        'component_code',
        'component_type',
        'component_name',
        'weight',
        'max_score',
        'sort_order',
        'is_active',
        'allow_subcomponents',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
    protected $useSoftDeletes   = true;

    /**
     * Get components by template ID
     */
    public function getByTemplateId(int $templateId): array
    {
        return $this->where('template_id', $templateId)
            ->where('is_active', 1)
            ->where('deleted_at', null)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    /**
     * Get total weight for a template
     */
    public function getTotalWeight(int $templateId): float
    {
        $result = $this->selectSum('weight', 'total')
            ->where('template_id', $templateId)
            ->where('is_active', 1)
            ->where('deleted_at', null)
            ->first();

        return (float) ($result['total'] ?? 0);
    }

    /**
     * Delete all components for a template
     */
    public function deleteByTemplateId(int $templateId): bool
    {
        return $this->where('template_id', $templateId)
            ->set(['deleted_at' => date('Y-m-d H:i:s')])
            ->update();
    }
}
