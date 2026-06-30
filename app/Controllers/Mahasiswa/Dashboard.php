<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\StudentDashboardModel;

class Dashboard extends BaseController
{
    private StudentDashboardModel $dash;
    private string $userNim;  // NIM dari session (users.id)

    /**
     * initController: dijalankan sebelum method apapun
     */
    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);

        $this->dash = new StudentDashboardModel();

        // Ambil NIM dari session. Setelah login, AuthController menyimpan 'user_id' (NIM)
        // Bisa juga 'nim' atau 'id' tergantung AuthController Anda
        $session = session();
        $this->userNim = (string) ($session->get('user_id') ?? $session->get('nim') ?? $session->get('id') ?? '');

        // Redirect ke login jika tidak ada session
        if ($this->userNim === '') {
            $session->setFlashdata('error', 'Silakan login terlebih dahulu.');
            // redirect()->to('login'); // Uncomment jika perlu redirect
        }
    }

    /**
     * Helper: data profil + tahun akademik (dipakai semua halaman)
     */
    private function base(): array
    {
        $displayName = session()->get('full_name') ?? '';
        return $this->dash->getBaseData($this->userNim, $displayName);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  HALAMAN DASHBOARD
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * 1. Ringkasan / Dashboard Utama
     */
    public function index(): string
    {
        $data = array_merge($this->base(), [
            'summaryCards' => $this->dash->getSummaryCards($this->userNim),
            'summaryMeta'  => $this->dash->getSummaryMeta($this->userNim),
            'activeMenu'   => 'ringkasan',
        ]);

        return view('mahasiswa/dashboard/index', $data);
    }

    /**
     * 2. Halaman Daftar Praktikum
     */
    public function praktikum(): string
    {
        $data = array_merge($this->base(), [
            'classRows'  => $this->dash->getClassRows($this->userNim),
            'activeMenu' => 'praktikum',
        ]);

        return view('mahasiswa/dashboard/praktikum', $data);
    }

    /**
     * 3. Halaman Kehadiran
     */
    public function kehadiran(): string
    {
        $data = array_merge($this->base(), [
            'attendanceRows' => $this->dash->getAttendanceRows($this->userNim),
            'activeMenu'     => 'kehadiran',
        ]);

        return view('mahasiswa/dashboard/kehadiran', $data);
    }

    /**
     * 4. Halaman Nilai
     */
    public function nilai(): string
    {
        $data = array_merge($this->base(), [
            'scoreRows'  => $this->dash->getScoreRows($this->userNim),
            'activeMenu' => 'nilai',
        ]);

        return view('mahasiswa/dashboard/nilai', $data);
    }

    /**
     * 5. Halaman Remedial
     */
    public function remedial(): string
    {
        $data = array_merge($this->base(), [
            'remedialRows' => $this->dash->getRemedialRows($this->userNim),
            'activeMenu'   => 'remedial',
        ]);

        return view('mahasiswa/dashboard/remedial', $data);
    }

    /**
     * 6. Halaman Notifikasi
     */
    public function notifikasi(): string
    {
        $data = array_merge($this->base(), [
            'notifications' => $this->dash->getNotifications($this->userNim),
            'activeMenu'    => 'notifikasi',
        ]);

        return view('mahasiswa/dashboard/notifikasi', $data);
    }

    /**
     * 7. Detail Praktikum (single class)
     */
    public function detail(int $classId): string
    {
        $data = array_merge($this->base(), [
            'detail'     => $this->dash->buildDetailData($this->userNim, $classId),
            'activeMenu' => 'praktikum',
        ]);

        return view('mahasiswa/dashboard/detail', $data);
    }
}
