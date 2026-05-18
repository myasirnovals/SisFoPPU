<?php

namespace App\Controllers\Koordinator;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index(): string
    {
        helper('url');

        return view('dashboard/placeholder', [
            'title' => 'Dashboard Koordinator Praktikum',
            'roleLabel' => 'Koordinator Praktikum',
            'dashboardPath' => 'koordinator/dashboard',
            'logoutUrl' => site_url('logout'),
            'username' => (string) (session()->get('full_name') ?: session()->get('username') ?: 'Pengguna'),
        ]);
    }
}
