<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateScoreTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_terminal' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'is_locked' => [
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
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('score_statuses');

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
            'group_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'student_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'component_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'subcomponent_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'score_value' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => true,
            ],
            'max_score' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => true,
            ],
            'status_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'submitted_by' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'submitted_at' => [
                'type' => 'DATETIME',
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
            'locked_at' => [
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['practicum_class_id', 'student_id', 'component_id', 'subcomponent_id']);
        $this->forge->addKey('practicum_class_id');
        $this->forge->addKey('group_id');
        $this->forge->addKey('student_id');
        $this->forge->addKey('component_id');
        $this->forge->addKey('subcomponent_id');
        $this->forge->addKey('status_id');
        $this->forge->addForeignKey('practicum_class_id', 'practicum_classes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('group_id', 'practicum_groups', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('component_id', 'assessment_components', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('subcomponent_id', 'assessment_subcomponents', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('status_id', 'score_statuses', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('submitted_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('validated_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('score_entries');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'score_entry_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'detail_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'detail_label' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'detail_value' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => true,
            ],
            'weight' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => true,
            ],
            'max_score' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
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
        $this->forge->addKey('score_entry_id');
        $this->forge->addForeignKey('score_entry_id', 'score_entries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('score_details');

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
            'group_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'student_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'final_score' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => true,
            ],
            'grade_letter' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'grade_point' => [
                'type' => 'DECIMAL',
                'constraint' => '4,2',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'submitted', 'reviewed', 'validated', 'locked', 'revision_requested', 'revised'],
                'default'    => 'draft',
            ],
            'validation_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
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
            'locked_at' => [
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['practicum_class_id', 'student_id']);
        $this->forge->addKey('practicum_class_id');
        $this->forge->addKey('group_id');
        $this->forge->addKey('student_id');
        $this->forge->addKey('status');
        $this->forge->addKey('validation_status');
        $this->forge->addForeignKey('practicum_class_id', 'practicum_classes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('group_id', 'practicum_groups', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('validated_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('final_scores');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'score_entry_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'final_score_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'note_type' => [
                'type'       => 'ENUM',
                'constraint' => ['teacher', 'assistant', 'system', 'student'],
                'default'    => 'system',
            ],
            'note_text' => [
                'type' => 'TEXT',
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
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('score_entry_id');
        $this->forge->addKey('final_score_id');
        $this->forge->addForeignKey('score_entry_id', 'score_entries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('final_score_id', 'final_scores', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('score_notes');

        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'score_entry_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'final_score_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'changed_by' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'change_type' => [
                'type'       => 'ENUM',
                'constraint' => ['create', 'update', 'delete', 'input', 'change', 'validate', 'lock', 'request_revision', 'approve_revision'],
            ],
            'old_value' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'new_value' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('score_entry_id');
        $this->forge->addKey('final_score_id');
        $this->forge->addKey('changed_by');
        $this->forge->addKey('change_type');
        $this->forge->addForeignKey('score_entry_id', 'score_entries', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('final_score_id', 'final_scores', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('changed_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('score_change_logs');
    }

    public function down()
    {
        $this->forge->dropTable('score_change_logs', true);
        $this->forge->dropTable('score_notes', true);
        $this->forge->dropTable('final_scores', true);
        $this->forge->dropTable('score_details', true);
        $this->forge->dropTable('score_entries', true);
        $this->forge->dropTable('score_statuses', true);
    }
}
