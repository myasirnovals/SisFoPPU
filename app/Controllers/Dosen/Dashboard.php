<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index(): string
    {
        helper('url');

        return view('dashboard/placeholder', [
            'title' => 'Dashboard Dosen',
            'roleLabel' => 'Dosen',
            'dashboardPath' => 'dosen/dashboard',
            'logoutUrl' => site_url('logout'),
            'username' => (string) (session()->get('full_name') ?: session()->get('username') ?: 'Pengguna'),
        ]);
    }
}
