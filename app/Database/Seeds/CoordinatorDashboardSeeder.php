<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CoordinatorDashboardSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        $courses = [
            ['id' => 1, 'study_program_id' => 1, 'course_code' => 'PRAK-IF101', 'course_name' => 'Praktikum Pemrograman Dasar', 'credits' => 2, 'is_active' => 1],
            ['id' => 2, 'study_program_id' => 1, 'course_code' => 'PRAK-IF205', 'course_name' => 'Praktikum Struktur Data', 'credits' => 2, 'is_active' => 1],
            ['id' => 3, 'study_program_id' => 2, 'course_code' => 'PRAK-SI310', 'course_name' => 'Praktikum Basis Data', 'credits' => 2, 'is_active' => 1],
        ];

        $classes = [
            ['id' => 1, 'course_id' => 1, 'academic_year_id' => 1, 'semester_id' => 1, 'class_code' => 'IF101-A', 'class_name' => 'A', 'lecturer_id' => 1, 'assistant_id' => 1, 'status' => 'active', 'deadline_at' => '2026-05-20'],
            ['id' => 2, 'course_id' => 2, 'academic_year_id' => 1, 'semester_id' => 1, 'class_code' => 'IF205-B', 'class_name' => 'B', 'lecturer_id' => 2, 'assistant_id' => 2, 'status' => 'active', 'deadline_at' => '2026-05-18'],
            ['id' => 3, 'course_id' => 3, 'academic_year_id' => 1, 'semester_id' => 1, 'class_code' => 'SI310-A', 'class_name' => 'A', 'lecturer_id' => 3, 'assistant_id' => 3, 'status' => 'active', 'deadline_at' => '2026-05-16'],
            ['id' => 4, 'course_id' => 1, 'academic_year_id' => 1, 'semester_id' => 1, 'class_code' => 'IF101-B', 'class_name' => 'B', 'lecturer_id' => 1, 'assistant_id' => 4, 'status' => 'active', 'deadline_at' => '2026-05-25'],
            ['id' => 5, 'course_id' => 2, 'academic_year_id' => 1, 'semester_id' => 1, 'class_code' => 'IF205-C', 'class_name' => 'C', 'lecturer_id' => 2, 'assistant_id' => 5, 'status' => 'active', 'deadline_at' => '2026-05-15'],
        ];

        $users = [];
        for ($i = 1; $i <= 50; $i++) {
            $users[] = [
                'id' => $i,
                'student_number' => sprintf('23%05d', $i),
                'full_name' => 'Mahasiswa ' . $i,
                'email' => 'student' . $i . '@example.com',
                'study_program_id' => $i % 2 === 0 ? 1 : 2,
                'is_active' => 1,
            ];
        }

        $tables = [
            'courses' => $courses,
            'practicum_classes' => $classes,
            'users' => $users,
        ];

        foreach ($tables as $table => $rows) {
            if (! $db->tableExists($table)) {
                continue;
            }

            $db->table($table)->insertBatch($rows);
        }
    }
}
