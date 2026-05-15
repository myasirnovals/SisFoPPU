<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    private const ROLE_DASHBOARDS = [
        'admin' => 'admin/dashboard',
        'koordinator' => 'koordinator/dashboard',
        'dosen' => 'dosen/dashboard',
        'asisten' => 'asisten/dashboard',
        'mahasiswa' => 'mahasiswa/dashboard',
    ];

    private const ROLE_LABELS = [
        'admin' => 'Admin',
        'koordinator' => 'Koordinator Praktikum',
        'dosen' => 'Dosen',
        'asisten' => 'Asisten Praktikum',
        'mahasiswa' => 'Mahasiswa',
    ];

    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        helper(['form', 'url']);

        $session = session();

        if ($session->get('is_logged_in') === true) {
            $role = $this->normalizeRole((string) $session->get('role_active'));

            if ($role !== null) {
                return redirect()->to(site_url($this->dashboardPath($role)));
            }

            $this->flushAuthSession();

            return redirect()->to(site_url('login'))->with('error', 'Sesi login tidak valid. Silakan masuk kembali.');
        }

        $data = [
            'title' => 'Login - Sistem Informasi Penilaian Praktikum',
            'validation' => null,
            'identity' => '',
        ];

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'identity' => [
                    'label' => 'Username atau email',
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Username atau email wajib diisi.',
                    ],
                ],
                'password' => [
                    'label' => 'Password',
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Password wajib diisi.',
                    ],
                ],
            ];

            if (! $this->validate($rules)) {
                $data['validation'] = $this->validator;
                $data['identity'] = trim((string) $this->request->getPost('identity'));

                return view('auth/login', $data);
            }

            $identity = trim((string) $this->request->getPost('identity'));
            $password = (string) $this->request->getPost('password');
            $user = $this->userModel->findForAuthentication($identity);

            if ($user === null || (int) $user['is_active'] !== 1 || ! password_verify($password, (string) $user['password'])) {
                return redirect()->back()->withInput()->with('error', 'Username/email atau password tidak valid.');
            }

            $role = $this->normalizeRole((string) ($user['role'] ?? ''));

            if ($role === null) {
                $this->flushAuthSession();

                return redirect()->back()->withInput()->with('error', 'Akun tidak memiliki role yang valid.');
            }

            session()->regenerate(true);

            $roleLabel = self::ROLE_LABELS[$role] ?? ucfirst($role);

            session()->set([
                'is_logged_in' => true,
                'user_id' => (int) $user['id'],
                'username' => (string) $user['username'],
                'full_name' => (string) $user['full_name'],
                'role_active' => $role,
                'role' => $roleLabel,
                'roles' => [$roleLabel],
                'role_label' => $roleLabel,
            ]);

            $this->userModel->touchLastLogin((int) $user['id']);

            return redirect()->to(site_url($this->dashboardPath($role)));
        }

        return view('auth/login', $data);
    }

    public function logout()
    {
        helper('url');

        $this->flushAuthSession();

        return redirect()->to(site_url('login'))->with('success', 'Anda berhasil logout.');
    }

    private function dashboardPath(string $role): ?string
    {
        $role = $this->normalizeRole($role);

        return $role !== null ? self::ROLE_DASHBOARDS[$role] : null;
    }

    private function normalizeRole(string $role): ?string
    {
        $role = strtolower(trim($role));

        return array_key_exists($role, self::ROLE_DASHBOARDS) ? $role : null;
    }

    private function flushAuthSession(): void
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
        $session->regenerate(true);
    }
}
