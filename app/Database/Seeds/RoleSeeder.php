<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $roles = [
            ['name' => 'admin', 'label' => 'Admin'],
            ['name' => 'koordinator', 'label' => 'Koordinator Praktikum'],
            ['name' => 'dosen', 'label' => 'Dosen'],
            ['name' => 'asisten', 'label' => 'Asisten Praktikum'],
            ['name' => 'mahasiswa', 'label' => 'Mahasiswa'],
        ];

        foreach ($roles as $role) {
            $builder = $this->db->table('roles');
            $existing = $builder->where('name', $role['name'])->get()->getRowArray();

            if ($existing === null) {
                $builder->insert([
                    'name' => $role['name'],
                    'label' => $role['label'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                continue;
            }

            $builder->where('id', $existing['id'])->update([
                'label' => $role['label'],
                'updated_at' => $now,
            ]);
        }
    }
}
