<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProfileTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'CHAR',
                'constraint' => 10,
            ],
            'nim' => [
                'type'       => 'CHAR',
                'constraint' => 10,
            ],
            'study_program_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'class_year' => [
                'type'    => 'SMALLINT',
                'unsigned' => true,
                'null'    => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'mengulang', 'mengundurkan_diri', 'tidak_aktif'],
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
        $this->forge->addUniqueKey('user_id');
        $this->forge->addUniqueKey('nim');
        $this->forge->addKey('study_program_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('study_program_id', 'study_programs', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('students');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'CHAR',
                'constraint' => 10,
            ],
            'nim' => [
                'type'       => 'CHAR',
                'constraint' => 10,
            ],
            'study_program_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
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
        $this->forge->addUniqueKey('user_id');
        $this->forge->addUniqueKey('nim');
        $this->forge->addKey('study_program_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('study_program_id', 'study_programs', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('assistants');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'CHAR',
                'constraint' => 10,
            ],
            'nid' => [
                'type'       => 'CHAR',
                'constraint' => 10,
            ],
            'study_program_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
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
        $this->forge->addUniqueKey('user_id');
        $this->forge->addUniqueKey('nid');
        $this->forge->addKey('study_program_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('study_program_id', 'study_programs', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('lecturers');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'CHAR',
                'constraint' => 10,
            ],
            'nid' => [
                'type'       => 'CHAR',
                'constraint' => 10,
            ],
            'study_program_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
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
        $this->forge->addUniqueKey('user_id');
        $this->forge->addUniqueKey('nid');
        $this->forge->addKey('study_program_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('study_program_id', 'study_programs', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('coordinators');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'CHAR',
                'constraint' => 10,
            ],
            'nip' => [
                'type'       => 'CHAR',
                'constraint' => 10,
            ],
            'unit_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'SISFO',
            ],
            'position' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->addUniqueKey('user_id');
        $this->forge->addUniqueKey('nip');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('admins');
    }

    public function down()
    {
        $this->forge->dropTable('admins', true);
        $this->forge->dropTable('coordinators', true);
        $this->forge->dropTable('lecturers', true);
        $this->forge->dropTable('assistants', true);
        $this->forge->dropTable('students', true);
    }
}
