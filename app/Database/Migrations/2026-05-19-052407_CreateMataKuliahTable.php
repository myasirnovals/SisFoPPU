<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMataKuliahTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('mata_kuliah')) {
            $this->forge->dropTable('mata_kuliah', true);
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_mk' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'unique'     => true,
            ],
            'nama_mk' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'sks' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'prodi_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        $this->forge->addForeignKey('prodi_id', 'prodi', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('mata_kuliah');
    }

    public function down()
    {
        if ($this->db->tableExists('mata_kuliah')) {
            $this->forge->dropTable('mata_kuliah', true);
        }
    }
}

