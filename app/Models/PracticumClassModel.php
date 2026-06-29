<?php

namespace App\Models;

use CodeIgniter\Model;

class PracticumClassModel extends Model
{
    protected $table = 'practicum_classes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'course_id',
        'academic_year_id',
        'semester_id',
        'laboratory_id',
        'template_id',
        'class_code',
        'class_name',
        'status',
        'deadline_at',
        'description',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $useSoftDeletes = true;

    /**
     * Get all practicum classes with details
     */
    public function getClassesWithDetails(): array
    {
        $builder = $this->db->table('practicum_classes pc');
        $builder->select([
            'pc.id',
            'pc.class_code',
            'pc.class_name',
            'pc.status',
            'pc.created_at',
            'c.course_name',
            'c.course_code',
            'ay.year_code as academic_year',
            's.semester_name',
            'l.room_name',
            'at.template_name',
        ]);
        $builder->join('courses c', 'c.id = pc.course_id', 'left');
        $builder->join('academic_years ay', 'ay.id = pc.academic_year_id', 'left');
        $builder->join('semesters s', 's.id = pc.semester_id', 'left');
        $builder->join('laboratories l', 'l.id = pc.laboratory_id', 'left');
        $builder->join('assessment_templates at', 'at.id = pc.template_id', 'left');
        $builder->where('pc.deleted_at', null);
        $builder->orderBy('pc.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get single class with full details
     */
    public function getClassDetail(int $classId): ?array
    {
        $builder = $this->db->table('practicum_classes pc');
        $builder->select([
            'pc.*',
            'c.course_name',
            'c.course_code',
            'c.credits',
            'ay.year_code',
            's.semester_name',
            's.semester_number',
            'l.room_code',
            'l.room_name',
            'l.capacity as lab_capacity',
            'at.template_name',
            'at.template_code',
        ]);
        $builder->join('courses c', 'c.id = pc.course_id', 'left');
        $builder->join('academic_years ay', 'ay.id = pc.academic_year_id', 'left');
        $builder->join('semesters s', 's.id = pc.semester_id', 'left');
        $builder->join('laboratories l', 'l.id = pc.laboratory_id', 'left');
        $builder->join('assessment_templates at', 'at.id = pc.template_id', 'left');
        $builder->where('pc.id', $classId);
        $builder->where('pc.deleted_at', null);

        return $builder->get()->getRowArray();
    }

    /**
     * Get class members (lecturers, assistants, students)
     */
    public function getClassMembers(int $classId): array
    {
        $db = $this->db;

        // Get lecturers
        $lecturers = $db->table('class_lecturers cl')
            ->select([
                'cl.id',
                'cl.role_type',
                'u.full_name',
                'u.email',
                'l.user_nid',
            ])
            ->join('lecturers l', 'l.user_nid = cl.lecturer_id', 'inner')
            ->join('users u', 'u.id = l.user_nid', 'inner')
            ->where('cl.practicum_class_id', $classId)
            ->get()
            ->getResultArray();

        // Get assistants
        $assistants = $db->table('class_assistants ca')
            ->select([
                'ca.id',
                'ca.is_main',
                'ca.duty_note',
                'u.full_name',
                'u.email',
                'a.user_nim',
            ])
            ->join('assistants a', 'a.user_nim = ca.assistant_id', 'inner')
            ->join('users u', 'u.id = a.user_nim', 'inner')
            ->where('ca.practicum_class_id', $classId)
            ->get()
            ->getResultArray();

        // Get students
        $students = $db->table('class_students cs')
            ->select([
                'cs.id',
                'cs.student_nim',
                'cs.group_id',
                'cs.enrollment_status',
                'u.full_name',
                'u.email',
                'pg.group_name',
                'pg.group_code',
            ])
            ->join('users u', 'u.id = cs.student_nim', 'inner')
            ->join('practicum_groups pg', 'pg.id = cs.group_id', 'left')
            ->where('cs.practicum_class_id', $classId)
            ->get()
            ->getResultArray();

        return [
            'lecturers' => $lecturers,
            'assistants' => $assistants,
            'students' => $students,
            'total_students' => count($students),
        ];
    }

    /**
     * Count students in class
     */
    public function countStudents(int $classId): int
    {
        return $this->db->table('class_students')
            ->where('practicum_class_id', $classId)
            ->countAllResults();
    }
}
