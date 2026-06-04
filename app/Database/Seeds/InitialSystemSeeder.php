<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSystemSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $roles = [
            ['name' => 'Admin SISFO', 'code' => 'admin_sisfo', 'description' => 'Petugas SISFO kampus'],
            ['name' => 'Koordinator Praktikum', 'code' => 'koordinator_praktikum', 'description' => 'Kepala laboratorium / koordinator praktikum'],
            ['name' => 'Dosen', 'code' => 'dosen', 'description' => 'Dosen pengampu praktikum'],
            ['name' => 'Asisten Praktikum', 'code' => 'asisten_praktikum', 'description' => 'Asisten praktikum dan pendamping kelas'],
            ['name' => 'Mahasiswa', 'code' => 'mahasiswa', 'description' => 'Peserta praktikum'],
        ];

        foreach ($roles as $role) {
            $this->upsertByCode('roles', 'code', $role + ['created_at' => $now, 'updated_at' => $now]);
        }

        $permissions = [
            ['name' => 'Lihat dashboard admin', 'code' => 'dashboard.admin.view', 'module' => 'dashboard', 'description' => 'Akses dashboard admin'],
            ['name' => 'Lihat dashboard koordinator', 'code' => 'dashboard.coordinator.view', 'module' => 'dashboard', 'description' => 'Akses dashboard koordinator'],
            ['name' => 'Lihat dashboard dosen', 'code' => 'dashboard.lecturer.view', 'module' => 'dashboard', 'description' => 'Akses dashboard dosen'],
            ['name' => 'Lihat dashboard asisten', 'code' => 'dashboard.assistant.view', 'module' => 'dashboard', 'description' => 'Akses dashboard asisten'],
            ['name' => 'Lihat dashboard mahasiswa', 'code' => 'dashboard.student.view', 'module' => 'dashboard', 'description' => 'Akses dashboard mahasiswa'],
            ['name' => 'Kelola pengguna', 'code' => 'users.manage', 'module' => 'auth', 'description' => 'Mengelola akun dan profil pengguna'],
            ['name' => 'Kelola master akademik', 'code' => 'academic.master.manage', 'module' => 'academic', 'description' => 'Mengelola program studi, tahun akademik, semester, dan kursus'],
            ['name' => 'Kelola kelas praktikum', 'code' => 'practicum.class.manage', 'module' => 'practicum', 'description' => 'Mengelola kelas dan kelompok praktikum'],
            ['name' => 'Kelola kehadiran', 'code' => 'attendance.manage', 'module' => 'attendance', 'description' => 'Input dan koreksi absensi'],
            ['name' => 'Input nilai', 'code' => 'score.input', 'module' => 'score', 'description' => 'Input nilai komponen'],
            ['name' => 'Validasi nilai', 'code' => 'score.validate', 'module' => 'score', 'description' => 'Validasi nilai akhir dan komponen'],
            ['name' => 'Kunci nilai', 'code' => 'score.lock', 'module' => 'score', 'description' => 'Mengunci nilai final'],
            ['name' => 'Kelola revisi nilai', 'code' => 'score.revision.manage', 'module' => 'score', 'description' => 'Request dan approval revisi nilai'],
            ['name' => 'Kelola remedial', 'code' => 'remedial.manage', 'module' => 'remedial', 'description' => 'Mengelola periode dan hasil remedial'],
            ['name' => 'Import data', 'code' => 'import.manage', 'module' => 'import', 'description' => 'Mengimpor data ke sistem'],
            ['name' => 'Export data', 'code' => 'export.manage', 'module' => 'export', 'description' => 'Mengekspor data dari sistem'],
            ['name' => 'Generate laporan', 'code' => 'report.generate', 'module' => 'report', 'description' => 'Membuat laporan dan rekap'],
            ['name' => 'Lihat audit log', 'code' => 'audit.view', 'module' => 'audit', 'description' => 'Melihat log aktivitas dan audit'],
            ['name' => 'Kelola notifikasi', 'code' => 'notification.manage', 'module' => 'notification', 'description' => 'Mengirim dan mengelola notifikasi'],
        ];

        // Mapping permission yang diberikan per role


        foreach ($permissions as $permission) {
            $this->upsertByCode('permissions', 'code', $permission + ['created_at' => $now, 'updated_at' => $now]);
        }

        $rolePermissionMap = [
            'admin_sisfo' => ['dashboard.admin.view', 'users.manage', 'academic.master.manage', 'practicum.class.manage', 'attendance.manage', 'score.input', 'score.validate', 'score.lock', 'score.revision.manage', 'remedial.manage', 'import.manage', 'export.manage', 'report.generate', 'audit.view', 'notification.manage'],
            'koordinator_praktikum' => ['dashboard.coordinator.view', 'practicum.class.manage', 'attendance.manage', 'score.validate', 'score.lock', 'score.revision.manage', 'remedial.manage', 'report.generate', 'audit.view'],
            'dosen' => ['dashboard.lecturer.view', 'score.input', 'score.validate', 'score.revision.manage', 'report.generate'],
            'asisten_praktikum' => ['dashboard.assistant.view', 'attendance.manage', 'score.input', 'report.generate'],
            'mahasiswa' => ['dashboard.student.view', 'notification.manage'],
        ];

        $roleRows = $this->db->table('roles')->select('id, code')->get()->getResultArray();
        $permissionRows = $this->db->table('permissions')->select('id, code')->get()->getResultArray();
        $roleIds = [];
        $permissionIds = [];

        foreach ($roleRows as $row) {
            $roleIds[(string) $row['code']] = (int) $row['id'];
        }

        foreach ($permissionRows as $row) {
            $permissionIds[(string) $row['code']] = (int) $row['id'];
        }

        foreach ($rolePermissionMap as $roleCode => $permissionCodes) {
            $roleId = $roleIds[$roleCode] ?? null;
            if ($roleId === null) {
                continue;
            }

            foreach ($permissionCodes as $permissionCode) {
                $permissionId = $permissionIds[$permissionCode] ?? null;
                if ($permissionId === null) {
                    continue;
                }

                $this->upsertComposite('role_permissions', ['role_id' => $roleId, 'permission_id' => $permissionId], ['created_at' => $now]);
            }
        }

        $attendanceStatuses = [
            ['code' => 'hadir', 'name' => 'Hadir', 'description' => 'Mahasiswa hadir tepat waktu', 'is_default' => 1],
            ['code' => 'izin', 'name' => 'Izin', 'description' => 'Mahasiswa izin resmi', 'is_default' => 0],
            ['code' => 'sakit', 'name' => 'Sakit', 'description' => 'Mahasiswa sakit', 'is_default' => 0],
            ['code' => 'alfa', 'name' => 'Alfa', 'description' => 'Mahasiswa tidak hadir tanpa keterangan', 'is_default' => 0],
            ['code' => 'susulan', 'name' => 'Susulan', 'description' => 'Absensi pengganti', 'is_default' => 0],
        ];

        foreach ($attendanceStatuses as $status) {
            $this->upsertByCode('attendance_statuses', 'code', $status + ['is_active' => 1, 'created_at' => $now, 'updated_at' => $now]);
        }

        $scoreStatuses = [
            ['code' => 'draft', 'name' => 'Draft', 'description' => 'Nilai belum final', 'is_terminal' => 0, 'is_locked' => 0, 'is_default' => 1],
            ['code' => 'submitted', 'name' => 'Submitted', 'description' => 'Nilai sudah dikirim', 'is_terminal' => 0, 'is_locked' => 0, 'is_default' => 0],
            ['code' => 'reviewed', 'name' => 'Reviewed', 'description' => 'Nilai telah ditinjau', 'is_terminal' => 0, 'is_locked' => 0, 'is_default' => 0],
            ['code' => 'validated', 'name' => 'Validated', 'description' => 'Nilai telah divalidasi', 'is_terminal' => 1, 'is_locked' => 0, 'is_default' => 0],
            ['code' => 'locked', 'name' => 'Locked', 'description' => 'Nilai terkunci', 'is_terminal' => 1, 'is_locked' => 1, 'is_default' => 0],
            ['code' => 'revision_requested', 'name' => 'Revision Requested', 'description' => 'Ada permintaan revisi nilai', 'is_terminal' => 0, 'is_locked' => 0, 'is_default' => 0],
            ['code' => 'revised', 'name' => 'Revised', 'description' => 'Nilai sudah direvisi', 'is_terminal' => 0, 'is_locked' => 0, 'is_default' => 0],
        ];

        foreach ($scoreStatuses as $status) {
            $this->upsertByCode('score_statuses', 'code', $status + ['is_active' => 1, 'created_at' => $now, 'updated_at' => $now]);
        }

        $gradeScales = [
            ['scale_code' => 'A', 'grade_letter' => 'A', 'min_score' => 85, 'max_score' => 100, 'grade_point' => 4.00, 'predicate' => 'Sangat Baik', 'description' => 'Lulus sangat baik', 'is_passing' => 1, 'is_default' => 1, 'is_active' => 1],
            ['scale_code' => 'B', 'grade_letter' => 'B', 'min_score' => 70, 'max_score' => 84.99, 'grade_point' => 3.00, 'predicate' => 'Baik', 'description' => 'Lulus', 'is_passing' => 1, 'is_default' => 0, 'is_active' => 1],
            ['scale_code' => 'C', 'grade_letter' => 'C', 'min_score' => 60, 'max_score' => 69.99, 'grade_point' => 2.00, 'predicate' => 'Cukup', 'description' => 'Lulus minimal', 'is_passing' => 1, 'is_default' => 0, 'is_active' => 1],
            ['scale_code' => 'D', 'grade_letter' => 'D', 'min_score' => 50, 'max_score' => 59.99, 'grade_point' => 1.00, 'predicate' => 'Kurang', 'description' => 'Tidak lulus', 'is_passing' => 0, 'is_default' => 0, 'is_active' => 1],
            ['scale_code' => 'E', 'grade_letter' => 'E', 'min_score' => 0, 'max_score' => 49.99, 'grade_point' => 0.00, 'predicate' => 'Sangat Kurang', 'description' => 'Tidak lulus', 'is_passing' => 0, 'is_default' => 0, 'is_active' => 1],
        ];

        foreach ($gradeScales as $scale) {
            $this->upsertByCode('grade_scales', 'scale_code', $scale + ['created_at' => $now, 'updated_at' => $now]);
        }

            // Dummy user untuk setiap role (hanya 1 user per role) + isi tabel detail role
        $dummyUsersByRole = [
            [
                'role_code' => 'admin_sisfo',
                'identifier_type' => 'NIP',
                'login_identifier' => '0000000001',
                'password' => 'admin12345',
                'full_name' => 'Administrator SISFO',
                'email' => 'sisfo@example.edu',
                'detail_table' => 'admins',
                'detail_code_field' => 'nip',
                'detail' => [
                    'unit_name' => 'SISFO',
                    'position' => 'Petugas SISFO',
                    'status' => 'aktif',
                    'deleted_at' => null,
                ],
                'detail_code_value' => '0000000001',
            ],
            [
                'role_code' => 'koordinator_praktikum',
                'identifier_type' => 'NIP',
                'login_identifier' => '0000000002',
                'password' => 'koordinator12345',
                'full_name' => 'Koordinator Praktikum',
                'email' => 'koordinator@example.edu',
                'detail_table' => 'coordinators',
                'detail_code_field' => 'nid',
                'detail' => [
                    'study_program_id' => 1,
                    'status' => 'aktif',
                    'deleted_at' => null,
                ],
                'detail_code_value' => '0000000002',
            ],
            [
                'role_code' => 'dosen',
                'identifier_type' => 'NIP',
                'login_identifier' => '0000000003',
                'password' => 'dosen12345',
                'full_name' => 'Dosen Pengampu',
                'email' => 'dosen@example.edu',
                'detail_table' => 'lecturers',
                'detail_code_field' => 'nid',
                'detail' => [
                    'study_program_id' => 1,
                    'status' => 'aktif',
                    'deleted_at' => null,
                ],
                'detail_code_value' => '0000000003',
            ],
            [
                'role_code' => 'asisten_praktikum',
                'identifier_type' => 'NID',
                'login_identifier' => '0000000004',
                'password' => 'asisten12345',
                'full_name' => 'Asisten Praktikum',
                'email' => 'asisten@example.edu',
                'detail_table' => 'assistants',
                'detail_code_field' => 'nim',
                'detail' => [
                    'study_program_id' => 1,
                    'status' => 'aktif',
                    'deleted_at' => null,
                ],
                'detail_code_value' => '0000000004',
            ],
            [
                'role_code' => 'mahasiswa',
                'identifier_type' => 'NIM',
                'login_identifier' => '0000000005',
                'password' => 'mahasiswa12345',
                'full_name' => 'Mahasiswa Praktikum',
                'email' => 'mahasiswa@example.edu',
                'detail_table' => 'students',
                'detail_code_field' => 'nim',
                'detail' => [
                    'study_program_id' => 1,
                    'class_year' => 2024,
                    'status' => 'aktif',
                    'deleted_at' => null,
                ],
                'detail_code_value' => '0000000005',
            ],
        ];


        foreach ($dummyUsersByRole as $dummy) {
            $roleCode = (string) $dummy['role_code'];
            $roleId = $roleIds[$roleCode] ?? null;
            if ($roleId === null) {
                continue;
            }

            $identifier = (string) $dummy['login_identifier'];
            $user = $this->db->table('users')->where('login_identifier', $identifier)->get()->getRowArray();

            if ($user === null) {
                $this->db->table('users')->insert([
                    'id' => $identifier,
                    'login_identifier' => $identifier,
                    'identifier_type' => $dummy['identifier_type'],
                    'password_hash' => password_hash($dummy['password'], PASSWORD_DEFAULT),
                    'full_name' => $dummy['full_name'],
                    'email' => $dummy['email'],
                    'phone' => null,
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ]);
                $userId = $identifier;
            } else {
                $userId = (string) $user['id'];
                $this->db->table('users')->where('id', $userId)->update([
                    'identifier_type' => $dummy['identifier_type'],
                    'password_hash' => password_hash($dummy['password'], PASSWORD_DEFAULT),
                    'full_name' => $dummy['full_name'],
                    'email' => $dummy['email'],
                    'is_active' => 1,
                    'updated_at' => $now,
                ]);
            }

            $this->upsertComposite('user_roles', ['user_id' => (string) $userId, 'role_id' => (int) $roleId], ['created_at' => $now]);

            // Tabel detail role (admins/coordinators/lecturers/assistants/students)
            $detailTable = $dummy['detail_table'];
            $detailCodeField = $dummy['detail_code_field'];
            $detailCodeValue = (string) $dummy['detail_code_value'];

            // Pastikan field upsertByCode sesuai dengan kolom unik di tabelnya.
            // Dari migration: coordinators pakai UNIQUE 'nip', admins pakai UNIQUE 'nip' (tapi di seeder sebelumnya ada ketidaksesuaian).
            // Gunakan mapping spesifik agar tidak error.
            // Mapping kolom unik per tabel sesuai dengan skema DB yang sedang dipakai.
            $uniqueFieldByTable = [
                'admins' => 'nip',
                'coordinators' => 'nid',
                'lecturers' => 'nid',
                'assistants' => 'nim',
                'students' => 'nim',
            ];

            $uniqueCodeField = $uniqueFieldByTable[$detailTable] ?? $detailCodeField;


            $this->upsertByCode($detailTable, $uniqueCodeField, array_merge(
                $dummy['detail'],
                [
                    'user_id' => (string) $userId,
                    $uniqueCodeField => $detailCodeValue,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            ));
        }
    }

    private function upsertByCode(string $table, string $codeField, array $data): void
    {
        $existing = $this->db->table($table)->where($codeField, $data[$codeField])->get()->getRowArray();

        if ($existing === null) {
            $this->db->table($table)->insert($data);
            return;
        }

        $this->db->table($table)->where('id', $existing['id'])->update($data);
    }

    private function upsertComposite(string $table, array $where, array $data): void
    {
        $builder = $this->db->table($table);
        foreach ($where as $field => $value) {
            $builder->where($field, $value);
        }

        $existing = $builder->get()->getRowArray();

        if ($existing === null) {
            $this->db->table($table)->insert($where + $data);
            return;
        }

        $this->db->table($table)->where('id', $existing['id'])->update($data);
    }
}
