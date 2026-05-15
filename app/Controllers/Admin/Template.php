<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Template extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Template Penilaian - Sisfo Praktikum'
        ];

        return view('admin/template_penilaian', $data);
    }
}
