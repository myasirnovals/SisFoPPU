<?php

namespace App\Controllers\Assistant;

use App\Controllers\BaseController;
use App\Models\AssistantDashboardModel;
use CodeIgniter\Exceptions\PageForbiddenException;

class DashboardController extends BaseController
{
    public function index(): string
    {
        if (! $this->canAccessDashboard()) {
            throw new PageForbiddenException('Anda tidak memiliki akses ke dashboard asisten praktikum.');
        }

        $session = session();
        $dashboardModel = new AssistantDashboardModel();
        $displayName = (string) ($session->get('full_name') ?: $session->get('name') ?: $session->get('username') ?: 'Asisten');
        $userId = (string) ($session->get('user_id') ?? '');
        $permissions = is_array($session->get('permissions')) ? $session->get('permissions') : [];

        return view('assistant/dashboard/index', $dashboardModel->buildDashboardData($userId, $displayName, $this->collectFilters(), $permissions));
    }

    private function collectFilters(): array
    {
        $month = (int) date('n');

        return [
            'academic_year' => trim((string) ($this->request->getGet('academic_year') ?? '')) ?: ($month >= 7 ? date('Y') . '/' . (date('Y') + 1) : (date('Y') - 1) . '/' . date('Y')),
            'semester' => trim((string) ($this->request->getGet('semester') ?? '')) ?: ($month >= 7 ? 'ganjil' : 'genap'),
            'course_id' => trim((string) ($this->request->getGet('course_id') ?? '')),
            'class_id' => trim((string) ($this->request->getGet('class_id') ?? '')),
            'group_id' => trim((string) ($this->request->getGet('group_id') ?? '')),
            'status_class' => trim((string) ($this->request->getGet('status_class') ?? '')),
            'status_score' => trim((string) ($this->request->getGet('status_score') ?? '')),
            'status_attendance' => trim((string) ($this->request->getGet('status_attendance') ?? '')),
        ];
    }

    private function canAccessDashboard(): bool
    {
        $session = session();

        if ($session->get('is_logged_in') !== true) {
            return false;
        }

        $role = strtolower(trim((string) ($session->get('role_active') ?: $session->get('role'))));

        if ($role !== '') {
            return in_array($role, ['asisten', 'admin'], true);
        }

        $roles = $session->get('roles');
        $roles = is_array($roles) ? $roles : [];
        $roles = array_map(static fn ($item): string => strtolower(trim((string) $item)), $roles);

        return in_array('asisten', $roles, true) || in_array('admin', $roles, true);
    }
}