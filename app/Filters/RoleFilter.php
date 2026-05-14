<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('is_logged_in')) {
            return redirect()
                ->to('/login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRoles = session()->get('roles') ?? [];

        if (empty($arguments)) {
            return;
        }

        foreach ($arguments as $allowedRole) {
            if (in_array($allowedRole, $userRoles)) {
                return;
            }
        }

        return service('response')
            ->setStatusCode(403)
            ->setBody('403 Forbidden - Anda tidak memiliki akses ke halaman ini.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}