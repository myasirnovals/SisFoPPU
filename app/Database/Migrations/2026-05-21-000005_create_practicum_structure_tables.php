<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePracticumStructureTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'course_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'academic_year_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'semester_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'laboratory_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'template_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'class_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'class_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'aktif', 'selesai', 'terkunci', 'diarsipkan'],
                'default'    => 'draft',
            ],
            'deadline_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('class_code');
        $this->forge->addKey('course_id');
        $this->forge->addKey('academic_year_id');
        $this->forge->addKey('semester_id');
        $this->forge->addKey('laboratory_id');
        $this->forge->addKey('template_id');
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('academic_year_id', 'academic_years', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('semester_id', 'semesters', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('laboratory_id', 'laboratories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('template_id', 'assessment_templates', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('practicum_classes');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'practicum_class_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'group_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'group_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'capacity' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'tidak_aktif'],
                'default'    => 'aktif',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['practicum_class_id', 'group_code']);
        $this->forge->addKey('practicum_class_id');
        $this->forge->addForeignKey('practicum_class_id', 'practicum_classes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('practicum_groups');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'practicum_class_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'student_nim' => [
                'type' => 'CHAR',
                'constraint' => 10,
            ],

            'group_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'enrollment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'drop', 'lulus', 'remedial'],
                'default'    => 'aktif',
            ],
            'enrolled_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['practicum_class_id', 'student_id']);
        $this->forge->addKey('practicum_class_id');
        $this->forge->addKey('student_id');
        $this->forge->addKey('group_id');
        $this->forge->addForeignKey('practicum_class_id', 'practicum_classes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('group_id', 'practicum_groups', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('class_students');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'practicum_class_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'lecturer_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'role_type' => [
                'type'       => 'ENUM',
                'constraint' => ['pengampu', 'koordinator', 'reviewer'],
                'default'    => 'pengampu',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['practicum_class_id', 'lecturer_id', 'role_type']);
        $this->forge->addKey('practicum_class_id');
        $this->forge->addKey('lecturer_id');
        $this->forge->addForeignKey('practicum_class_id', 'practicum_classes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('lecturer_id', 'lecturers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('class_lecturers');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'practicum_class_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'assistant_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'group_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'is_main' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'duty_note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['practicum_class_id', 'assistant_id', 'group_id']);
        $this->forge->addKey('practicum_class_id');
        $this->forge->addKey('assistant_id');
        $this->forge->addKey('group_id');
        $this->forge->addForeignKey('practicum_class_id', 'practicum_classes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assistant_id', 'assistants', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('group_id', 'practicum_groups', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('class_assistants');
    }

    public function down()
    {
        $this->forge->dropTable('class_assistants', true);
        $this->forge->dropTable('class_lecturers', true);
        $this->forge->dropTable('class_students', true);
        $this->forge->dropTable('practicum_groups', true);
        $this->forge->dropTable('practicum_classes', true);
    }
}
