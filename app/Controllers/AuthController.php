<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ActivityLogModel;

class AuthController extends BaseController
{
    protected UserModel $userModel;
    protected ActivityLogModel $activityLogModel;

    private const ROLE_DASHBOARD_PRIORITY = [
        'admin' => 'admin/dashboard',
        'koordinator' => 'coordinator/dashboard',
        'dosen' => 'dosen/dashboard',
        'asisten' => 'assistant/dashboard',
        'mahasiswa' => 'mahasiswa/dashboard',
    ];

    private const ROLE_CODE_TO_ALIAS = [
        'admin_sisfo' => 'admin',
        'koordinator_praktikum' => 'koordinator',
        'dosen' => 'dosen',
        'asisten_praktikum' => 'asisten',
        'mahasiswa' => 'mahasiswa',
    ];

    public function __construct()
    {
        $this->userModel        = new UserModel();
        $this->activityLogModel = new ActivityLogModel();
    }

    public function login()
    {
        if (session()->get('is_logged_in')) {
            $dashboard = $this->dashboardPath((string) (session()->get('role_active') ?: session()->get('role')));

            return redirect()->to(site_url($dashboard ?? 'dashboard'));
        }

        return view('auth/login');
    }

    public function attemptLogin()
    {
        $rules = [
            'identity' => [
                'label' => 'Login Identifier',
                'rules' => 'required|regex_match[/^\d{10}$/]',
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[6]',
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $identity = trim((string) (
            $this->request->getPost('identity')
            ?? $this->request->getPost('login')
            ?? ''
        ));
        $password = $this->request->getPost('password');

        $user = $this->userModel->findForAuthentication($identity);

        if (! $user) {
            $this->activityLogModel->logActivity(
                null,
                'LOGIN_FAILED',
                'Login gagal. Akun tidak ditemukan: ' . $identity
            );

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'NIM/NID atau password salah.');
        }

        if ((int) ($user['is_active'] ?? 0) !== 1) {
            $this->activityLogModel->logActivity(
                (string) $user['id'],
                'LOGIN_BLOCKED',
                'Login ditolak karena akun nonaktif.'
            );

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Akun Anda tidak aktif atau diblokir.');
        }

        $identity = trim((string) (
            $this->request->getPost('identity')
            ?? $this->request->getPost('login')
            ?? ''
        ));

        if (! password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            $this->activityLogModel->logActivity(
                (string) $user['id'],
                'LOGIN_FAILED',
                'Login gagal karena password salah.'
            );

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'NIM/NID atau password salah.');
        }

        $roleCodes = $this->normalizeRoles((array) ($user['role_codes'] ?? []));
        $roles = $this->mapRoleCodesToAliases($roleCodes);
        $activeRole = $this->chooseActiveRole($roles);

        if ($activeRole === '') {
            $this->activityLogModel->logActivity(
                (string) $user['id'],
                'LOGIN_BLOCKED',
                'Login ditolak karena role pengguna tidak valid/terpetakan.'
            );

            session()->destroy();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Role pengguna tidak valid.');
        }

        $roleLabel = $this->roleLabel($activeRole);
        $activeRoleCode = $this->roleCodeFromAlias($activeRole, $roleCodes);

        session()->regenerate();

        session()->set([

            'user_id'      => (string) $user['id'],
            'login_identifier' => (string) ($user['login_identifier'] ?? $identity),
            'identifier_type' => (string) ($user['identifier_type'] ?? ''),
            'identity'     => $identity,
            'name'         => (string) ($user['full_name'] ?? ''),
            'full_name'    => (string) ($user['full_name'] ?? ''),
            'username'     => (string) ($user['login_identifier'] ?? ''),
            'email'        => (string) ($user['email'] ?? ''),
            'roles'        => $roles,
            'role_codes'   => $roleCodes,
            'role'         => $activeRole,
            'role_active'  => $activeRole,
            'role_code'    => $activeRoleCode,
            'role_label'   => $roleLabel,
            'is_logged_in' => true,
        ]);

        $this->userModel->touchLastLogin((string) $user['id']);

        $this->activityLogModel->logActivity(
            (string) $user['id'],
            'LOGIN_SUCCESS',
            'User berhasil login.'
        );

        $dashboard = $this->dashboardPath($activeRole);

        return redirect()->to(site_url($dashboard ?? 'dashboard'));
    }

    public function logout()
    {
        $userId = session()->get('user_id');

        if ($userId) {
            $this->activityLogModel->logActivity(
                (string) $userId,
                'LOGOUT',
                'User logout dari sistem.'
            );
        }

        session()->destroy();

        return redirect()
            ->to('/login')
            ->with('success', 'Anda berhasil logout.');
    }

    private function dashboardPath(string $role): ?string
    {
        $role = strtolower(trim($role));

        return self::ROLE_DASHBOARD_PRIORITY[$role] ?? null;
    }

    private function chooseActiveRole(array $roles): string
    {
        $roles = array_values(array_filter(array_map(static fn ($role): string => strtolower(trim((string) $role)), $roles)));

        foreach (array_keys(self::ROLE_DASHBOARD_PRIORITY) as $preferredRole) {
            if (in_array($preferredRole, $roles, true)) {
                return $preferredRole;
            }
        }

        return $roles[0] ?? '';
    }

    private function mapRoleCodesToAliases(array $roleCodes): array
    {
        $aliases = [];

        foreach ($roleCodes as $roleCode) {
            $aliases[] = self::ROLE_CODE_TO_ALIAS[strtolower(trim((string) $roleCode))] ?? strtolower(trim((string) $roleCode));
        }

        return array_values(array_unique(array_filter($aliases)));
    }

    private function roleCodeFromAlias(string $alias, array $roleCodes): string
    {
        $alias = strtolower(trim($alias));

        foreach ($roleCodes as $roleCode) {
            $mappedAlias = self::ROLE_CODE_TO_ALIAS[strtolower(trim((string) $roleCode))] ?? strtolower(trim((string) $roleCode));

            if ($mappedAlias === $alias) {
                return (string) $roleCode;
            }
        }

        return $roleCodes[0] ?? '';
    }

    private function roleLabel(string $role): string
    {
        return match (strtolower(trim($role))) {
            'admin' => 'Admin',
            'koordinator' => 'Koordinator Praktikum',
            'dosen' => 'Dosen',
            'asisten' => 'Asisten Praktikum',
            'mahasiswa' => 'Mahasiswa',
            default => 'Pengguna',
        };
    }

    /**
     * Menormalkan array role codes yang didapat dari database.
     * * @param array $roles
     * @return array
     */
    private function normalizeRoles(array $roles): array
    {
        // Pastikan format role seragam, misalnya menghapus spasi ekstra 
        // dan memastikan bentuknya array of strings yang valid.
        // Sesuaikan logika ini jika bentuk array dari getUserRoleSlugs() berbeda (misal array of objects).
        
        return array_filter(array_map('trim', $roles));
    }
}
