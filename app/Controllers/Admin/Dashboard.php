<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard Admin - Sistem Penilaian Praktikum',
            'total_mk' => 12,
            'kelas_aktif' => 45,
            'total_pengajar' => 60,
            'total_mhs' => 1200,

            'status_nilai' => [
                ['kelas' => 'TI-2A', 'mk' => 'Pemrograman Web', 'progress' => '80%', 'status' => 'In Progress'],
                ['kelas' => 'TI-2B', 'mk' => 'Struktur Data', 'progress' => '100%', 'status' => 'Selesai'],
                ['kelas' => 'TI-2C', 'mk' => 'Jaringan Komputer', 'progress' => '20%', 'status' => 'Belum Lengkap']
            ]
        ];

        return view('admin/dashboard', $data);
    }
}
