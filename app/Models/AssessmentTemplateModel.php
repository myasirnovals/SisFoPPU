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
     * Get templates by course
     */
    public function getByCourse(int $courseId): array
    {
        return $this->where('course_id', $courseId)
            ->where('is_active', 1)
            ->where('deleted_at', null)
            ->findAll();
    }
}
