<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssessmentTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'template_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'template_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'study_program_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'course_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_default' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        $this->forge->addUniqueKey('template_code');
        $this->forge->addKey('study_program_id');
        $this->forge->addKey('course_id');
        $this->forge->addForeignKey('study_program_id', 'study_programs', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('assessment_templates');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'template_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'component_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'component_type' => [
                'type'       => 'ENUM',
                'constraint' => ['kehadiran', 'tugas', 'modul', 'laporan', 'kuis', 'responsi', 'uts', 'uas', 'proyek', 'presentasi', 'sikap', 'custom'],
            ],
            'component_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'weight' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'default'    => '0.00',
            ],
            'max_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'default'    => '100.00',
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'allow_subcomponents' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
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
        $this->forge->addUniqueKey(['template_id', 'component_code']);
        $this->forge->addKey('template_id');
        $this->forge->addForeignKey('template_id', 'assessment_templates', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('assessment_components');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'component_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'subcomponent_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'subcomponent_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'weight' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'default'    => '0.00',
            ],
            'max_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'default'    => '100.00',
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        $this->forge->addUniqueKey(['component_id', 'subcomponent_code']);
        $this->forge->addKey('component_id');
        $this->forge->addForeignKey('component_id', 'assessment_components', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('assessment_subcomponents');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'scale_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'grade_letter' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'min_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
            ],
            'max_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
            ],
            'grade_point' => [
                'type'       => 'DECIMAL',
                'constraint' => '4,2',
                'default'    => '0.00',
            ],
            'predicate' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_passing' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'is_default' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        $this->forge->addUniqueKey('scale_code');
        $this->forge->addKey('grade_letter');
        $this->forge->createTable('grade_scales');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'course_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'template_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'min_final_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'default'    => '0.00',
            ],
            'min_attendance_percent' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '0.00',
            ],
            'min_component_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'default'    => '0.00',
            ],
            'allow_remedial' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'notes' => [
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('course_id');
        $this->forge->addKey('template_id');
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('template_id', 'assessment_templates', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('passing_rules');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'course_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'template_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'min_score_for_remedial' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'default'    => '0.00',
            ],
            'max_remedial_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'default'    => '100.00',
            ],
            'require_approval' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'notes' => [
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('course_id');
        $this->forge->addKey('template_id');
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('template_id', 'assessment_templates', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('remedial_rules');
    }

    public function down()
    {
        $this->forge->dropTable('remedial_rules', true);
        $this->forge->dropTable('passing_rules', true);
        $this->forge->dropTable('grade_scales', true);
        $this->forge->dropTable('assessment_subcomponents', true);
        $this->forge->dropTable('assessment_components', true);
        $this->forge->dropTable('assessment_templates', true);
    }
}
