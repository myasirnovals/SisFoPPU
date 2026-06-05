<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAcademicMasterTables extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('courses')) {
            $this->forge->dropTable('courses', true);
        }
        if ($this->db->tableExists('laboratories')) {
            $this->forge->dropTable('laboratories', true);
        }
        if ($this->db->tableExists('semesters')) {
            $this->forge->dropTable('semesters', true);
        }
        if ($this->db->tableExists('academic_years')) {
            $this->forge->dropTable('academic_years', true);
        }
        if ($this->db->tableExists('study_programs')) {
            $this->forge->dropTable('study_programs', true);
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'program_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'program_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'faculty_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'degree_level' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
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
        $this->forge->addUniqueKey('program_code');
        $this->forge->createTable('study_programs');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'year_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'is_active' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
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
        $this->forge->addUniqueKey('year_code');
        $this->forge->createTable('academic_years');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'academic_year_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'semester_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'semester_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'semester_number' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'is_active' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
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
        $this->forge->addUniqueKey(['academic_year_id', 'semester_number']);
        $this->forge->addUniqueKey('semester_code');
        $this->forge->addKey('academic_year_id');
        $this->forge->addForeignKey('academic_year_id', 'academic_years', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('semesters');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'room_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'room_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'location' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'capacity' => [
                'type'       => 'INT',
                'constraint' => 11,
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
        $this->forge->addUniqueKey('room_code');
        $this->forge->createTable('laboratories');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'study_program_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'course_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'course_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'credits' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'is_practicum' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
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
        $this->forge->addUniqueKey('course_code');
        $this->forge->addKey('study_program_id');
        $this->forge->addForeignKey('study_program_id', 'study_programs', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('courses');
    }

    public function down()
    {
        if ($this->db->tableExists('courses')) {
            $this->forge->dropTable('courses', true);
        }
        if ($this->db->tableExists('laboratories')) {
            $this->forge->dropTable('laboratories', true);
        }
        if ($this->db->tableExists('semesters')) {
            $this->forge->dropTable('semesters', true);
        }
        if ($this->db->tableExists('academic_years')) {
            $this->forge->dropTable('academic_years', true);
        }
        if ($this->db->tableExists('study_programs')) {
            $this->forge->dropTable('study_programs', true);
        }
    }
}
