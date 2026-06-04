<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Versi tanpa foreign key agar migrate tidak gagal karena constraint FK.
 * Setelah skema FK sudah dipakai penuh, Anda bisa menambahkan kembali foreign key
 * secara terkontrol.
 */
class RestructureProfileTablesRelationsNoFK extends Migration
{
    public function up()
    {
        $this->forge->dropTable('assistants', true);
        $this->forge->dropTable('lecturers', true);
        $this->forge->dropTable('students', true);
        $this->forge->dropTable('coordinators', true);
        $this->forge->dropTable('admins', true);

        $this->forge->addField([
            'user_nim' => ['type' => 'CHAR', 'constraint' => 10],
            'study_program_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'class_year' => ['type' => 'SMALLINT', 'unsigned' => true, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['aktif','mengulang','mengundurkan_diri','tidak_aktif'], 'default' => 'aktif'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('user_nim', true);
        $this->forge->createTable('students');

        $this->forge->addField([
            'user_nim' => ['type' => 'CHAR', 'constraint' => 10],
            'study_program_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['aktif','tidak_aktif'], 'default' => 'aktif'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('user_nim', true);
        $this->forge->createTable('assistants');

        $this->forge->addField([
            'user_nid' => ['type' => 'CHAR', 'constraint' => 10],
            'study_program_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['aktif','tidak_aktif'], 'default' => 'aktif'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('user_nid', true);
        $this->forge->createTable('lecturers');

        $this->forge->addField([
            'user_nid' => ['type' => 'CHAR', 'constraint' => 10],
            'unit_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'SISFO'],
            'position' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['aktif','tidak_aktif'], 'default' => 'aktif'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('user_nid', true);
        $this->forge->createTable('coordinators');

        $this->forge->addField([
            'user_nip' => ['type' => 'CHAR', 'constraint' => 10],
            'unit_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'SISFO'],
            'position' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['aktif','tidak_aktif'], 'default' => 'aktif'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('user_nip', true);
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

