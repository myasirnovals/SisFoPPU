<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHurufMutuTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'huruf' => [
                'type'       => 'VARCHAR',
                'constraint' => '2',
            ],
            'batas_bawah' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
            ],
            'batas_atas' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
            ],
            'angka_mutu' => [
                'type'       => 'DECIMAL',
                'constraint' => '3,2',
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
        $this->forge->createTable('huruf_mutu');
    }

    public function down()
    {
        $this->forge->dropTable('huruf_mutu');
    }
}
