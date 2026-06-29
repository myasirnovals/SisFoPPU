<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AssessmentTemplateModel;
use App\Models\AssessmentComponentModel;
use App\Models\CourseModel;

class Template extends BaseController
{
    protected $templateModel;
    protected $componentModel;
    protected $courseModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->templateModel = new AssessmentTemplateModel();
        $this->componentModel = new AssessmentComponentModel();
        $this->courseModel = new CourseModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Konfigurasi Template Penilaian - Sisfo Praktikum',
            'courses' => $this->templateModel->getCoursesWithoutTemplate(),
            'templates' => $this->templateModel->getActive(),
        ];

        return view('admin/template_penilaian', $data);
    }

    /**
     * Store new template with components
     */
    public function store()
    {
        $rules = [
            'course_id' => 'required|integer',
            'template_name' => 'required|max_length[150]',
            'template_code' => 'permit_empty|max_length[50]',
            'description' => 'permit_empty',
        ];

        // Validate components
        $componentNames = $this->request->getPost('nama_komponen');
        $componentWeights = $this->request->getPost('bobot');
        $componentTypes = $this->request->getPost('tipe_komponen');

        if (empty($componentNames) || empty($componentWeights)) {
            return redirect()->back()->withInput()->with('error', 'Minimal harus ada 1 komponen penilaian');
        }

        // Validate total weight = 100
        $totalWeight = array_sum(array_map('floatval', $componentWeights));
        if ($totalWeight != 100) {
            return redirect()->back()->withInput()->with('error', "Total bobot harus tepat 100%. Saat ini: {$totalWeight}%");
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $now = date('Y-m-d H:i:s');
            $courseId = $this->request->getPost('course_id');

            // Get course info for template code
            $course = $this->courseModel->find($courseId);
            $templateCode = $this->request->getPost('template_code') ?: 'TPL_' . ($course['course_code'] ?? 'UNK');

            // Prepare template data
            $templateData = [
                'template_code' => $templateCode,
                'template_name' => $this->request->getPost('template_name'),
                'course_id' => $courseId,
                'study_program_id' => $course['study_program_id'] ?? null,
                'description' => $this->request->getPost('description'),
                'is_default' => 0,
                'is_active' => 1,
            ];

            // Prepare components
            $components = [];
            foreach ($componentNames as $index => $name) {
                if (empty($name)) continue;

                $components[] = [
                    'name' => $name,
                    'weight' => floatval($componentWeights[$index] ?? 0),
                    'type' => $componentTypes[$index] ?? 'custom',
                    'max_score' => 100.00,
                ];
            }

            // Save template with components
            $templateId = $this->templateModel->saveTemplateWithComponents($templateData, $components);

            // Log activity
            $db = \Config\Database::connect();
            $db->table('activity_logs')->insert([
                'user_id' => session()->get('user_id') ?? '0000000001',
                'action' => 'create',
                'module' => 'assessment_template',
                'target_type' => 'template',
                'target_id' => $templateId,
                'description' => "Membuat template penilaian: {$templateData['template_name']}",
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'created_at' => $now,
            ]);

            return redirect()->to('/admin/template')->with('success', 'Template penilaian berhasil dibuat!');
        } catch (\Exception $e) {
            log_message('error', '[Template::store] Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal membuat template: ' . $e->getMessage());
        }
    }

    /**
     * Get template detail (AJAX)
     */
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

            return $this->response->setJSON([
                'success' => true,
                'data' => $template,
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete template (soft delete)
     */
    public function delete(int $templateId)
    {
        try {
            $template = $this->templateModel->find($templateId);

            if (!$template) {
                return redirect()->back()->with('error', 'Template tidak ditemukan');
            }

            // Soft delete template
            $this->templateModel->delete($templateId);

            // Soft delete components
            $this->componentModel->deleteByTemplateId($templateId);

            // Log activity
            $db = \Config\Database::connect();
            $db->table('activity_logs')->insert([
                'user_id' => session()->get('user_id') ?? '0000000001',
                'action' => 'delete',
                'module' => 'assessment_template',
                'target_type' => 'template',
                'target_id' => $templateId,
                'description' => "Menghapus template: {$template['template_name']}",
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/template')->with('success', 'Template berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', '[Template::delete] Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus template: ' . $e->getMessage());
        }
    }
}
