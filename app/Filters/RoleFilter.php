<?php

namespace App\Filters;

use CodeIgniter\Exceptions\PageForbiddenException;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $allowedRoles = array_values(array_filter(array_map(
            static fn ($role): string => strtolower(trim((string) $role)),
            is_array($arguments) ? $arguments : []
        )));

        if ($allowedRoles === []) {
            return null;
        }

        $session = session();

        if ($session->get('is_logged_in') !== true) {
            return redirect()->to(site_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $role = strtolower(trim((string) $session->get('role_active')));

        if ($role === '' || ! in_array($role, $allowedRoles, true)) {
            throw new PageForbiddenException('Anda tidak memiliki akses ke halaman ini.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
