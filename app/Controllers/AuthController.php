<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ActivityLogModel;

class AuthController extends BaseController
{
    protected UserModel $userModel;
    protected ActivityLogModel $activityLogModel;

    public function __construct()
    {
        $this->userModel        = new UserModel();
        $this->activityLogModel = new ActivityLogModel();
    }

    public function login()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('/dashboard');
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

        $user = $this->userModel->findByLogin($login);

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

        if ($user['status'] !== 'active') {
            $this->activityLogModel->logActivity(
                (int) $user['id'],
                'LOGIN_BLOCKED',
                'Login ditolak karena status akun: ' . $user['status']
            );

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Akun Anda tidak aktif atau diblokir.');
        }

        if (! password_verify($password, $user['password_hash'])) {
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

        session()->regenerate();

        session()->set([
            'user_id'      => (int) $user['id'],
            'name'         => $user['name'],
            'username'     => $user['username'],
            'email'        => $user['email'],
            'roles'        => $roles,
            'is_logged_in' => true,
        ]);

        $this->userModel->update($user['id'], [
            'last_login_at' => date('Y-m-d H:i:s'),
        ]);

        $this->activityLogModel->logActivity(
            (int) $user['id'],
            'LOGIN_SUCCESS',
            'User berhasil login.'
        );

        return redirect()->to('/dashboard');
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
}
