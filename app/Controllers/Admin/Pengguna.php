<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\LecturerModel;
use App\Models\AssistantModel;
use App\Models\StudyProgramModel;
use App\Models\RoleModel;

class Pengguna extends BaseController
{
    protected $userModel;
    protected $studentModel;
    protected $lecturerModel;
    protected $assistantModel;
    protected $studyProgramModel;
    protected $roleModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->userModel = new UserModel();
        $this->studentModel = new StudentModel();
        $this->lecturerModel = new LecturerModel();
        $this->assistantModel = new AssistantModel();
        $this->studyProgramModel = new StudyProgramModel();
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Pengguna - Sisfo Praktikum',
            'studyPrograms' => $this->studyProgramModel->where('status', 'aktif')->findAll(),
            'students' => $this->studentModel->getStudentsWithUserInfo(),
            'lecturers' => $this->lecturerModel->getLecturersWithUserInfo(),
            'assistants' => $this->assistantModel->getAssistantsWithUserInfo(),
        ];

        return view('admin/manajemen_pengguna', $data);
    }

    /**
     * Store new user (Mahasiswa, Dosen, or Asisten)
     */
    public function store()
    {
        $type = $this->request->getPost('user_type');

        $rules = [
            'user_type' => 'required|in_list[mahasiswa,dosen,asisten]',
            'full_name' => 'required|min_length[3]|max_length[150]',
            'email' => 'required|valid_email|max_length[150]',
            'study_program_id' => 'required|integer',
        ];

        if ($type === 'mahasiswa') {
            $rules['nim'] = 'required|exact_length[10]|is_unique[users.login_identifier]';
            $rules['class_year'] = 'required|integer|greater_than[2000]|less_than[2100]';
        } elseif ($type === 'dosen') {
            $rules['nid'] = 'required|exact_length[10]|is_unique[users.login_identifier]';
        } elseif ($type === 'asisten') {
            $rules['nim'] = 'required|exact_length[10]|is_unique[users.login_identifier]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();

        try {
            $now = date('Y-m-d H:i:s');
            $currentUserId = session()->get('user_id') ?? '0000000001';

            if ($type === 'mahasiswa') {
                $identifier = $this->request->getPost('nim');
                $identifierType = 'NIM';
                $roleCode = 'mahasiswa';
                $userId = $identifier;

                $userData = [
                    'id' => $userId,
                    'login_identifier' => $identifier,
                    'identifier_type' => $identifierType,
                    'full_name' => $this->request->getPost('full_name'),
                    'email' => $this->request->getPost('email'),
                    'phone' => $this->request->getPost('phone') ?: null,
                    'password_hash' => password_hash($identifier, PASSWORD_DEFAULT),
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $profileData = [
                    'user_nim' => $userId,
                    'study_program_id' => (int)$this->request->getPost('study_program_id'),
                    'class_year' => (int)$this->request->getPost('class_year'),
                    'status' => 'aktif',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            } elseif ($type === 'dosen') {
                $identifier = $this->request->getPost('nid');
                $identifierType = 'NID';
                $roleCode = 'dosen';
                $userId = $identifier;

                $userData = [
                    'id' => $userId,
                    'login_identifier' => $identifier,
                    'identifier_type' => $identifierType,
                    'full_name' => $this->request->getPost('full_name'),
                    'email' => $this->request->getPost('email'),
                    'phone' => $this->request->getPost('phone') ?: null,
                    'password_hash' => password_hash($identifier, PASSWORD_DEFAULT),
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $profileData = [
                    'user_nid' => $userId,
                    'study_program_id' => (int)$this->request->getPost('study_program_id'),
                    'status' => 'aktif',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            } elseif ($type === 'asisten') {
                $identifier = $this->request->getPost('nim');
                $identifierType = 'NID';
                $roleCode = 'asisten_praktikum';
                $userId = $identifier;

                $userData = [
                    'id' => $userId,
                    'login_identifier' => $identifier,
                    'identifier_type' => $identifierType,
                    'full_name' => $this->request->getPost('full_name'),
                    'email' => $this->request->getPost('email'),
                    'phone' => $this->request->getPost('phone') ?: null,
                    'password_hash' => password_hash($identifier, PASSWORD_DEFAULT),
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $profileData = [
                    'user_nim' => $userId,
                    'study_program_id' => (int)$this->request->getPost('study_program_id'),
                    'status' => 'aktif',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $insertUser = $db->table('users')->insert($userData);
            if (!$insertUser) {
                $dbError = $db->error();
                throw new \Exception('Gagal insert users: ' . ($dbError['message'] ?? 'Unknown error'));
            }

            if ($type === 'mahasiswa') {
                $insertProfile = $db->table('students')->insert($profileData);
            } elseif ($type === 'dosen') {
                $insertProfile = $db->table('lecturers')->insert($profileData);
            } else {
                $insertProfile = $db->table('assistants')->insert($profileData);
            }

            if (!$insertProfile) {
                $dbError = $db->error();
                throw new \Exception('Gagal insert profile: ' . ($dbError['message'] ?? 'Unknown error'));
            }

            $role = $this->roleModel->getByCode($roleCode);
            if (!$role) {
                throw new \Exception("Role '{$roleCode}' tidak ditemukan di database");
            }

            $insertRole = $db->table('user_roles')->insert([
                'user_id' => $userId,
                'role_id' => $role['id'],
                'created_at' => $now,
            ]);

            if (!$insertRole) {
                $dbError = $db->error();
                throw new \Exception('Gagal insert user_roles: ' . ($dbError['message'] ?? 'Unknown error'));
            }

            $db->table('activity_logs')->insert([
                'user_id' => $currentUserId,
                'action' => 'create',
                'module' => 'users',
                'target_type' => 'user',
                'target_id' => $userId,
                'description' => "Menambahkan pengguna baru: {$this->request->getPost('full_name')} ({$identifierType}: {$identifier})",
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'created_at' => $now,
            ]);

            return redirect()->to('/admin/pengguna')->with(
                'success',
                "Pengguna berhasil ditambahkan! ({$identifierType}: {$identifier}, Password default: {$identifier})"
            );
        } catch (\Exception $e) {
            log_message('error', '[Pengguna::store] Error: ' . $e->getMessage());
            log_message('error', '[Pengguna::store] Trace: ' . $e->getTraceAsString());

            return redirect()->back()->withInput()->with(
                'error',
                'Gagal menambahkan pengguna: ' . $e->getMessage()
            );
        }
    }

    // ============================================
    // EDIT METHODS
    // ============================================

    /**
     * Get user data for edit (AJAX)
     */
    public function getUserData(string $userId, string $type)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $db = \Config\Database::connect();

        try {
            // Get user data
            $user = $db->table('users')
                ->where('id', $userId)
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();

            if (!$user) {
                return $this->response->setJSON(['success' => false, 'message' => 'User tidak ditemukan']);
            }

            $data = [
                'id' => $user['id'],
                'login_identifier' => $user['login_identifier'],
                'identifier_type' => $user['identifier_type'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'is_active' => $user['is_active'],
            ];

            // Get profile data based on type
            if ($type === 'mahasiswa') {
                $profile = $db->table('students')
                    ->where('user_nim', $userId)
                    ->where('deleted_at', null)
                    ->get()
                    ->getRowArray();

                if ($profile) {
                    $data['study_program_id'] = $profile['study_program_id'];
                    $data['class_year'] = $profile['class_year'];
                    $data['status'] = $profile['status'];
                }
            } elseif ($type === 'dosen') {
                $profile = $db->table('lecturers')
                    ->where('user_nid', $userId)
                    ->where('deleted_at', null)
                    ->get()
                    ->getRowArray();

                if ($profile) {
                    $data['study_program_id'] = $profile['study_program_id'];
                    $data['status'] = $profile['status'];
                }
            } elseif ($type === 'asisten') {
                $profile = $db->table('assistants')
                    ->where('user_nim', $userId)
                    ->where('deleted_at', null)
                    ->get()
                    ->getRowArray();

                if ($profile) {
                    $data['study_program_id'] = $profile['study_program_id'];
                    $data['status'] = $profile['status'];
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Pengguna::getUserData] Error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update user data
     */
    public function update()
    {
        $userId = $this->request->getPost('user_id');
        $type = $this->request->getPost('user_type');

        if (!$userId || !$type) {
            return redirect()->back()->with('error', 'Data tidak valid');
        }

        $rules = [
            'user_type' => 'required|in_list[mahasiswa,dosen,asisten]',
            'full_name' => 'required|min_length[3]|max_length[150]',
            'email' => 'required|valid_email|max_length[150]',
            'study_program_id' => 'required|integer',
            'is_active' => 'required|in_list[0,1]',
            'status' => 'required|in_list[aktif,tidak_aktif]',
        ];

        if ($type === 'mahasiswa') {
            $rules['class_year'] = 'required|integer|greater_than[2000]|less_than[2100]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();

        try {
            $now = date('Y-m-d H:i:s');
            $currentUserId = session()->get('user_id') ?? '0000000001';

            // Update users table
            $userData = [
                'full_name' => $this->request->getPost('full_name'),
                'email' => $this->request->getPost('email'),
                'phone' => $this->request->getPost('phone') ?: null,
                'is_active' => (int)$this->request->getPost('is_active'),
                'updated_at' => $now,
            ];

            $updateUser = $db->table('users')
                ->where('id', $userId)
                ->update($userData);

            if (!$updateUser) {
                $dbError = $db->error();
                throw new \Exception('Gagal update users: ' . ($dbError['message'] ?? 'Unknown error'));
            }

            // Update profile table
            if ($type === 'mahasiswa') {
                $profileData = [
                    'study_program_id' => (int)$this->request->getPost('study_program_id'),
                    'class_year' => (int)$this->request->getPost('class_year'),
                    'status' => $this->request->getPost('status'),
                    'updated_at' => $now,
                ];
                $updateProfile = $db->table('students')
                    ->where('user_nim', $userId)
                    ->update($profileData);
            } elseif ($type === 'dosen') {
                $profileData = [
                    'study_program_id' => (int)$this->request->getPost('study_program_id'),
                    'status' => $this->request->getPost('status'),
                    'updated_at' => $now,
                ];
                $updateProfile = $db->table('lecturers')
                    ->where('user_nid', $userId)
                    ->update($profileData);
            } else {
                $profileData = [
                    'study_program_id' => (int)$this->request->getPost('study_program_id'),
                    'status' => $this->request->getPost('status'),
                    'updated_at' => $now,
                ];
                $updateProfile = $db->table('assistants')
                    ->where('user_nim', $userId)
                    ->update($profileData);
            }

            if (!$updateProfile) {
                $dbError = $db->error();
                throw new \Exception('Gagal update profile: ' . ($dbError['message'] ?? 'Unknown error'));
            }

            // Log activity
            $db->table('activity_logs')->insert([
                'user_id' => $currentUserId,
                'action' => 'update',
                'module' => 'users',
                'target_type' => 'user',
                'target_id' => $userId,
                'description' => "Mengupdate data pengguna: {$this->request->getPost('full_name')}",
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'created_at' => $now,
            ]);

            return redirect()->to('/admin/pengguna')->with(
                'success',
                "Data pengguna berhasil diperbarui!"
            );
        } catch (\Exception $e) {
            log_message('error', '[Pengguna::update] Error: ' . $e->getMessage());
            log_message('error', '[Pengguna::update] Trace: ' . $e->getTraceAsString());

            return redirect()->back()->withInput()->with(
                'error',
                'Gagal memperbarui pengguna: ' . $e->getMessage()
            );
        }
    }
}
