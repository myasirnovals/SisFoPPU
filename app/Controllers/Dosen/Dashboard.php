<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\LecturerModel;
use App\Models\ClassLecturerModel;
use App\Models\MataKuliahModel;
use App\Models\ScoreModel;

class Dashboard extends BaseController
{
    private LecturerModel $lecturerModel;
    private ClassLecturerModel $classLecturerModel;
    private MataKuliahModel $mataKuliahModel;
    private ScoreModel $scoreModel;
    private string $userId;
    private ?string $lecturerNid = null;
    protected $db;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);

        $this->db = \Config\Database::connect();
        $this->lecturerModel = new LecturerModel();
        $this->classLecturerModel = new ClassLecturerModel();
        $this->mataKuliahModel = new MataKuliahModel();
        $this->scoreModel = new ScoreModel();

        $session = session();
        $this->userId = (string) ($session->get('user_id') ?? $session->get('nid') ?? $session->get('id') ?? '');

        // Resolve lecturer NID dari session
        if ($this->userId !== '') {
            $this->lecturerNid = $this->lecturerModel->getLecturerIdByUserId($this->userId);
        }
    }

    /**
     * Base data untuk semua halaman dosen
     */
    private function base(): array
    {
        $session = session();
        $displayName = (string) ($session->get('full_name') ?: $session->get('username') ?: 'Pengguna');

        $academic = $this->resolveAcademicContext();

        // Hitung total kelas yang diampu
        $classTotal = $this->countLecturerClasses();

        return [
            'userName'      => $displayName,
            'lecturerName'  => $displayName,
            'academicYear'  => $academic['academic_year'],
            'semesterLabel' => $academic['semester_label'],
            'todayLabel'    => $this->formatDateIndo(date('Y-m-d')),
            'classTotal'    => $classTotal,
            'activeMenu'    => 'dashboard',
        ];
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  HALAMAN DASHBOARD
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * 1. Dashboard / Ringkasan Utama
     */
    public function index(): string
    {
        $base = $this->base();
        $classRows = $this->loadClassRows();
        $summary = $this->buildSummary($classRows);
        $validationRows = $this->loadValidationRows($classRows);
        $riskRows = $this->loadRiskRows($classRows);
        $remedialRows = $this->loadRemedialRows($classRows);

        $data = array_merge($base, [
            'activeMenu'       => 'dashboard',
            'summaryCards'     => $summary['cards'],
            'quickActions'     => $this->buildQuickActions(),
            'classRows'        => $classRows,
            'validationRows'   => array_slice($validationRows, 0, 6),
            'riskRows'         => array_slice($riskRows, 0, 6),
            'remedialRows'     => array_slice($remedialRows, 0, 6),
            'chartData'        => $this->buildChartData($classRows),
            'classTotal'       => $summary['class_total'],
            'studentTotal'     => $summary['student_total'],
            'validationTotal'  => $summary['validation_total'],
            'remedialTotal'    => $summary['remedial_total'],
            'progressNilai'    => $summary['progress_nilai'],
            'progressKehadiran' => $summary['progress_kehadiran'],
            'statusLegend'     => $summary['status_legend'],
        ]);

        return view('dosen/dashboard/index', $data);
    }

    /**
     * 2. Halaman Kelas Saya
     */
    public function kelasSaya(): string
    {
        $base = $this->base();
        $classRows = $this->loadClassRowsDetailed();

        $data = array_merge($base, [
            'activeMenu' => 'kelas-saya',
            'classRows'  => $classRows,
        ]);

        return view('dosen/dashboard/kelas_saya', $data);
    }

    /**
     * 3. Halaman Validasi Nilai
     */
    public function validasi(): string
    {
        $base = $this->base();
        $classRows = $this->loadClassRows();
        $validationRows = $this->loadValidationRows($classRows, 50);

        $data = array_merge($base, [
            'activeMenu'     => 'validasi',
            'validationRows' => $validationRows,
        ]);

        return view('dosen/dashboard/validasi', $data);
    }

    /**
     * 4. Halaman Remedial
     */
    public function remedial(): string
    {
        $base = $this->base();
        $classRows = $this->loadClassRows();
        $remedialRows = $this->loadRemedialRows($classRows, 50);

        $data = array_merge($base, [
            'activeMenu'   => 'remedial',
            'remedialRows' => $remedialRows,
        ]);

        return view('dosen/dashboard/remedial', $data);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  POPUP METHODS
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * 5. Popup: Input Nilai per Mahasiswa
     */
    public function inputNilaiForm(int $classId, string $studentNim = ''): string
    {
        $db = $this->db;
        if (empty($studentNim)) {
            return '<div class="alert alert-danger">NIM mahasiswa tidak valid.</div>';
        }

        // Get class info
        $class = $db->table('practicum_classes pc')
            ->select('pc.*, mk.kode_mk as course_code, mk.nama_mk as course_name')
            ->join('mata_kuliah mk', 'mk.id = pc.course_id', 'left')
            ->where('pc.id', $classId)
            ->get()
            ->getRowArray();

        if (!$class) {
            return '<div class="alert alert-danger">Kelas tidak ditemukan.</div>';
        }

        // Get student info
        $student = $db->table('users')
            ->select('id, full_name, email')
            ->where('id', $studentNim)
            ->get()
            ->getRowArray();

        // Get assessment components
        $components = $this->loadClassComponents($classId);

        // Get existing scores
        $existingScores = [];
        $scoreRows = $db->table('score_entries')
            ->select('component_id, subcomponent_id, score_value, notes')
            ->where('practicum_class_id', $classId)
            ->where('student_id', $studentNim)
            ->get()
            ->getResultArray();

        foreach ($scoreRows as $sr) {
            $key = $sr['subcomponent_id']
                ? "sub_{$sr['subcomponent_id']}"
                : "comp_{$sr['component_id']}";
            $existingScores[$key] = [
                'score' => $sr['score_value'],
                'notes' => $sr['notes'],
            ];
        }

        $data = [
            'class'          => $class,
            'student'        => $student,
            'components'     => $components,
            'existingScores' => $existingScores,
        ];

        return view('dosen/dashboard/popup_input_nilai', $data);
    }

    /**
     * 6. Popup: Rekap Nilai Kelas
     */
    public function rekapNilai(int $classId): string
    {
        $db = $this->db;

        // Get class info
        $class = $db->table('practicum_classes pc')
            ->select('pc.*, mk.kode_mk as course_code, mk.nama_mk as course_name')
            ->join('mata_kuliah mk', 'mk.id = pc.course_id', 'left')
            ->where('pc.id', $classId)
            ->get()
            ->getRowArray();

        if (!$class) {
            return '<div class="alert alert-danger">Kelas tidak ditemukan.</div>';
        }

        // Get components
        $components = $this->loadClassComponents($classId);

        // Get students with scores
        $students = $this->loadClassStudents($classId);

        // Build score matrix
        // Build score matrix
        foreach ($students as $key => $student) {
            $studentScores = [];
            $totalWeighted = 0;
            $totalWeight = 0;

            foreach ($components as $comp) {
                $compId = $comp['id'] ?? 0;
                $maxScore = isset($comp['max_score']) ? (float) $comp['max_score'] : 100;
                $weight = isset($comp['weight']) ? (float) $comp['weight'] : 0;

                $scoreEntry = $db->table('score_entries')
                    ->select('score_value')
                    ->where('practicum_class_id', $classId)
                    ->where('student_id', $student['nim'])
                    ->where('component_id', $compId)
                    ->get()
                    ->getRowArray();

                $score = ($scoreEntry && isset($scoreEntry['score_value'])) ? (float) $scoreEntry['score_value'] : null;

                $percentage = ($score !== null && $maxScore > 0) ? ($score / $maxScore) * 100 : null;

                $studentScores[$compId] = [
                    'score'      => $score,
                    'max_score'  => $maxScore,
                    'percentage' => $percentage,
                ];

                if ($score !== null && $maxScore > 0) {
                    $totalWeighted += ($score / $maxScore) * $weight;
                    $totalWeight += $weight;
                }
            }

            // Calculate final score
            $finalScore = ($totalWeight > 0) ? ($totalWeighted / $totalWeight) * 100 : null;

            // Get grade
            $grade = '-';
            if ($finalScore !== null) {
                $gradeRow = $db->table('grade_scales')
                    ->select('grade_letter')
                    ->where('min_score <=', $finalScore)
                    ->where('max_score >=', $finalScore)
                    ->where('is_active', 1)
                    ->orderBy('min_score', 'DESC')
                    ->get()
                    ->getRow();
                $grade = ($gradeRow && isset($gradeRow->grade_letter)) ? $gradeRow->grade_letter : 'E';
            }

            $students[$key]['scores'] = $studentScores;
            $students[$key]['final_score'] = ($finalScore !== null) ? round($finalScore, 2) : '-';
            $students[$key]['grade'] = $grade;
        }

        $data = [
            'class'      => $class,
            'components' => $components,
            'students'   => $students,
        ];

        return view('dosen/dashboard/popup_rekap', $data);
    }

    /**
     * 7. Popup: Detail Mahasiswa
     */
    public function detailMahasiswa(int $classId, string $studentNim = ''): string
    {
        $db = $this->db;
        if (empty($studentNim)) {
            return '<div class="alert alert-danger">NIM mahasiswa tidak valid.</div>';
        }

        // Get student info
        $student = $db->table('users u')
            ->select('u.*, s.study_program_id, s.class_year, s.status as student_status')
            ->join('students s', 's.user_nim = u.id', 'left')
            ->where('u.id', $studentNim)
            ->get()
            ->getRowArray();

        // Get class enrollment info
        $enrollment = $db->table('class_students')
            ->where('practicum_class_id', $classId)
            ->where('student_nim', $studentNim)
            ->get()
            ->getRowArray();

        // Get class info
        $class = $db->table('practicum_classes pc')
            ->select('pc.*, mk.kode_mk, mk.nama_mk, mk.sks')
            ->join('mata_kuliah mk', 'mk.id = pc.course_id', 'left')
            ->where('pc.id', $classId)
            ->get()
            ->getRowArray();

        // Get scores with components
        $scores = $db->table('score_entries se')
            ->select('se.*, ac.component_name, ac.component_type, ac.weight, ac.max_score')
            ->join('assessment_components ac', 'ac.id = se.component_id', 'left')
            ->where('se.practicum_class_id', $classId)
            ->where('se.student_id', $studentNim)
            ->get()
            ->getResultArray();

        // Get attendance stats
        $attendanceStats = $db->table('attendance_records ar')
            ->select('ast.name as status_name, ast.code as status_code, COUNT(*) as total')
            ->join('attendance_sessions ase', 'ase.id = ar.attendance_session_id', 'inner')
            ->join('attendance_statuses ast', 'ast.id = ar.attendance_status_id', 'left')
            ->where('ase.practicum_class_id', $classId)
            ->where('ar.student_id', $studentNim)
            ->groupBy('ar.attendance_status_id')
            ->get()
            ->getResultArray();

        $attendanceSummary = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alfa' => 0,
            'susulan' => 0,
            'total_sessions' => 0
        ];
        foreach ($attendanceStats as $stat) {
            $code = $stat['status_code'] ?? 'unknown';
            $attendanceSummary[$code] = (int) $stat['total'];
            $attendanceSummary['total_sessions'] += (int) $stat['total'];
        }

        // Get final score
        $finalScore = $db->table('final_scores')
            ->where('practicum_class_id', $classId)
            ->where('student_id', $studentNim)
            ->get()
            ->getRowArray();

        $data = [
            'student'           => $student,
            'enrollment'        => $enrollment,
            'class'             => $class,
            'scores'            => $scores,
            'attendanceSummary' => $attendanceSummary,
            'finalScore'        => $finalScore,
        ];

        return view('dosen/dashboard/popup_detail', $data);
    }

    /**
     * 8. Simpan Nilai (AJAX)
     */
    public function simpanNilai()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $classId = $this->request->getPost('practicum_class_id');
        $studentId = $this->request->getPost('student_id');
        $scores = $this->request->getPost('scores') ?? [];
        $notes = $this->request->getPost('notes');

        $db = $this->db;

        $db->transStart();

        try {
            // Save component scores
            if (!empty($scores['comp'])) {
                foreach ($scores['comp'] as $compId => $scoreValue) {
                    if ($scoreValue === '' || $scoreValue === null) continue;

                    $comp = $db->table('assessment_components')
                        ->where('id', $compId)
                        ->get()
                        ->getRowArray();

                    $this->scoreModel->saveScore([
                        'practicum_class_id'        => $classId,
                        'student_id'      => $studentId,
                        'component_id'    => $compId,
                        'subcomponent_id' => null,
                        'score_value'     => $scoreValue,
                        'submitted_by'    => $this->userId,
                        'notes'           => $notes,
                    ]);
                }
            }

            // Save subcomponent scores
            if (!empty($scores['sub'])) {
                foreach ($scores['sub'] as $subId => $scoreValue) {
                    if ($scoreValue === '' || $scoreValue === null) continue;

                    $sub = $db->table('assessment_subcomponents')
                        ->where('id', $subId)
                        ->get()
                        ->getRowArray();

                    $this->scoreModel->saveScore([
                        'practicum_class_id' => $classId,
                        'student_id'      => $studentId,
                        'component_id'    => $sub['component_id'] ?? 0,
                        'subcomponent_id' => $subId,
                        'score_value'     => $scoreValue,
                        'submitted_by'    => $this->userId,
                        'notes'           => $notes,
                    ]);
                }
            }

            $db->transComplete();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Nilai berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ]);
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  DATA LOADER (Private Methods)
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Load class rows untuk dashboard (ringkasan)
     */
    private function loadClassRows(): array
    {
        $db = $this->db;

        if ($this->lecturerNid === null) {
            return [];
        }

        $builder = $db->table('practicum_classes pc');
        $builder->select([
            'pc.id',
            'pc.course_id',
            'pc.academic_year_id',
            'pc.semester_id',
            'pc.class_code',
            'pc.class_name',
            'pc.status',
            'pc.deadline_at',
        ]);

        $builder->select('mk.kode_mk as course_code, mk.nama_mk as course_name, mk.sks as credits');
        $builder->join('mata_kuliah mk', 'mk.id = pc.course_id', 'left');

        $builder->select('ay.year_code as academic_year_label');
        $builder->join('academic_years ay', 'ay.id = pc.academic_year_id', 'left');

        $builder->select('s.semester_name as semester_label');
        $builder->join('semesters s', 's.id = pc.semester_id', 'left');

        $builder->join('class_lecturers cl', 'cl.practicum_class_id = pc.id', 'inner');
        $builder->where('cl.lecturer_id', (int) $this->lecturerNid);

        $builder->where('pc.status', 'aktif');
        $builder->orderBy('mk.nama_mk', 'ASC');
        $builder->groupBy('pc.id');

        $rows = $builder->get()->getResultArray();

        $classIds = array_column($rows, 'id');
        $studentCounts = $this->loadStudentCounts($classIds);
        $scoreStats = $this->loadScoreStats($classIds);
        $finalStats = $this->loadFinalStats($classIds);
        $remedialCounts = $this->loadRemedialCounts($classIds);

        $prepared = [];
        foreach ($rows as $row) {
            $classId = (int) ($row['id'] ?? 0);
            $studentCount = (int) ($studentCounts[$classId] ?? 0);
            $scoreStat = $scoreStats[$classId] ?? ['filled' => 0, 'expected' => 0];
            $finalStat = $finalStats[$classId] ?? ['average_score' => null, 'validation_status' => 'draft'];

            $status = $this->normalizeStatus((string) ($finalStat['validation_status'] ?? $row['status'] ?? 'draft'));

            $prepared[] = [
                'id'                => $classId,
                'academic_year'     => (string) ($row['academic_year_label'] ?? '-'),
                'semester'          => (string) ($row['semester_label'] ?? '-'),
                'course_name'       => (string) ($row['course_name'] ?? '-'),
                'course_code'       => (string) ($row['course_code'] ?? '-'),
                'class_name'        => (string) ($row['class_name'] ?? $row['class_code'] ?? '-'),
                'student_count'     => $studentCount,
                'progress_nilai'    => $this->percentage($scoreStat['filled'] ?? 0, $scoreStat['expected'] ?? 0),
                'progress_kehadiran' => 0,
                'average_score'     => $finalStat['average_score'] !== null ? round((float) $finalStat['average_score'], 1) : 0.0,
                'status'            => $status,
                'status_badge'      => $this->statusBadgeClass($status),
                'low_students'      => (int) ($finalStat['low_count'] ?? 0),
                'remedial_count'    => (int) ($remedialCounts[$classId] ?? 0),
                'detail_url'        => site_url('dosen/kelas-saya'),
                'input_url'         => site_url('dosen/kelas-saya'),
                'rekap_url'         => site_url('dosen/kelas-saya'),
                'validation_url'    => site_url('dosen/validasi'),
                'remedial_url'      => site_url('dosen/remedial'),
                'report_url'        => site_url('dosen/kelas-saya'),
            ];
        }

        return $prepared;
    }

    /**
     * Load detailed class rows untuk halaman Kelas Saya
     */
    private function loadClassRowsDetailed(): array
    {
        $rows = $this->loadClassRows();

        foreach ($rows as &$row) {
            $classId = $row['id'];

            $students = $this->loadClassStudents($classId);
            $row['students'] = $students;
            $row['student_count'] = count($students);

            $row['components'] = $this->loadClassComponents($classId);
        }

        return $rows;
    }

    /**
     * Load students in a class
     */
    private function loadClassStudents(int $classId): array
    {
        $db = $this->db;

        $rows = $db->table('class_students cs')
            ->select([
                'cs.id',
                'cs.student_nim',
                'cs.group_id',
                'cs.enrollment_status',
                'u.full_name as student_name',
                'u.email',
            ])
            ->join('users u', 'u.id = cs.student_nim', 'left')
            ->where('cs.practicum_class_id', $classId)
            ->get()
            ->getResultArray();

        return array_map(function (array $row): array {
            return [
                'id'                => (int) ($row['id'] ?? 0),
                'nim'               => (string) ($row['student_nim'] ?? '-'),
                'student_name'      => (string) ($row['student_name'] ?? '-'),
                'email'             => (string) ($row['email'] ?? '-'),
                'group_id'          => (int) ($row['group_id'] ?? 0),
                'enrollment_status' => (string) ($row['enrollment_status'] ?? 'aktif'),
            ];
        }, $rows);
    }

    /**
     * Load assessment components for a class
     */
    private function loadClassComponents(int $classId): array
    {
        $db = $this->db;

        $classRow = $db->table('practicum_classes')
            ->select('template_id')
            ->where('id', $classId)
            ->get()
            ->getRow();

        $templateId = $classRow ? (int) $classRow->template_id : 0;
        if ($templateId === 0) {
            return [];
        }

        $rows = $db->table('assessment_components')
            ->select('id, component_code, component_name, component_type, weight, max_score, sort_order, allow_subcomponents')
            ->where('template_id', $templateId)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();

        $components = [];
        foreach ($rows as $row) {
            $compId = (int) ($row['id'] ?? 0);
            $component = [
                'id'                  => $compId,
                'component_code'      => (string) ($row['component_code'] ?? '-'),
                'component_name'      => (string) ($row['component_name'] ?? '-'),
                'component_type'      => (string) ($row['component_type'] ?? '-'),
                'weight'              => (float) ($row['weight'] ?? 0),
                'max_score'           => (float) ($row['max_score'] ?? 100),
                'allow_subcomponents' => (bool) ($row['allow_subcomponents'] ?? 0),
                'subcomponents'       => [],
            ];

            if ($component['allow_subcomponents']) {
                $subRows = $db->table('assessment_subcomponents')
                    ->select('id, subcomponent_code, subcomponent_name, weight, max_score, sort_order')
                    ->where('component_id', $compId)
                    ->where('is_active', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->get()
                    ->getResultArray();

                foreach ($subRows as $sub) {
                    $component['subcomponents'][] = [
                        'id'                 => (int) ($sub['id'] ?? 0),
                        'subcomponent_code'  => (string) ($sub['subcomponent_code'] ?? '-'),
                        'subcomponent_name'  => (string) ($sub['subcomponent_name'] ?? '-'),
                        'weight'             => (float) ($sub['weight'] ?? 0),
                        'max_score'          => (float) ($sub['max_score'] ?? 100),
                    ];
                }
            }

            $components[] = $component;
        }

        return $components;
    }

    /**
     * Load student counts per class
     */
    private function loadStudentCounts(array $classIds): array
    {
        if (empty($classIds)) {
            return [];
        }

        $rows = $this->db->table('class_students')
            ->select('practicum_class_id as class_id, COUNT(*) as total')
            ->whereIn('practicum_class_id', $classIds)
            ->groupBy('practicum_class_id')
            ->get()
            ->getResultArray();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(int) ($row['class_id'] ?? 0)] = (int) ($row['total'] ?? 0);
        }

        return $counts;
    }

    /**
     * Load score stats per class
     */
    private function loadScoreStats(array $classIds): array
    {
        if (empty($classIds)) {
            return [];
        }

        $rows = $this->db->table('score_entries')
            ->select('practicum_class_id as class_id')
            ->select('COUNT(*) as filled_count', false)
            ->select('COUNT(DISTINCT student_id) * COUNT(DISTINCT component_id) as expected_count', false)
            ->whereIn('practicum_class_id', $classIds)
            ->groupBy('practicum_class_id')
            ->get()
            ->getResultArray();

        $stats = [];
        foreach ($rows as $row) {
            $classId = (int) ($row['class_id'] ?? 0);
            $stats[$classId] = [
                'filled'   => (int) ($row['filled_count'] ?? 0),
                'expected' => (int) ($row['expected_count'] ?? 0),
            ];
        }

        return $stats;
    }

    /**
     * Load final score stats per class
     */
    private function loadFinalStats(array $classIds): array
    {
        if (empty($classIds)) {
            return [];
        }

        $rows = $this->db->table('final_scores')
            ->select('practicum_class_id as class_id')
            ->select('AVG(final_score) as average_score')
            ->select('COUNT(CASE WHEN final_score < 60 THEN 1 END) as low_count')
            ->select('MAX(validation_status) as validation_status')
            ->whereIn('practicum_class_id', $classIds)
            ->groupBy('practicum_class_id')
            ->get()
            ->getResultArray();

        $stats = [];
        foreach ($rows as $row) {
            $stats[(int) ($row['class_id'] ?? 0)] = [
                'average_score'      => $row['average_score'] !== null ? (float) $row['average_score'] : null,
                'low_count'          => (int) ($row['low_count'] ?? 0),
                'validation_status'  => (string) ($row['validation_status'] ?? 'draft'),
            ];
        }

        return $stats;
    }

    /**
     * Load remedial counts per class
     */
    private function loadRemedialCounts(array $classIds): array
    {
        if (empty($classIds)) {
            return [];
        }

        $csRows = $this->db->table('class_students')
            ->select('id, practicum_class_id')
            ->whereIn('practicum_class_id', $classIds)
            ->get()
            ->getResultArray();

        $csIds = array_column($csRows, 'id');
        if (empty($csIds)) {
            return [];
        }

        $rows = $this->db->table('remedial_participants')
            ->select('practicum_class_id as class_id, COUNT(*) as total')
            ->whereIn('practicum_class_id', $classIds)
            ->whereIn('student_id', $csIds)
            ->whereIn('status', ['eligible', 'terdaftar', 'dijadwalkan'])
            ->groupBy('practicum_class_id')
            ->get()
            ->getResultArray();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(int) ($row['class_id'] ?? 0)] = (int) ($row['total'] ?? 0);
        }

        return $counts;
    }

    /**
     * Load validation rows
     */
    private function loadValidationRows(array $classRows, int $limit = 6): array
    {
        if (empty($classRows)) {
            return [];
        }

        $classMap = [];
        foreach ($classRows as $row) {
            $classMap[$row['id']] = $row;
        }

        $rows = $this->db->table('score_entries se')
            ->select([
                'se.practicum_class_id as class_id',
                'se.component_id',
                'se.student_id',
                'se.score_value',
                'se.submitted_by',
                'se.submitted_at',
            ])
            ->whereIn('se.practicum_class_id', array_keys($classMap))
            ->orderBy('se.submitted_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        $prepared = [];
        foreach ($rows as $row) {
            $classId = (int) ($row['class_id'] ?? 0);
            $classInfo = $classMap[$classId] ?? null;
            if (!$classInfo) continue;

            $component = $this->db->table('assessment_components')
                ->select('component_name')
                ->where('id', (int) ($row['component_id'] ?? 0))
                ->get()
                ->getRow();

            $submitter = $this->db->table('users')
                ->select('full_name')
                ->where('id', (string) ($row['submitted_by'] ?? ''))
                ->get()
                ->getRow();

            $prepared[] = [
                'course_name'    => $classInfo['course_name'],
                'class_name'     => $classInfo['class_name'],
                'component_name' => $component ? $component->component_name : 'Komponen #' . ($row['component_id'] ?? '-'),
                'submitted_by'   => $submitter ? $submitter->full_name : 'Asisten',
                'submitted_at'   => $this->formatDateTime((string) ($row['submitted_at'] ?? '')),
                'status'         => 'Submitted',
                'badge_class'    => 'warning',
                'review_url'     => site_url('dosen/validasi'),
            ];
        }

        return $prepared;
    }

    /**
     * Load risk rows (mahasiswa berisiko)
     */
    private function loadRiskRows(array $classRows): array
    {
        if (empty($classRows)) {
            return [];
        }

        $classMap = [];
        foreach ($classRows as $row) {
            $classMap[$row['id']] = $row;
        }

        $rows = $this->db->table('final_scores fs')
            ->select([
                'fs.practicum_class_id as class_id',
                'fs.student_id',
                'fs.final_score',
                'fs.grade_letter',
            ])
            ->whereIn('fs.practicum_class_id', array_keys($classMap))
            ->groupStart()
            ->where('fs.final_score <', 60)
            ->orWhere('fs.final_score IS NULL')
            ->groupEnd()
            ->limit(20)
            ->get()
            ->getResultArray();

        $prepared = [];
        foreach ($rows as $row) {
            $classId = (int) ($row['class_id'] ?? 0);
            $classInfo = $classMap[$classId] ?? null;
            if (!$classInfo) continue;

            $student = $this->db->table('class_students cs')
                ->select('u.full_name as student_name, cs.student_nim')
                ->join('users u', 'u.id = cs.student_nim', 'left')
                ->where('cs.practicum_class_id', $classId)
                ->where('cs.id', (int) ($row['student_id'] ?? 0))
                ->get()
                ->getRow();

            $finalScore = $row['final_score'] !== null ? (float) $row['final_score'] : null;

            $riskStatus = $finalScore === null
                ? 'Komponen nilai belum lengkap'
                : ($finalScore < 60 ? 'Nilai rendah' : 'Aman');

            if ($riskStatus === 'Aman') continue;

            $prepared[] = [
                'nim'             => $student ? $student->student_nim : '-',
                'student_name'    => $student ? $student->student_name : 'Mahasiswa',
                'course_name'     => $classInfo['course_name'],
                'class_name'      => $classInfo['class_name'],
                'temporary_score' => $finalScore !== null ? number_format($finalScore, 1) : '-',
                'attendance'      => 0,
                'risk_status'     => $riskStatus,
                'badge_class'     => $this->riskBadgeClass($riskStatus),
                'detail_url'      => site_url('dosen/kelas-saya'),
            ];
        }

        return $prepared;
    }

    /**
     * Load remedial rows
     */
    private function loadRemedialRows(array $classRows, int $limit = 6): array
    {
        if (empty($classRows)) {
            return [];
        }

        $classMap = [];
        foreach ($classRows as $row) {
            $classMap[$row['id']] = $row;
        }

        $csRows = $this->db->table('class_students')
            ->select('id, practicum_class_id')
            ->whereIn('practicum_class_id', array_keys($classMap))
            ->get()
            ->getResultArray();

        $csMap = [];
        foreach ($csRows as $cs) {
            $csMap[(int) $cs['id']] = (int) $cs['practicum_class_id'];
        }

        $csIds = array_keys($csMap);
        if (empty($csIds)) {
            return [];
        }

        $rows = $this->db->table('remedial_participants rp')
            ->select([
                'rp.id',
                'rp.practicum_class_id',
                'rp.student_id',
                'rp.status',
                'rp.reason',
                'rp.before_score',
                'rp.after_score',
            ])
            ->whereIn('rp.student_id', $csIds)
            ->whereIn('rp.status', ['eligible', 'terdaftar', 'dijadwalkan', 'sudah_dinilai'])
            ->orderBy('rp.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        $prepared = [];
        foreach ($rows as $row) {
            $classId = (int) ($row['practicum_class_id'] ?? 0);
            $classInfo = $classMap[$classId] ?? null;
            if (!$classInfo) continue;

            $student = $this->db->table('users')
                ->select('full_name')
                ->where('id', (string) ($row['student_id'] ?? ''))
                ->get()
                ->getRow();

            $status = $this->normalizeRemedialStatus((string) ($row['status'] ?? ''));

            $prepared[] = [
                'nim'          => (string) ($row['student_id'] ?? '-'),
                'student_name' => $student ? $student->full_name : 'Mahasiswa',
                'course_name'  => $classInfo['course_name'],
                'class_name'   => $classInfo['class_name'],
                'score'        => $row['before_score'] !== null ? number_format((float) $row['before_score'], 1) : '-',
                'grade'        => '-',
                'reason'       => (string) ($row['reason'] ?? 'Memenuhi kriteria remedial'),
                'status'       => $status,
                'badge_class'  => $this->remedialBadgeClass($status),
                'detail_url'   => site_url('dosen/remedial'),
                'manage_url'   => site_url('dosen/remedial'),
            ];
        }

        return $prepared;
    }

    /**
     * Count total classes for this lecturer
     */
    private function countLecturerClasses(): int
    {
        if ($this->lecturerNid === null) {
            return 0;
        }

        return $this->db->table('practicum_classes pc')
            ->join('class_lecturers cl', 'cl.practicum_class_id = pc.id', 'inner')
            ->where('pc.status', 'aktif')
            ->where('cl.lecturer_id', (int) $this->lecturerNid)
            ->countAllResults();
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  BUILDERS
    // ═══════════════════════════════════════════════════════════════════════

    private function buildSummary(array $classRows): array
    {
        $classTotal = count($classRows);
        $studentTotal = array_sum(array_column($classRows, 'student_count'));
        $validationTotal = count(array_filter($classRows, fn($r) => in_array($r['status'] ?? '', ['submitted', 'reviewed'])));
        $remedialTotal = array_sum(array_column($classRows, 'remedial_count'));

        $progressNilai = $classTotal > 0
            ? (int) round(array_sum(array_column($classRows, 'progress_nilai')) / $classTotal)
            : 0;

        $progressKehadiran = $classTotal > 0
            ? (int) round(array_sum(array_column($classRows, 'progress_kehadiran')) / $classTotal)
            : 0;

        $statusOrder = ['draft', 'submitted', 'reviewed', 'validated', 'locked'];
        $statusCounts = array_fill_keys($statusOrder, 0);
        foreach ($classRows as $row) {
            $status = $row['status'] ?? 'draft';
            $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
        }

        return [
            'cards' => [
                ['title' => 'Total Kelas Diampu', 'value' => $classTotal, 'icon' => 'bi-journal-bookmark-fill', 'color' => 'primary', 'description' => 'Kelas aktif yang Anda ampu'],
                ['title' => 'Total Mahasiswa', 'value' => $studentTotal, 'icon' => 'bi-people-fill', 'color' => 'success', 'description' => 'Mahasiswa lintas kelas aktif'],
                ['title' => 'Nilai Perlu Validasi', 'value' => $validationTotal, 'icon' => 'bi-shield-check', 'color' => 'warning', 'description' => 'Submission yang belum final'],
                ['title' => 'Mahasiswa Remedial', 'value' => $remedialTotal, 'icon' => 'bi-arrow-repeat', 'color' => 'danger', 'description' => 'Eligible atau sedang remedial'],
                ['title' => 'Progress Input Nilai', 'value' => $progressNilai . '%', 'icon' => 'bi-graph-up-arrow', 'color' => 'info', 'description' => 'Rata-rata kelengkapan input nilai'],
                ['title' => 'Progress Kehadiran', 'value' => $progressKehadiran . '%', 'icon' => 'bi-calendar-check', 'color' => 'secondary', 'description' => 'Rata-rata kelengkapan absensi'],
            ],
            'class_total'       => $classTotal,
            'student_total'     => $studentTotal,
            'validation_total'  => $validationTotal,
            'remedial_total'    => $remedialTotal,
            'progress_nilai'    => $progressNilai,
            'progress_kehadiran' => $progressKehadiran,
            'status_legend'     => $statusCounts,
        ];
    }

    private function buildQuickActions(): array
    {
        return [
            ['label' => 'Kelas Saya', 'icon' => 'bi-journal-text', 'color' => 'primary', 'url' => site_url('dosen/kelas-saya')],
            ['label' => 'Input Nilai', 'icon' => 'bi-pencil-square', 'color' => 'warning', 'url' => site_url('dosen/kelas-saya')],
            ['label' => 'Rekapitulasi', 'icon' => 'bi-table', 'color' => 'info', 'url' => site_url('dosen/kelas-saya')],
            ['label' => 'Validasi Nilai', 'icon' => 'bi-shield-check', 'color' => 'success', 'url' => site_url('dosen/validasi')],
            ['label' => 'Remedial', 'icon' => 'bi-arrow-repeat', 'color' => 'danger', 'url' => site_url('dosen/remedial')],
            ['label' => 'Laporan', 'icon' => 'bi-file-earmark-text', 'color' => 'secondary', 'url' => site_url('dosen/kelas-saya')],
        ];
    }

    private function buildChartData(array $classRows): array
    {
        $labels = [];
        $values = [];
        foreach ($classRows as $row) {
            $labels[] = $row['course_code'] ?? 'Kelas';
            $values[] = (int) ($row['progress_nilai'] ?? 0);
        }
        return [
            'labels' => $labels ?: ['Belum ada data'],
            'values' => $values ?: [0],
        ];
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  HELPERS
    // ═══════════════════════════════════════════════════════════════════════

    private function resolveAcademicContext(): array
    {
        $month = (int) date('n');
        $year = (int) date('Y');
        return [
            'academic_year'  => $month >= 7 ? "$year/" . ($year + 1) : ($year - 1) . "/$year",
            'semester_label' => $month >= 7 ? 'Semester Ganjil' : 'Semester Genap',
        ];
    }

    private function normalizeStatus(string $status): string
    {
        return match (strtolower(trim($status))) {
            'submitted' => 'submitted',
            'reviewed' => 'reviewed',
            'validated', 'approved' => 'validated',
            'locked' => 'locked',
            default => 'draft',
        };
    }

    private function statusBadgeClass(string $status): string
    {
        return match ($status) {
            'submitted' => 'warning',
            'reviewed' => 'info',
            'validated' => 'success',
            'locked' => 'dark',
            default => 'secondary',
        };
    }

    private function normalizeRemedialStatus(string $status): string
    {
        return match (strtolower(trim($status))) {
            'eligible' => 'Eligible',
            'terdaftar', 'registered' => 'Terdaftar',
            'dijadwalkan', 'scheduled' => 'Dijadwalkan',
            'sudah_dinilai', 'scored' => 'Selesai',
            'dibatalkan', 'cancelled' => 'Ditolak',
            default => 'Eligible',
        };
    }

    private function remedialBadgeClass(string $status): string
    {
        return match ($status) {
            'Eligible' => 'warning',
            'Terdaftar' => 'info',
            'Dijadwalkan' => 'primary',
            'Selesai' => 'success',
            'Ditolak' => 'danger',
            default => 'secondary',
        };
    }

    private function riskBadgeClass(string $status): string
    {
        return match ($status) {
            'Nilai rendah' => 'danger',
            'Kehadiran kurang' => 'warning',
            'Komponen nilai belum lengkap' => 'info',
            default => 'secondary',
        };
    }

    private function percentage(int $filled, int $expected): int
    {
        return $expected > 0 ? (int) round(($filled / $expected) * 100) : 0;
    }

    private function formatDateIndo(string $date): string
    {
        if ($date === '' || $date === '0000-00-00') return '-';
        $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $ts = strtotime($date);
        return $ts ? date('d', $ts) . ' ' . $bulan[(int)date('m', $ts)] . ' ' . date('Y', $ts) : $date;
    }

    private function formatDateTime(string $value): string
    {
        if ($value === '') return '-';
        $ts = strtotime($value);
        return $ts ? date('d/m/Y H:i', $ts) : $value;
    }
}
