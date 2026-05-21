<?php

namespace App\Filters;

use CodeIgniter\Exceptions\PageForbiddenException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class CoordinatorAccessFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        $roles = array_map('strtolower', $this->normalizeValues($session->get('roles') ?? $session->get('role')));
        $permissions = array_map('strtolower', $this->normalizeValues($session->get('permissions')));

        if ($roles === [] && $permissions === []) {
            return null;
        }

        $isCoordinator = in_array('koordinator', $roles, true)
            || in_array('koordinator praktikum', $roles, true)
            || in_array('dashboard.coordinator.view', $permissions, true);
        $isAdmin = in_array('admin', $roles, true);

        if ($isCoordinator && ! $isAdmin) {
            return null;
        }

        throw new PageForbiddenException('Anda tidak memiliki akses ke dashboard koordinator praktikum.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }

    private function normalizeValues(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_string($value)) {
            return array_values(array_filter(array_map('trim', preg_split('/[,|;]/', $value) ?: [])));
        }

        if (! is_array($value)) {
            return [(string) $value];
        }

        $normalized = [];

        foreach ($value as $item) {
            if (is_array($item)) {
                foreach (['role', 'name', 'permission', 'code'] as $key) {
                    if (isset($item[$key]) && $item[$key] !== '') {
                        $normalized[] = (string) $item[$key];
                        continue 2;
                    }
                }

                continue;
            }

            if ($item !== null && $item !== '') {
                $normalized[] = (string) $item;
            }
        }

        return array_values(array_unique($normalized));
    }
}