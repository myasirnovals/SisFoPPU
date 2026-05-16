<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    private const ROLE_DASHBOARDS = [
        'admin' => 'admin/dashboard',
        'koordinator' => 'koordinator/dashboard',
        'dosen' => 'dosen/dashboard',
        'asisten' => 'asisten/dashboard',
        'mahasiswa' => 'mahasiswa/dashboard',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $mode = strtolower((string) ($arguments[0] ?? 'protected'));
        $session = session();
        $isLoggedIn = $session->get('is_logged_in') === true;

        if ($mode === 'guest') {
            if (! $isLoggedIn) {
                return null;
            }

            $dashboard = $this->dashboardPath((string) ($session->get('role_active') ?: $session->get('role')));

            if ($dashboard === null) {
                $this->clearAuthSession();

                return redirect()->to(site_url('login'))->with('error', 'Sesi login tidak valid. Silakan masuk kembali.');
            }

            return redirect()->to(site_url($dashboard));
        }

        if (! $isLoggedIn) {
            return redirect()->to(site_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $dashboard = $this->dashboardPath((string) ($session->get('role_active') ?: $session->get('role')));

        if ($dashboard === null) {
            $this->clearAuthSession();

            return redirect()->to(site_url('login'))->with('error', 'Role pengguna tidak valid. Silakan login kembali.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }

    private function dashboardPath(string $role): ?string
    {
        $role = strtolower(trim($role));

        return self::ROLE_DASHBOARDS[$role] ?? null;
    }

    private function clearAuthSession(): void
    {
        $session = session();
        $session->remove([
            'is_logged_in',
            'user_id',
            'username',
            'full_name',
            'role_active',
            'role',
            'roles',
            'role_label',
        ]);
    }
}
