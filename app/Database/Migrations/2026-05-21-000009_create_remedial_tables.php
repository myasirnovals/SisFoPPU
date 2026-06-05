<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRemedialTables extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('remedial_logs')) {
            $this->forge->dropTable('remedial_logs', true);
        }
        if ($this->db->tableExists('remedial_results')) {
            $this->forge->dropTable('remedial_results', true);
        }
        if ($this->db->tableExists('remedial_scores')) {
            $this->forge->dropTable('remedial_scores', true);
        }
        if ($this->db->tableExists('remedial_components')) {
            $this->forge->dropTable('remedial_components', true);
        }
        if ($this->db->tableExists('remedial_participants')) {
            $this->forge->dropTable('remedial_participants', true);
        }
        if ($this->db->tableExists('remedial_periods')) {
            $this->forge->dropTable('remedial_periods', true);
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'remedial_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'semester_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'registration_deadline' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'active', 'closed', 'archived'],
                'default'    => 'draft',
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('remedial_code');
        $this->forge->addKey('semester_id');
        $this->forge->addForeignKey('semester_id', 'semesters', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('remedial_periods');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'remedial_period_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'student_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'practicum_class_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'final_score_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['eligible', 'terdaftar', 'dijadwalkan', 'sudah_dinilai', 'validated', 'tidak_mengikuti', 'dibatalkan'],
                'default'    => 'eligible',
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'before_score' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => true,
            ],
            'after_score' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => true,
            ],
            'max_after_score' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => true,
            ],
            'validated_by' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'validated_at' => [
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['remedial_period_id', 'student_id', 'practicum_class_id']);
        $this->forge->addKey('remedial_period_id');
        $this->forge->addKey('student_id');
        $this->forge->addKey('practicum_class_id');
        $this->forge->addKey('final_score_id');
        $this->forge->addKey('status');
        // Foreign key ditunda
        // $this->forge->addForeignKey('remedial_period_id', 'remedial_periods', 'id', 'CASCADE', 'CASCADE');
        // $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');
        // $this->forge->addForeignKey('practicum_class_id', 'practicum_classes', 'id', 'CASCADE', 'CASCADE');
        // $this->forge->addForeignKey('final_score_id', 'final_scores', 'id', 'SET NULL', 'CASCADE');
        // $this->forge->addForeignKey('validated_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('remedial_participants');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'remedial_period_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'component_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'max_score_after_remedial' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'default' => '100.00',
            ],
            'weight_adjustment' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'default' => '0.00',
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
        $this->forge->addUniqueKey(['remedial_period_id', 'component_id']);
        $this->forge->addKey('remedial_period_id');
        $this->forge->addKey('component_id');
        $this->forge->addForeignKey('remedial_period_id', 'remedial_periods', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('component_id', 'assessment_components', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('remedial_components');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'remedial_participant_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'remedial_component_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'score_before' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => true,
            ],
            'score_after' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => true,
            ],
            'raw_score' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'entered_by' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'null'       => true,
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
        $this->forge->addKey('remedial_participant_id');
        $this->forge->addKey('remedial_component_id');
        $this->forge->addForeignKey('remedial_participant_id', 'remedial_participants', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('remedial_component_id', 'remedial_components', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('entered_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('remedial_scores');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'remedial_participant_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'final_score_before' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => true,
            ],
            'final_score_after' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => true,
            ],
            'grade_letter_before' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'grade_letter_after' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'is_passed' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'validation_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'validated', 'rejected'],
                'default'    => 'pending',
            ],
            'validated_by' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'validated_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addUniqueKey('remedial_participant_id');
        $this->forge->addKey('validation_status');
        $this->forge->addForeignKey('remedial_participant_id', 'remedial_participants', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('validated_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('remedial_results');

        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'remedial_period_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'remedial_participant_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'event_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('remedial_period_id');
        $this->forge->addKey('remedial_participant_id');
        $this->forge->addKey('event_type');
        $this->forge->addForeignKey('remedial_period_id', 'remedial_periods', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('remedial_participant_id', 'remedial_participants', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('remedial_logs');
    }

    public function down()
    {
        if ($this->db->tableExists('remedial_logs')) {
            $this->forge->dropTable('remedial_logs', true);
        }
        if ($this->db->tableExists('remedial_results')) {
            $this->forge->dropTable('remedial_results', true);
        }
        if ($this->db->tableExists('remedial_scores')) {
            $this->forge->dropTable('remedial_scores', true);
        }
        if ($this->db->tableExists('remedial_components')) {
            $this->forge->dropTable('remedial_components', true);
        }
        if ($this->db->tableExists('remedial_participants')) {
            $this->forge->dropTable('remedial_participants', true);
        }
        if ($this->db->tableExists('remedial_periods')) {
            $this->forge->dropTable('remedial_periods', true);
        }
    }
}
