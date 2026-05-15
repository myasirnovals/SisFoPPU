<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index(): string
    {
        helper('url');

        return view('dashboard/placeholder', [
            'title' => 'Dashboard Mahasiswa',
            'roleLabel' => 'Mahasiswa',
            'dashboardPath' => 'mahasiswa/dashboard',
            'logoutUrl' => site_url('logout'),
            'username' => (string) (session()->get('full_name') ?: session()->get('username') ?: 'Pengguna'),
        ]);
    }
}
