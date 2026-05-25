<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttendanceTables extends Migration
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
                'constraint' => 30,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('attendance_statuses');

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
            'meeting_no' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'session_date' => [
                'type' => 'DATE',
            ],
            'start_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'end_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'topic' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'scheduled', 'open', 'closed', 'locked', 'cancelled'],
                'default'    => 'draft',
            ],
            'locked_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'updated_by' => [
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['practicum_class_id', 'group_id', 'meeting_no']);
        $this->forge->addKey('practicum_class_id');
        $this->forge->addKey('group_id');
        $this->forge->addKey('status');
        $this->forge->addKey('session_date');
        $this->forge->addForeignKey('practicum_class_id', 'practicum_classes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('group_id', 'practicum_groups', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('attendance_sessions');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'attendance_session_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'student_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'attendance_status_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'marked_by' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'marked_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'previous_attendance_status_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
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
        $this->forge->addUniqueKey(['attendance_session_id', 'student_id']);
        $this->forge->addKey('attendance_session_id');
        $this->forge->addKey('student_id');
        $this->forge->addKey('attendance_status_id');
        $this->forge->addForeignKey('attendance_session_id', 'attendance_sessions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('attendance_status_id', 'attendance_statuses', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('previous_attendance_status_id', 'attendance_statuses', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('marked_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('attendance_records');
    }

    public function down()
    {
        $this->forge->dropTable('attendance_records', true);
        $this->forge->dropTable('attendance_sessions', true);
        $this->forge->dropTable('attendance_statuses', true);
    }
}
