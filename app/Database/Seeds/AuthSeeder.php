<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AuthSeeder extends Seeder
{
    public function run()
    {
        /*
         * Insert roles
         */
        $roles = [
            [
                'name'        => 'Admin',
                'slug'        => 'admin',
                'description' => 'Mengelola seluruh sistem',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Koordinator Praktikum',
                'slug'        => 'koordinator',
                'description' => 'Memantau dan mengatur kegiatan praktikum',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Dosen',
                'slug'        => 'dosen',
                'description' => 'Mengelola kelas, validasi nilai, dan remedial',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Asisten Praktikum',
                'slug'        => 'asisten',
                'description' => 'Input kehadiran, nilai, dan catatan praktikum',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Mahasiswa',
                'slug'        => 'mahasiswa',
                'description' => 'Melihat nilai, kehadiran, dan status remedial',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('roles')->insertBatch($roles);

        /*
         * Insert admin default
         */
        $this->db->table('users')->insert([
            'name'          => 'Administrator',
            'username'      => 'admin',
            'email'         => 'admin@praktikum.test',
            'password_hash' => password_hash('admin12345', PASSWORD_DEFAULT),
            'status'        => 'active',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        $adminId = $this->db->insertID();

        $adminRole = $this->db->table('roles')
            ->where('slug', 'admin')
            ->get()
            ->getRowArray();

        $this->db->table('user_roles')->insert([
            'user_id'    => $adminId,
            'role_id'    => $adminRole['id'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}