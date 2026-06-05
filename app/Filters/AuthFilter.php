<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    private const ROLE_DASHBOARDS = [
        'admin' => 'admin/dashboard',
        'koordinator' => 'coordinator/dashboard',
        'dosen' => 'dosen/dashboard',
        'asisten' => 'assistant/dashboard',
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

        // Enforce: authenticated user can only access routes that belong to their active role.
        // Otherwise deny by redirecting to their own dashboard.
        $currentPath = ltrim(trim($request->getUri()->getPath()), '/');
        $currentPath = preg_replace('#^index\.php/#', '', (string) $currentPath);

        if ($currentPath !== '' && str_contains($dashboard, '/')) {
            $allowedPrefix = preg_replace('#/dashboard$#', '', (string) $dashboard); // e.g. "admin" from "admin/dashboard"

            // Ignore login/logout/static assets.
            $routeTarget = strtolower($currentPath);
            if (! in_array($routeTarget, ['login', 'logout'], true)) {
                $currentPrefix = explode('/', $currentPath)[0] ?? '';

                if ($allowedPrefix !== '' && strtolower(trim((string) $currentPrefix)) !== strtolower(trim((string) $allowedPrefix))) {
                    return redirect()->to(site_url($dashboard))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
                }
            }
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
