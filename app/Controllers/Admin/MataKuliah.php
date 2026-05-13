<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class MataKuliah extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Manajemen Mata Kuliah - Sisfo Praktikum'
        ];

        return view('admin/mata_kuliah', $data);
    }
}
