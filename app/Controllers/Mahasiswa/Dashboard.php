<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\StudentDashboardModel;

class Dashboard extends BaseController
{
    private StudentDashboardModel $dash;
    private int $sid; // student id dari session
    // ─── initController: dijalankan sebelum method apapun ─────────────────
    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->dash = new StudentDashboardModel();
        // Sesuaikan key session dengan AuthController Anda
        // Biasanya: 'student_id', 'user_id', atau 'id'
        $this->sid = (int) session()->get('student_id');
    }
    // ─── Helper: data profil + tahun akademik (dipakai semua halaman) ──────
    private function base(): array
    {
        return $this->dash->getBaseData($this->sid);
        // Mengembalikan: studentProfile, academicYear, semesterLabel
    }
    // ─── 1. Ringkasan / Dashboard Utama ────────────────────────────────────
    public function index(): string
    {
        $data = array_merge($this->base(), [
            'summaryCards' => $this->dash->getSummaryCards($this->sid),
            'summaryMeta'  => $this->dash->getSummaryMeta($this->sid),
            'activeMenu'   => 'ringkasan',
        ]);
        return view('mahasiswa/dashboard/index', $data);
    }
    // ─── 2. Halaman Daftar Praktikum ───────────────────────────────────────
    public function praktikum(): string
    {
        $data = array_merge($this->base(), [
            'classRows'  => $this->dash->getClassRows($this->sid),
            'activeMenu' => 'praktikum',
        ]);
        return view('mahasiswa/dashboard/praktikum', $data);
    }
    // ─── 3. Halaman Kehadiran ──────────────────────────────────────────────
    public function kehadiran(): string
    {
        $data = array_merge($this->base(), [
            'attendanceRows' => $this->dash->getAttendanceRows($this->sid),
            'activeMenu'     => 'kehadiran',
        ]);
        return view('mahasiswa/dashboard/kehadiran', $data);
    }
    // ─── 4. Halaman Nilai ──────────────────────────────────────────────────
    public function nilai(): string
    {
        $data = array_merge($this->base(), [
            'scoreRows'  => $this->dash->getScoreRows($this->sid),
            'activeMenu' => 'nilai',
        ]);
        return view('mahasiswa/dashboard/nilai', $data);
    }
    // ─── 5. Halaman Remedial ───────────────────────────────────────────────
    public function remedial(): string
    {
        $data = array_merge($this->base(), [
            'remedialRows' => $this->dash->getRemedialRows($this->sid),
            'activeMenu'   => 'remedial',
        ]);
        return view('mahasiswa/dashboard/remedial', $data);
    }
    // ─── 6. Halaman Notifikasi ─────────────────────────────────────────────
    public function notifikasi(): string
    {
        $data = array_merge($this->base(), [
            'notifications' => $this->dash->getNotifications($this->sid),
            'activeMenu'    => 'notifikasi',
        ]);
        return view('mahasiswa/dashboard/notifikasi', $data);
    }
}
