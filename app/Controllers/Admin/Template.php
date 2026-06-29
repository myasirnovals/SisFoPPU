<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AssessmentTemplateModel;
use App\Models\AssessmentComponentModel;
use App\Models\MataKuliahModel;
use App\Models\CourseModel;

class Template extends BaseController
{
    protected $templateModel;
    protected $componentModel;
    protected $mataKuliahModel;
    protected $courseModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->templateModel   = new AssessmentTemplateModel();
        $this->componentModel  = new AssessmentComponentModel();
        $this->mataKuliahModel = new MataKuliahModel();
        $this->courseModel     = new CourseModel();
    }

    public function index()
    {
        $this->syncMataKuliahToCourses();

        $data = [
            'title'     => 'Konfigurasi Template Penilaian - Sisfo Praktikum',
            'courses'   => $this->templateModel->getCoursesWithoutTemplate(),
            'templates' => $this->templateModel->getActive(),
        ];

        return view('admin/template_penilaian', $data);
    }

    /**
     * Dapatkan prodi_id default (pastikan ada di tabel prodi)
     */
    private function getDefaultProdiId(): int
    {
        $db = \Config\Database::connect();

        // Cari prodi pertama
        $prodi = $db->table('prodi')->select('id')->limit(1)->get()->getRow();

        if ($prodi) {
            return (int) $prodi->id;
        }

        // Jika tabel prodi kosong, buat default
        $now = date('Y-m-d H:i:s');
        $db->table('prodi')->insert([
            'kode_prodi' => 'DEFAULT',
            'nama_prodi' => 'Program Studi Default',
            'jenjang'    => 'S1',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return (int) $db->insertID();
    }

    /**
     * Sync mata_kuliah ke courses dengan prodi_id yang valid
     */
    private function syncMataKuliahToCourses(): void
    {
        $db = \Config\Database::connect();
        $defaultProdiId = $this->getDefaultProdiId();

        // Update mata_kuliah yang prodi_id-nya NULL
        $db->query("UPDATE mata_kuliah SET prodi_id = ? WHERE prodi_id IS NULL OR prodi_id = 0", [$defaultProdiId]);

        // Sync ke courses
        $sql = "INSERT INTO courses (study_program_id, course_code, course_name, credits, is_practicum, status, created_at, updated_at)
                SELECT 
                    COALESCE(m.prodi_id, ?),
                    m.kode_mk,
                    m.nama_mk,
                    m.sks,
                    1,
                    'aktif',
                    COALESCE(m.created_at, NOW()),
                    COALESCE(m.updated_at, NOW())
                FROM mata_kuliah m
                LEFT JOIN courses c ON c.course_code = m.kode_mk
                WHERE c.id IS NULL";

        try {
            $db->query($sql, [$defaultProdiId]);
        } catch (\Exception $e) {
            log_message('error', 'syncMataKuliahToCourses error: ' . $e->getMessage());
        }
    }

    /**
     * Dapatkan course_id yang valid
     */
    private function getValidCourseId(int $mataKuliahId): ?int
    {
        $this->syncMataKuliahToCourses();

        $mk = $this->mataKuliahModel->find($mataKuliahId);
        if (!$mk) {
            return null;
        }

        $course = $this->courseModel->where('course_code', $mk['kode_mk'])->first();

        if (!$course) {
            $defaultProdiId = $this->getDefaultProdiId();
            $now = date('Y-m-d H:i:s');

            $courseId = $this->courseModel->insert([
                'study_program_id' => !empty($mk['prodi_id']) ? (int) $mk['prodi_id'] : $defaultProdiId,
                'course_code'      => $mk['kode_mk'],
                'course_name'      => $mk['nama_mk'],
                'credits'          => $mk['sks'],
                'is_practicum'     => 1,
                'status'           => 'aktif',
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            if (!$courseId) {
                log_message('error', 'Failed to insert course for kode_mk: ' . $mk['kode_mk']);
                return null;
            }

            return (int) $courseId;
        }

        return (int) $course['id'];
    }

    public function store()
    {
        $rules = [
            'course_id'      => 'required|integer',
            'template_name'  => 'required|max_length[150]',
            'template_code'  => 'permit_empty|max_length[50]',
            'description'    => 'permit_empty',
        ];

        $componentNames   = $this->request->getPost('nama_komponen');
        $componentWeights = $this->request->getPost('bobot');
        $componentTypes   = $this->request->getPost('tipe_komponen');

        if (empty($componentNames) || empty($componentWeights)) {
            return redirect()->back()->withInput()->with('error', 'Minimal harus ada 1 komponen penilaian');
        }

        $totalWeight = array_sum(array_map('floatval', $componentWeights));
        if (abs($totalWeight - 100) > 0.01) {
            return redirect()->back()->withInput()->with('error', "Total bobot harus tepat 100%. Saat ini: {$totalWeight}%");
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();

        try {
            $now           = date('Y-m-d H:i:s');
            $mataKuliahId  = (int) $this->request->getPost('course_id');

            $validCourseId = $this->getValidCourseId($mataKuliahId);
            if (!$validCourseId) {
                return redirect()->back()->withInput()->with('error', 'Gagal menemukan/membuat data course yang valid');
            }

            $mk = $this->mataKuliahModel->find($mataKuliahId);
            if (!$mk) {
                return redirect()->back()->withInput()->with('error', 'Mata kuliah tidak ditemukan (ID: ' . $mataKuliahId . ')');
            }

            // Pastikan study_program_id valid
            $defaultProdiId = $this->getDefaultProdiId();
            $studyProgramId = !empty($mk['prodi_id']) ? (int) $mk['prodi_id'] : $defaultProdiId;

            $templateCode = trim($this->request->getPost('template_code'));
            if (empty($templateCode)) {
                $templateCode = 'TPL_' . strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $mk['kode_mk'])) . '_' . date('YmdHis');
            }

            // Cek duplikat
            $existing = $db->table('assessment_templates')
                ->where('template_code', $templateCode)
                ->where('deleted_at', null)
                ->get()
                ->getRow();

            if ($existing) {
                $templateCode .= '_' . uniqid();
            }

            // Insert template pakai query builder
            $templateData = [
                'template_code'     => $templateCode,
                'template_name'     => $this->request->getPost('template_name'),
                'course_id'         => $validCourseId,
                'study_program_id'  => $studyProgramId,
                'description'       => $this->request->getPost('description') ?? '',
                'is_default'        => 0,
                'is_active'         => 1,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];

            $db->table('assessment_templates')->insert($templateData);
            $templateId = $db->insertID();

            if (!$templateId) {
                throw new \Exception('Gagal insert template: ' . $db->error()['message']);
            }

            // Insert components
            $sortOrder = 0;
            $usedCodes = [];

            foreach ($componentNames as $index => $name) {
                if (empty(trim($name))) continue;

                $baseCode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 3)) ?: 'CMP';
                $componentCode = $baseCode . '_' . str_pad((string)($sortOrder + 1), 2, '0', STR_PAD_LEFT);

                $suffix = 1;
                while (in_array($componentCode, $usedCodes)) {
                    $componentCode = $baseCode . '_' . str_pad((string)($sortOrder + 1), 2, '0', STR_PAD_LEFT) . '_' . $suffix;
                    $suffix++;
                }
                $usedCodes[] = $componentCode;

                $componentData = [
                    'template_id'         => $templateId,
                    'component_code'      => $componentCode,
                    'component_type'      => $componentTypes[$index] ?? 'custom',
                    'component_name'      => trim($name),
                    'weight'              => floatval($componentWeights[$index] ?? 0),
                    'max_score'           => 100.00,
                    'sort_order'          => $sortOrder,
                    'is_active'           => 1,
                    'allow_subcomponents' => 0,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ];

                $db->table('assessment_components')->insert($componentData);

                if (!$db->insertID()) {
                    throw new \Exception('Gagal insert komponen: ' . $db->error()['message']);
                }

                $sortOrder++;
            }

            // Log activity
            $db->table('activity_logs')->insert([
                'user_id'     => session()->get('user_id') ?? '0000000001',
                'action'      => 'create',
                'module'      => 'assessment_template',
                'target_type' => 'template',
                'target_id'   => $templateId,
                'description' => "Membuat template penilaian: " . $templateData['template_name'],
                'ip_address'  => $this->request->getIPAddress(),
                'user_agent'  => $this->request->getUserAgent()->getAgentString(),
                'created_at'  => $now,
            ]);

            return redirect()->to('/admin/template')->with('success', 'Template penilaian berhasil dibuat!');
        } catch (\Exception $e) {
            log_message('error', '[Template::store] ERROR: ' . $e->getMessage());
            log_message('error', '[Template::store] FILE: ' . $e->getFile() . ' LINE: ' . $e->getLine());
            return redirect()->back()->withInput()->with('error', 'Gagal membuat template: ' . $e->getMessage());
        }
    }

    public function getTemplateDetail(int $templateId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        try {
            $template = $this->templateModel->getWithComponents($templateId);
            if (!$template) {
                return $this->response->setJSON(['success' => false, 'message' => 'Template tidak ditemukan']);
            }
            return $this->response->setJSON(['success' => true, 'data' => $template]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function delete(int $templateId)
    {
        try {
            $template = $this->templateModel->find($templateId);
            if (!$template) {
                return redirect()->back()->with('error', 'Template tidak ditemukan');
            }

            $this->templateModel->delete($templateId);
            $this->componentModel->deleteByTemplateId($templateId);

            $db = \Config\Database::connect();
            $db->table('activity_logs')->insert([
                'user_id'     => session()->get('user_id') ?? '0000000001',
                'action'      => 'delete',
                'module'      => 'assessment_template',
                'target_type' => 'template',
                'target_id'   => $templateId,
                'description' => "Menghapus template: {$template['template_name']}",
                'ip_address'  => $this->request->getIPAddress(),
                'user_agent'  => $this->request->getUserAgent()->getAgentString(),
                'created_at'  => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/template')->with('success', 'Template berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', '[Template::delete] Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus template: ' . $e->getMessage());
        }
    }
}
