<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateValidationTables extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('score_revision_requests')) {
            $this->forge->dropTable('score_revision_requests', true);
        }
        if ($this->db->tableExists('validation_logs')) {
            $this->forge->dropTable('validation_logs', true);
        }

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
            'validator_user_id' => [
                'type'       => 'CHAR',
                'constraint' => 10,
            ],
            'action' => [
                'type'       => 'ENUM',
                'constraint' => ['validate', 'approve', 'reject', 'lock', 'unlock'],
            ],
            'result' => [
                'type'       => 'ENUM',
                'constraint' => ['valid', 'invalid', 'locked', 'unlocked'],
                'default'    => 'valid',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'validated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('practicum_class_id');
        $this->forge->addKey('score_entry_id');
        $this->forge->addKey('final_score_id');
        $this->forge->addKey('validator_user_id');
        $this->forge->addKey('action');
        $this->forge->addKey('result');
        $this->forge->addForeignKey('practicum_class_id', 'practicum_classes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('score_entry_id', 'score_entries', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('final_score_id', 'final_scores', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('validator_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('validation_logs');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'final_score_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'requested_by' => [
                'type'       => 'CHAR',
                'constraint' => 10,
            ],
            'requested_to' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'reason' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected', 'implemented', 'cancelled'],
                'default'    => 'pending',
            ],
            'approved_by' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'approved_at' => [
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
        $this->forge->addKey('final_score_id');
        $this->forge->addKey('requested_by');
        $this->forge->addKey('requested_to');
        $this->forge->addKey('approved_by');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('final_score_id', 'final_scores', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('requested_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('requested_to', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('score_revision_requests');
    }

    public function down()
    {
        if ($this->db->tableExists('score_revision_requests')) {
            $this->forge->dropTable('score_revision_requests', true);
        }
        if ($this->db->tableExists('validation_logs')) {
            $this->forge->dropTable('validation_logs', true);
        }
    }
}
