<?php

namespace App\Models;

use CodeIgniter\Model;

class AssessmentTemplateModel extends Model
{
    protected $table            = 'assessment_templates';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'template_code',
        'template_name',
        'study_program_id',
        'course_id',
        'description',
        'is_default',
        'is_active',
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
     * Get active templates with course info
     */
    public function getActive(): array
    {
        $builder = $this->db->table('assessment_templates at');
        $builder->select([
            'at.id',
            'at.template_code',
            'at.template_name',
            'at.description',
            'at.is_default',
            'at.is_active',
            'c.course_name',
            'c.course_code',
            'sp.program_name',
        ]);
        $builder->join('courses c', 'c.id = at.course_id', 'left');
        $builder->join('study_programs sp', 'sp.id = at.study_program_id', 'left');
        $builder->where('at.is_active', 1);
        $builder->where('at.deleted_at', null);
        $builder->orderBy('at.template_name', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get template with all components
     */
    public function getWithComponents(int $templateId): ?array
    {
        $template = $this->find($templateId);

        if (!$template) {
            return null;
        }

        // Get components
        $components = $this->db->table('assessment_components')
            ->where('template_id', $templateId)
            ->where('deleted_at', null)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();

        $template['components'] = $components;
        $template['total_weight'] = array_sum(array_column($components, 'weight'));

        return $template;
    }

    /**
     * Get courses without template (for dropdown)
     */
    public function getCoursesWithoutTemplate(): array
    {
        $builder = $this->db->table('courses c');
        $builder->select([
            'c.id',
            'c.course_code',
            'c.course_name',
            'c.credits',
        ]);
        $builder->where('c.is_practicum', 1);
        $builder->where('c.status', 'aktif');
        $builder->where('c.deleted_at', null);
        $builder->whereNotIn('c.id', function ($query) {
            $query->select('course_id')
                ->from('assessment_templates')
                ->where('deleted_at', null);
        });
        $builder->orderBy('c.course_name', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Save template with components (transaction)
     */
    public function saveTemplateWithComponents(array $templateData, array $components): int
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $now = date('Y-m-d H:i:s');

            // Insert template
            $templateData['created_at'] = $now;
            $templateData['updated_at'] = $now;

            $templateId = $this->insert($templateData);

            if (!$templateId) {
                throw new \Exception('Gagal menyimpan template');
            }

            // Insert components
            $componentModel = new AssessmentComponentModel();
            $sortOrder = 0;

            foreach ($components as $component) {
                $componentData = [
                    'template_id' => $templateId,
                    'component_code' => $this->generateComponentCode($component['name'], $sortOrder),
                    'component_type' => $component['type'] ?? 'custom',
                    'component_name' => $component['name'],
                    'weight' => $component['weight'],
                    'max_score' => $component['max_score'] ?? 100.00,
                    'sort_order' => $sortOrder,
                    'is_active' => 1,
                    'allow_subcomponents' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $componentModel->insert($componentData);
                $sortOrder++;
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return $templateId;
        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Generate component code
     */
    private function generateComponentCode(string $name, int $index): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 3)) ?: 'CMP';
        return $prefix . '_' . str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT);
    }
}
