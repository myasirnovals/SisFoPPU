<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSemesterToMataKuliah extends Migration
{
    public function up()
    {
        $this->forge->addColumn('mata_kuliah', [
            'semester' => [
                'type'       => 'ENUM',
                'constraint' => ['Ganjil', 'Genap'],
                'null'       => true,
                'after'      => 'sks',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('mata_kuliah', 'semester');
    }
}
