<?php

namespace App\Controllers\Asisten;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index(): string
    {
        helper('url');

        return view('dashboard/placeholder', [
            'title' => 'Dashboard Asisten',
            'roleLabel' => 'Asisten Praktikum',
            'dashboardPath' => 'asisten/dashboard',
            'logoutUrl' => site_url('logout'),
            'username' => (string) (session()->get('full_name') ?: session()->get('username') ?: 'Pengguna'),
        ]);
    }
}
