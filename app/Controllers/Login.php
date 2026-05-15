<?php

namespace App\Controllers;

class Login extends BaseController
{
    public function index(): string
    {
        $notice = null;

        if ($this->request->getMethod() === 'post') {
            $notice = 'Halaman login sudah tersedia, tetapi autentikasi backend belum dihubungkan.';
        }

        return view('login', [
            'title' => 'Login Sisfo Praktikum',
            'notice' => $notice,
        ]);
    }
}