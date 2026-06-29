<?php

namespace App\Models;

use CodeIgniter\Model;

class PracticumClassModel extends Model
{
    protected $table            = 'practicum_classes';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
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

    // AKTIFKAN timestamps karena tabel punya kolom lengkap
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
    protected $useSoftDeletes   = true;

    /**
     * Get all practicum classes with details + lecturer + assistant names
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
            'mk.nama_mk as course_name',
            'mk.kode_mk as course_code',
            'ay.year_code as academic_year',
            's.semester_name',
            'l.room_name',
            'at.template_name',
        ]);
        $builder->join('mata_kuliah mk', 'mk.id = pc.course_id', 'left');
        $builder->join('academic_years ay', 'ay.id = pc.academic_year_id', 'left');
        $builder->join('semesters s', 's.id = pc.semester_id', 'left');
        $builder->join('laboratories l', 'l.id = pc.laboratory_id', 'left');
        $builder->join('assessment_templates at', 'at.id = pc.template_id', 'left');
        $builder->where('pc.deleted_at', null);
        $builder->orderBy('pc.created_at', 'DESC');

        $classes = $builder->get()->getResultArray();

        // Ambil dosen & asisten untuk setiap kelas
        foreach ($classes as &$class) {
            $class['lecturer_name']  = $this->getMainLecturerName($class['id']);
            $class['assistant_name'] = $this->getMainAssistantName($class['id']);
        }

        return $classes;
    }

    /**
     * Get main lecturer name for a class
     */
    public function getMainLecturerName(int $classId): string
    {
        $builder = $this->db->table('class_lecturers cl');
        $builder->select('u.full_name');
        $builder->join('users u', 'u.id = cl.lecturer_id', 'left');
        $builder->where('cl.practicum_class_id', $classId);
        $builder->where('cl.role_type', 'pengampu');
        $builder->orderBy('cl.id', 'DESC');
        $result = $builder->get()->getRowArray();

        return $result['full_name'] ?? '-';
    }

    /**
     * Get main assistant name for a class
     */
    public function getMainAssistantName(int $classId): string
    {
        $builder = $this->db->table('class_assistants ca');
        $builder->select('u.full_name');
        $builder->join('users u', 'u.id = ca.assistant_id', 'left');
        $builder->where('ca.practicum_class_id', $classId);
        $builder->where('ca.is_main', 1);
        $builder->orderBy('ca.id', 'DESC');
        $result = $builder->get()->getRowArray();

        return $result['full_name'] ?? '-';
    }

    /**
     * Get single class with full details
     */
    public function getClassDetail(int $classId): ?array
    {
        $builder = $this->db->table('practicum_classes pc');
        $builder->select([
            'pc.*',
            'mk.nama_mk as course_name',
            'mk.kode_mk as course_code',
            'mk.sks as credits',
            'ay.year_code',
            's.semester_name',
            's.semester_number',
            'l.room_code',
            'l.room_name',
            'l.capacity as lab_capacity',
            'at.template_name',
            'at.template_code',
        ]);
        $builder->join('mata_kuliah mk', 'mk.id = pc.course_id', 'left');
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
     * FIXED: Gunakan LEFT JOIN agar tidak hilang kalau user tidak ditemukan
     */
    public function getClassMembers(int $classId): array
    {
        $db = $this->db;

        // Get lecturers — LEFT JOIN agar aman
        $lecturers = $db->table('class_lecturers cl')
            ->select([
                'cl.id',
                'cl.role_type',
                'cl.lecturer_id as user_nid',
                'u.full_name',
                'u.email',
            ])
            ->join('users u', 'u.id = cl.lecturer_id', 'left')  // LEFT JOIN!
            ->where('cl.practicum_class_id', $classId)
            ->get()
            ->getResultArray();

        // Get assistants — LEFT JOIN agar aman
        $assistants = $db->table('class_assistants ca')
            ->select([
                'ca.id',
                'ca.is_main',
                'ca.duty_note',
                'ca.assistant_id as user_nim',
                'u.full_name',
                'u.email',
            ])
            ->join('users u', 'u.id = ca.assistant_id', 'left')  // LEFT JOIN!
            ->where('ca.practicum_class_id', $classId)
            ->get()
            ->getResultArray();

        // Get students — LEFT JOIN agar aman
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
            ->join('users u', 'u.id = cs.student_nim', 'left')  // LEFT JOIN!
            ->join('practicum_groups pg', 'pg.id = cs.group_id', 'left')
            ->where('cs.practicum_class_id', $classId)
            ->get()
            ->getResultArray();

        return [
            'lecturers'      => $lecturers,
            'assistants'     => $assistants,
            'students'       => $students,
            'total_students' => count($students),
        ];
    }

    public function countStudents(int $classId): int
    {
        return $this->db->table('class_students')
            ->where('practicum_class_id', $classId)
            ->countAllResults();
    }

    public function countActive(): int
    {
        return $this->where('status', 'aktif')
            ->where('deleted_at', null)
            ->countAllResults();
    }

    public function getClassesWithScoreProgress(int $limit = 5): array
    {
        $builder = $this->db->table('practicum_classes pc');
        $builder->select([
            'pc.id',
            'pc.class_name',
            'pc.class_code',
            'pc.status',
            'mk.nama_mk as course_name',
            'mk.kode_mk as course_code',
        ]);
        $builder->join('mata_kuliah mk', 'mk.id = pc.course_id', 'left');
        $builder->where('pc.deleted_at', null);
        $builder->whereIn('pc.status', ['aktif', 'selesai', 'draft']);
        $builder->orderBy('pc.created_at', 'DESC');
        $builder->limit($limit);

        $classes = $builder->get()->getResultArray();

        foreach ($classes as &$class) {
            $classId = $class['id'];

            $totalStudents = $this->db->table('class_students')
                ->where('practicum_class_id', $classId)
                ->countAllResults();

            $studentsWithScores = $this->db->table('final_scores')
                ->where('practicum_class_id', $classId)
                ->countAllResults();

            $validatedScores = $this->db->table('final_scores')
                ->where('practicum_class_id', $classId)
                ->whereIn('validation_status', ['approved', 'validated'])
                ->countAllResults();

            if ($totalStudents > 0) {
                $scoreProgress = $studentsWithScores > 0
                    ? (int) round(($validatedScores / $totalStudents) * 100)
                    : 0;
            } else {
                $scoreProgress = 0;
            }

            $class['total_students']       = $totalStudents;
            $class['students_with_scores'] = $studentsWithScores;
            $class['validated_scores']     = $validatedScores;
            $class['progress']             = $scoreProgress;
            $class['progress_display']     = $scoreProgress . '%';

            if ($scoreProgress >= 100) {
                $class['status_label'] = 'Selesai';
                $class['status_badge'] = 'bg-success';
            } elseif ($scoreProgress >= 50) {
                $class['status_label'] = 'In Progress';
                $class['status_badge'] = 'bg-warning text-dark';
            } else {
                $class['status_label'] = 'Menunggu';
                $class['status_badge'] = 'bg-danger';
            }
        }

        return $classes;
    }
}
