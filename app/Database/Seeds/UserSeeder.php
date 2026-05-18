<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $this->call(RoleSeeder::class);

        $now = date('Y-m-d H:i:s');
        $passwordHash = password_hash('password123', PASSWORD_DEFAULT);

        $roleRow = $this->db->table('roles')->where('name', 'admin')->get()->getRowArray();

        if ($roleRow === null) {
            throw new \RuntimeException('Role admin tidak ditemukan. Jalankan RoleSeeder terlebih dahulu.');
        }

        $userBuilder = $this->db->table('users');
        $existingUser = $userBuilder->groupStart()
            ->where('username', 'admin')
            ->orWhere('email', 'admin@praktikum.test')
            ->groupEnd()
            ->get()
            ->getRowArray();

        if ($existingUser === null) {
            $userBuilder->insert([
                'username' => 'admin',
                'email' => 'admin@praktikum.test',
                'password' => $passwordHash,
                'full_name' => 'Administrator Sistem',
                'is_active' => 1,
                'last_login' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
            $userId = (int) $this->db->insertID();
        } else {
            $userId = (int) $existingUser['id'];

            $userBuilder->where('id', $userId)->update([
                'username' => 'admin',
                'email' => 'admin@praktikum.test',
                'password' => $passwordHash,
                'full_name' => 'Administrator Sistem',
                'is_active' => 1,
                'updated_at' => $now,
            ]);
        }

        $pivotBuilder = $this->db->table('user_roles');
        $existingPivot = $pivotBuilder->where('user_id', $userId)->where('role_id', $roleRow['id'])->get()->getRowArray();

        if ($existingPivot === null) {
            $pivotBuilder->insert([
                'user_id' => $userId,
                'role_id' => $roleRow['id'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            return;
        }

        $pivotBuilder->where('id', $existingPivot['id'])->update([
            'updated_at' => $now,
        ]);
    }
}
