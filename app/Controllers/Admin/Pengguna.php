<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Pengguna extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Manajemen Pengguna - Sisfo Praktikum'
        ];

        return view('admin/manajemen_pengguna', $data);
    }
}
