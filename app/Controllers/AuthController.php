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
        'asisten' => 'asisten/dashboard',
        'mahasiswa' => 'mahasiswa/dashboard',
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
            'login' => [
                'label' => 'Email atau Username',
                'rules' => 'required|min_length[3]',
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

        $login    = trim($this->request->getPost('login'));
        $password = $this->request->getPost('password');

        $user = $this->userModel->findForAuthentication($login);

        if (! $user) {
            $this->activityLogModel->logActivity(
                null,
                'LOGIN_FAILED',
                'Login gagal. Akun tidak ditemukan: ' . $login
            );

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Email/username atau password salah.');
        }

        if (($user['status'] ?? null) !== 'active') {
            $this->activityLogModel->logActivity(
                (int) $user['id'],
                'LOGIN_BLOCKED',
                'Login ditolak karena status akun: ' . (string) ($user['status'] ?? '-')
            );

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Akun Anda tidak aktif atau diblokir.');
        }

        if (! password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            $this->activityLogModel->logActivity(
                (int) $user['id'],
                'LOGIN_FAILED',
                'Login gagal karena password salah.'
            );

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Email/username atau password salah.');
        }

        $roles = $this->userModel->getUserRoleSlugs((int) $user['id']);
        $activeRole = $this->chooseActiveRole($roles);
        $roleLabel = $this->roleLabel($activeRole);

        session()->regenerate();

        session()->set([
            'user_id'      => (int) $user['id'],
            'name'         => (string) ($user['name'] ?? $user['full_name'] ?? $user['username'] ?? ''),
            'full_name'    => (string) ($user['full_name'] ?? $user['name'] ?? $user['username'] ?? ''),
            'username'     => (string) ($user['username'] ?? ''),
            'email'        => (string) ($user['email'] ?? ''),
            'roles'        => $roles,
            'role'         => $activeRole,
            'role_active'  => $activeRole,
            'role_label'   => $roleLabel,
            'is_logged_in' => true,
        ]);

        $this->userModel->touchLastLogin((int) $user['id']);

        $this->activityLogModel->logActivity(
            (int) $user['id'],
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
                (int) $userId,
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
}
