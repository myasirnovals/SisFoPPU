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

    // MATIKAN VALIDASI BAWAAN
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;

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
            'mk.nama_mk as course_name',
            'mk.kode_mk as course_code',
            'sp.program_name',
        ]);
        $builder->join('mata_kuliah mk', 'mk.id = at.course_id', 'left');
        $builder->join('study_programs sp', 'sp.id = at.study_program_id', 'left');
        $builder->where('at.is_active', 1);
        $builder->where('at.deleted_at', null);
        $builder->orderBy('at.template_name', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function getWithComponents(int $templateId): ?array
    {
        $template = $this->find($templateId);
        if (!$template) {
            return null;
        }

        $components = $this->db->table('assessment_components')
            ->where('template_id', $templateId)
            ->where('deleted_at', null)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();

        $template['components']   = $components;
        $template['total_weight'] = array_sum(array_column($components, 'weight'));

        return $template;
    }

    public function getCoursesWithoutTemplate(): array
    {
        $builder = $this->db->table('mata_kuliah mk');
        $builder->select([
            'mk.id',
            'mk.kode_mk as course_code',
            'mk.nama_mk as course_name',
            'mk.sks as credits',
        ]);
        $builder->whereNotIn('mk.id', function ($query) {
            $query->select('course_id')
                ->from('assessment_templates')
                ->where('deleted_at', null);
        });
        $builder->orderBy('mk.nama_mk', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function saveTemplateWithComponents(array $templateData, array $components): int
    {
        $db = \Config\Database::connect();

        $db->transBegin();

        try {
            $now = date('Y-m-d H:i:s');

            $templateData['created_at'] = $now;
            $templateData['updated_at'] = $now;

            // DEBUG
            log_message('debug', 'Inserting template: ' . json_encode($templateData));

            $templateId = $this->insert($templateData);

            if (!$templateId) {
                $errors = $this->errors();
                $errorMsg = is_array($errors) ? implode(', ', $errors) : 'Unknown validation error';
                throw new \Exception('Gagal insert template: ' . $errorMsg);
            }

            $componentModel = new AssessmentComponentModel();
            $sortOrder = 0;
            $usedCodes = [];

            foreach ($components as $component) {
                $baseCode = $this->generateComponentCode($component['name'], $sortOrder);
                $componentCode = $baseCode;
                $suffix = 1;

                while (in_array($componentCode, $usedCodes)) {
                    $componentCode = $baseCode . '_' . $suffix;
                    $suffix++;
                }
                $usedCodes[] = $componentCode;

                $componentData = [
                    'template_id'         => $templateId,
                    'component_code'      => $componentCode,
                    'component_type'      => $component['type'] ?? 'custom',
                    'component_name'      => $component['name'],
                    'weight'              => $component['weight'],
                    'max_score'           => $component['max_score'] ?? 100.00,
                    'sort_order'          => $sortOrder,
                    'is_active'           => 1,
                    'allow_subcomponents' => 0,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ];

                $componentId = $componentModel->insert($componentData);

                if (!$componentId) {
                    $errors = $componentModel->errors();
                    $errorMsg = is_array($errors) ? implode(', ', $errors) : 'Unknown validation error';
                    throw new \Exception('Gagal insert komponen "' . $component['name'] . '": ' . $errorMsg);
                }

                $sortOrder++;
            }

            $db->transCommit();

            return (int) $templateId;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'saveTemplateWithComponents: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateComponentCode(string $name, int $index): string
    {
        $clean = preg_replace('/[^a-zA-Z]/', '', $name);
        $prefix = strtoupper(substr($clean, 0, 3)) ?: 'CMP';
        return $prefix . '_' . str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT);
    }
}
