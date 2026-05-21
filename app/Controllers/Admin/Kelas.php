<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Kelas extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Manajemen Kelas - Sisfo Praktikum'
        ];

        return view('admin/manajemen_kelas', $data);
    }
}
