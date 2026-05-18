<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class HurufMutu extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Pengaturan Huruf Mutu - Sisfo Praktikum'
        ];

        return view('admin/huruf_mutu', $data);
    }
}
