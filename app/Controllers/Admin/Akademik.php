<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Akademik extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Data Akademik - Sisfo Praktikum'
        ];

        return view('admin/data_akademik', $data);
    }
}
