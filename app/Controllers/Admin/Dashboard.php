<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\PracticumClassModel;
use App\Models\LecturerModel;
use App\Models\AssistantModel;
use App\Models\StudentModel;

class Dashboard extends BaseController
{
    protected $courseModel;
    protected $practicumClassModel;
    protected $lecturerModel;
    protected $assistantModel;
    protected $studentModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->courseModel = new CourseModel();
        $this->practicumClassModel = new PracticumClassModel();
        $this->lecturerModel = new LecturerModel();
        $this->assistantModel = new AssistantModel();
        $this->studentModel = new StudentModel();
    }

    public function index(): string
    {
        helper('url');

        return view('admin/dashboard', $this->buildDashboardData());
    }

    private function buildDashboardData(): array
    {
        return [
            'title' => 'Dashboard Admin',
            'roleLabel' => 'Admin',
            'logoutUrl' => site_url('logout'),
            'username' => (string) (session()->get('full_name') ?: session()->get('username') ?: 'Administrator'),
            'total_mk' => $this->courseModel->countAll(),
            'kelas_aktif' => $this->practicumClassModel->countActive(),
            'total_pengajar' => $this->lecturerModel->countActive() + $this->assistantModel->countActive(),
            'total_mhs' => $this->studentModel->countActive(),
            'status_nilai' => $this->practicumClassModel->getClassesWithScoreProgress(5),
        ];
    }
}
