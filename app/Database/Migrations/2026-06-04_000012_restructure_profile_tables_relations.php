<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Restructure tabel profile agar relasi/PK pakai user_nim/user_nid/user_nip.
 * Berdasar requirement:
 * - Mahasiswa & Asisten: user_nim
 * - Dosen & Koordinator: user_nid
 * - Admin: user_nip
 * - Tidak ada kolom `id` dan `user_id` pada tabel profile.
 */
class RestructureProfileTablesRelations extends Migration
{
    // DISABLED: migration ini menyebabkan FK creation gagal.
    public function up()
    {
        // sengaja kosong
        return;

        // Drop old profile tables (data hilang sesuai instruksi DROP RECREATE)
        $this->forge->dropTable('admins', true);
        $this->forge->dropTable('coordinators', true);
        $this->forge->dropTable('lecturers', true);
        $this->forge->dropTable('assistants', true);
        $this->forge->dropTable('students', true);

        // students (Mahasiswa)
        $this->forge->addField([
            'user_nim' => ['type' => 'CHAR', 'constraint' => 10],
            'study_program_id' => ['type' => 'INT', 'unsigned' => true],
            'class_year' => ['type' => 'SMALLINT', 'unsigned' => true, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['aktif','mengulang','mengundurkan_diri','tidak_aktif'], 'default' => 'aktif'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('user_nim', true);
        $this->forge->addKey('study_program_id');
        $this->forge->addForeignKey('study_program_id', 'prodi', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('students');

        // Setelah membuat students, CI4 Forge terkadang menambahkan FK default sebelumnya.



        // assistants (Asisten)
        $this->forge->addField([
            'user_nim' => ['type' => 'CHAR', 'constraint' => 10],
            'study_program_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['aktif','tidak_aktif'], 'default' => 'aktif'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('user_nim', true);
        $this->forge->addKey('study_program_id');
        $this->forge->addForeignKey('study_program_id', 'prodi', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('assistants');


        // lecturers (Dosen)
        $this->forge->addField([
            'user_nid' => ['type' => 'CHAR', 'constraint' => 10],
            'study_program_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['aktif','tidak_aktif'], 'default' => 'aktif'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('user_nid', true);
        $this->forge->addKey('study_program_id');
        $this->forge->addForeignKey('study_program_id', 'prodi', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('lecturers');


        // coordinators (Koordinator)
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

        // admins
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

