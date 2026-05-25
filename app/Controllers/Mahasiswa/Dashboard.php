<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\StudentDashboardModel;
use CodeIgniter\Exceptions\PageForbiddenException;

class Dashboard extends BaseController
{
    public function index(): string
    {
        if (! $this->canAccessDashboard()) {
            throw new PageForbiddenException('Anda tidak memiliki akses ke dashboard mahasiswa.');
        }

        helper('url');

        $session = session();
        $userId = (string) ($session->get('user_id') ?? '');
        $displayName = (string) ($session->get('full_name') ?: $session->get('name') ?: $session->get('username') ?: 'Mahasiswa');
        $dashboardModel = new StudentDashboardModel();

        return view('mahasiswa/dashboard/index', $dashboardModel->buildDashboardData($userId, $displayName));
    }

    public function detail(int $classId): string
    {
        if (! $this->canAccessDashboard()) {
            throw new PageForbiddenException('Anda tidak memiliki akses ke detail praktikum mahasiswa.');
        }

        helper('url');

        $session = session();
        $userId = (string) ($session->get('user_id') ?? '');
        $dashboardModel = new StudentDashboardModel();

        if (! in_array($classId, $dashboardModel->getStudentClassIds($userId), true)) {
            throw new PageForbiddenException('Anda tidak memiliki akses ke praktikum ini.');
        }

        return view('mahasiswa/dashboard/detail', $dashboardModel->buildDetailData($userId, $classId));
    }

    private function canAccessDashboard(): bool
    {
        $session = session();

        if ($session->get('is_logged_in') !== true) {
            return false;
        }

        $role = strtolower(trim((string) ($session->get('role_active') ?: $session->get('role'))));

        if ($role !== '') {
            return $role === 'mahasiswa';
        }

        $roles = $session->get('roles');
        $roles = is_array($roles) ? $roles : [];
        $roles = array_map(static fn ($item): string => strtolower(trim((string) $item)), $roles);

        return in_array('mahasiswa', $roles, true);
    }
}
