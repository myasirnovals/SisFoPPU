<?php

namespace App\Controllers\Coordinator;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageForbiddenException;

class DashboardController extends BaseController
{
    private const COMPONENT_NAMES = [
        'Kehadiran',
        'Modul/Tugas',
        'Laporan',
        'UAS Praktik',
    ];

    public function index()
    {
        if (! $this->canAccessDashboard()) {
            throw new PageForbiddenException('Anda tidak memiliki akses ke dashboard koordinator praktikum.');
        }

        $context = $this->buildDashboardContext();

        return view('coordinator/summary', [
            'title' => 'Dashboard Koordinator Praktikum',
            'filters' => $context['filters'],
            'filterOptions' => $context['dataset']['filter_options'],
            'summaryCards' => $context['summaryCards'],
            'quickActions' => $this->buildQuickActions($context['filters']),
            'overviewStats' => [
                'monitoring_kelas' => $context['counts']['classes'],
                'kelas_perhatian' => $context['counts']['attention'],
                'remedial' => $context['counts']['remedial'],
                'validasi' => $context['counts']['validated'],
            ],
        ]);
    }

    public function classes()
    {
        if (! $this->canAccessDashboard()) {
            throw new PageForbiddenException('Anda tidak memiliki akses ke dashboard koordinator praktikum.');
        }

        $context = $this->buildDashboardContext();
        $progressRows = $this->paginate($this->buildProgressRows($context['filteredClasses']), 8, 'page');

        return view('coordinator/section_page', [
            'title' => 'Monitoring Kelas - Koordinator Praktikum',
            'sectionType' => 'classes',
            'sectionTitle' => 'Monitoring Progress Input Nilai',
            'sectionDescription' => 'Tabel ringkas progres kelengkapan nilai per kelas aktif.',
            'filters' => $context['filters'],
            'filterOptions' => $context['dataset']['filter_options'],
            'rows' => $progressRows['items'],
            'pagination' => $progressRows['pagination'],
            'totalRows' => $progressRows['pagination']['total'],
            'backUrl' => site_url('coordinator/dashboard?' . http_build_query($context['filters'])),
        ]);
    }

    public function attention()
    {
        if (! $this->canAccessDashboard()) {
            throw new PageForbiddenException('Anda tidak memiliki akses ke dashboard koordinator praktikum.');
        }

        $context = $this->buildDashboardContext();
        $attentionRows = $this->paginate($this->buildAttentionRows($context['filteredClasses']), 8, 'page');

        return view('coordinator/section_page', [
            'title' => 'Kelas Perlu Perhatian - Koordinator Praktikum',
            'sectionType' => 'attention',
            'sectionTitle' => 'Kelas Perlu Perhatian',
            'sectionDescription' => 'Kelas bermasalah, belum selesai, atau mendekati tenggat.',
            'filters' => $context['filters'],
            'filterOptions' => $context['dataset']['filter_options'],
            'rows' => $attentionRows['items'],
            'pagination' => $attentionRows['pagination'],
            'totalRows' => $attentionRows['pagination']['total'],
            'backUrl' => site_url('coordinator/dashboard?' . http_build_query($context['filters'])),
        ]);
    }

    public function remedial()
    {
        if (! $this->canAccessDashboard()) {
            throw new PageForbiddenException('Anda tidak memiliki akses ke dashboard koordinator praktikum.');
        }

        $context = $this->buildDashboardContext();
        $remedialRows = $this->paginate($this->buildRemedialRows($context['filteredRemedial'], $context['filteredFinalScores']), 8, 'page');

        return view('coordinator/section_page', [
            'title' => 'Monitoring Remedial - Koordinator Praktikum',
            'sectionType' => 'remedial',
            'sectionTitle' => 'Monitoring Remedial',
            'sectionDescription' => 'Ringkasan status remedial dan daftar mahasiswa remedial.',
            'filters' => $context['filters'],
            'filterOptions' => $context['dataset']['filter_options'],
            'stats' => $this->buildRemedialStats($context['filteredRemedial'], $context['filteredFinalScores']),
            'rows' => $remedialRows['items'],
            'pagination' => $remedialRows['pagination'],
            'totalRows' => $remedialRows['pagination']['total'],
            'backUrl' => site_url('coordinator/dashboard?' . http_build_query($context['filters'])),
        ]);
    }

    public function validation()
    {
        if (! $this->canAccessDashboard()) {
            throw new PageForbiddenException('Anda tidak memiliki akses ke dashboard koordinator praktikum.');
        }

        $context = $this->buildDashboardContext();
        $validationRows = $this->paginate($this->buildValidationRows($context['filteredValidations']), 8, 'page');

        return view('coordinator/section_page', [
            'title' => 'Validasi Nilai - Koordinator Praktikum',
            'sectionType' => 'validation',
            'sectionTitle' => 'Status Validasi Nilai',
            'sectionDescription' => 'Status input, validasi, tanggal submit, dan jumlah revisi.',
            'filters' => $context['filters'],
            'filterOptions' => $context['dataset']['filter_options'],
            'rows' => $validationRows['items'],
            'pagination' => $validationRows['pagination'],
            'totalRows' => $validationRows['pagination']['total'],
            'backUrl' => site_url('coordinator/dashboard?' . http_build_query($context['filters'])),
        ]);
    }

    public function activity()
    {
        if (! $this->canAccessDashboard()) {
            throw new PageForbiddenException('Anda tidak memiliki akses ke dashboard koordinator praktikum.');
        }

        $context = $this->buildDashboardContext();
        $activityRows = $this->paginate($this->buildActivityRows($context['filteredActivities']), 10, 'page');

        return view('coordinator/section_page', [
            'title' => 'Aktivitas Terbaru - Koordinator Praktikum',
            'sectionType' => 'activity',
            'sectionTitle' => 'Aktivitas Terbaru',
            'sectionDescription' => 'Aktivitas nilai, validasi, remedial, dan export laporan.',
            'filters' => $context['filters'],
            'filterOptions' => $context['dataset']['filter_options'],
            'rows' => $activityRows['items'],
            'pagination' => $activityRows['pagination'],
            'totalRows' => $activityRows['pagination']['total'],
            'backUrl' => site_url('coordinator/dashboard?' . http_build_query($context['filters'])),
        ]);
    }

    private function buildDashboardContext(): array
    {
        $filters = $this->collectFilters();
        $dataset = $this->buildDataset();

        $filteredClasses = $this->filterClasses($dataset['classes'], $filters);
        $filteredFinalScores = $this->filterFinalScores($dataset['final_scores'], $filteredClasses);
        $filteredRemedial = $this->filterRemedialParticipants($dataset['remedial_participants'], $filteredClasses);
        $filteredActivities = $this->filterActivities($dataset['activity_logs'], $filteredClasses, $filters);
        $filteredValidations = $this->filterValidations($dataset['validation_logs'], $filteredClasses);

        return [
            'filters' => $filters,
            'dataset' => $dataset,
            'filteredClasses' => $filteredClasses,
            'filteredFinalScores' => $filteredFinalScores,
            'filteredRemedial' => $filteredRemedial,
            'filteredActivities' => $filteredActivities,
            'filteredValidations' => $filteredValidations,
            'summaryCards' => $this->buildSummaryCards($filteredClasses, $filteredFinalScores, $filteredRemedial, $filteredValidations),
            'counts' => [
                'classes' => count($filteredClasses),
                'attention' => count($this->buildAttentionRows($filteredClasses)),
                'remedial' => count($filteredRemedial),
                'validated' => count(array_filter($filteredValidations, static fn (array $row): bool => in_array($row['validation_status'], ['Validated', 'Locked'], true))),
            ],
        ];
    }

    private function canAccessDashboard(): bool
    {
        $session = session();
        $roles = $this->normalizeValues($session->get('roles') ?? $session->get('role'));
        $permissions = $this->normalizeValues($session->get('permissions'));

        $roles = array_values(array_unique(array_map(static fn (string $role): string => strtolower(trim($role)), $roles)));
        $permissions = array_values(array_unique(array_map(static fn (string $permission): string => strtolower(trim($permission)), $permissions)));

        if ($roles === [] && $permissions === []) {
            return true;
        }

        return in_array('admin', $roles, true)
            || in_array('koordinator', $roles, true)
            || in_array('koordinator praktikum', $roles, true)
            || in_array('dashboard.coordinator.view', $permissions, true);
    }

    private function collectFilters(): array
    {
        $request = $this->request;

        return [
            'academic_year' => trim((string) ($request->getGet('academic_year') ?? '')),
            'semester' => trim((string) ($request->getGet('semester') ?? '')),
            'study_program' => trim((string) ($request->getGet('study_program') ?? '')),
            'course_id' => trim((string) ($request->getGet('course_id') ?? '')),
            'class_id' => trim((string) ($request->getGet('class_id') ?? '')),
            'lecturer' => trim((string) ($request->getGet('lecturer') ?? '')),
            'score_status' => trim((string) ($request->getGet('score_status') ?? '')),
        ];
    }

    private function buildDataset(): array
    {
        $courses = [
            ['id' => 1, 'code' => 'PRAK-IF101', 'name' => 'Praktikum Pemrograman Dasar', 'study_program' => 'Informatika'],
            ['id' => 2, 'code' => 'PRAK-IF205', 'name' => 'Praktikum Struktur Data', 'study_program' => 'Informatika'],
            ['id' => 3, 'code' => 'PRAK-SI310', 'name' => 'Praktikum Basis Data', 'study_program' => 'Sistem Informasi'],
        ];

        $classes = [
            [
                'id' => 1,
                'course_id' => 1,
                'course_code' => 'PRAK-IF101',
                'course_name' => 'Praktikum Pemrograman Dasar',
                'study_program' => 'Informatika',
                'class_name' => 'A',
                'lecturer_name' => 'Dr. Rina Prameswari',
                'assistant_name' => 'Alya',
                'student_count' => 10,
                'component_scores' => [90, 85, 0, 78],
                'attendance_completion' => 100,
                'template_weight_total' => 100,
                'score_status' => 'Validated',
                'validation_status' => 'Validated',
                'deadline' => '2026-05-20',
                'last_input' => '2026-05-14',
                'submit_date' => '2026-05-14',
                'validation_date' => '2026-05-15',
                'revision_count' => 0,
                'remedial_count' => 1,
            ],
            [
                'id' => 2,
                'course_id' => 2,
                'course_code' => 'PRAK-IF205',
                'course_name' => 'Praktikum Struktur Data',
                'study_program' => 'Informatika',
                'class_name' => 'B',
                'lecturer_name' => 'Dr. Bayu Hidayat',
                'assistant_name' => 'Dimas',
                'student_count' => 12,
                'component_scores' => [88, null, 78, 84],
                'attendance_completion' => 88,
                'template_weight_total' => 100,
                'score_status' => 'Reviewed',
                'validation_status' => 'Reviewed',
                'deadline' => '2026-05-18',
                'last_input' => '2026-05-13',
                'submit_date' => '2026-05-13',
                'validation_date' => null,
                'revision_count' => 1,
                'remedial_count' => 4,
            ],
            [
                'id' => 3,
                'course_id' => 3,
                'course_code' => 'PRAK-SI310',
                'course_name' => 'Praktikum Basis Data',
                'study_program' => 'Sistem Informasi',
                'class_name' => 'A',
                'lecturer_name' => 'Dr. Sinta Lestari',
                'assistant_name' => 'Nadia',
                'student_count' => 10,
                'component_scores' => [null, 70, 65, 80],
                'attendance_completion' => 70,
                'template_weight_total' => 90,
                'score_status' => 'Draft',
                'validation_status' => 'Draft',
                'deadline' => '2026-05-16',
                'last_input' => '2026-05-12',
                'submit_date' => null,
                'validation_date' => null,
                'revision_count' => 0,
                'remedial_count' => 3,
            ],
            [
                'id' => 4,
                'course_id' => 1,
                'course_code' => 'PRAK-IF101',
                'course_name' => 'Praktikum Pemrograman Dasar',
                'study_program' => 'Informatika',
                'class_name' => 'B',
                'lecturer_name' => 'Dr. Rina Prameswari',
                'assistant_name' => 'Fajar',
                'student_count' => 8,
                'component_scores' => [92, 88, 85, 90],
                'attendance_completion' => 95,
                'template_weight_total' => 100,
                'score_status' => 'Locked',
                'validation_status' => 'Locked',
                'deadline' => '2026-05-25',
                'last_input' => '2026-05-15',
                'submit_date' => '2026-05-15',
                'validation_date' => '2026-05-15',
                'revision_count' => 0,
                'remedial_count' => 0,
            ],
            [
                'id' => 5,
                'course_id' => 2,
                'course_code' => 'PRAK-IF205',
                'course_name' => 'Praktikum Struktur Data',
                'study_program' => 'Informatika',
                'class_name' => 'C',
                'lecturer_name' => 'Dr. Bayu Hidayat',
                'assistant_name' => 'Rafi',
                'student_count' => 10,
                'component_scores' => [75, 0, 82, null],
                'attendance_completion' => 80,
                'template_weight_total' => 100,
                'score_status' => 'Revision Requested',
                'validation_status' => 'Revision Requested',
                'deadline' => '2026-05-15',
                'last_input' => '2026-05-10',
                'submit_date' => '2026-05-10',
                'validation_date' => null,
                'revision_count' => 2,
                'remedial_count' => 5,
            ],
        ];

        foreach ($classes as $index => $class) {
            $classes[$index]['filled_components'] = $this->countFilledValues($class['component_scores']);
            $classes[$index]['total_components'] = count(self::COMPONENT_NAMES);
            $classes[$index]['progress_percent'] = (int) round(($classes[$index]['filled_components'] / $classes[$index]['total_components']) * 100);
            $classes[$index]['is_complete'] = $classes[$index]['progress_percent'] === 100;
            $classes[$index]['issues'] = $this->buildAttentionIssues($classes[$index]);
            $classes[$index]['priority'] = $this->classPriority($classes[$index]);
            $classes[$index]['badge_class'] = $this->statusBadgeClass($classes[$index]['score_status']);
        }

        $studentNames = [
            'Andi', 'Budi', 'Citra', 'Dewi', 'Eka', 'Fadil', 'Gina', 'Hani', 'Irfan', 'Joko',
            'Kiki', 'Lia', 'Maya', 'Nanda', 'Oki', 'Putri', 'Qori', 'Raka', 'Sinta', 'Tegar',
            'Umi', 'Vina', 'Wawan', 'Xena', 'Yusuf', 'Zahra', 'Alif', 'Bella', 'Cahya', 'Dito',
            'Elma', 'Farhan', 'Gilang', 'Helmi', 'Indah', 'Jihan', 'Kevin', 'Lutfi', 'Mira', 'Naufal',
            'Olivia', 'Pandu', 'Qila', 'Rani', 'Satria', 'Tania', 'Umar', 'Vio', 'Wida', 'Zaki',
        ];

        $finalScores = [];
        $remedialStatuses = ['Eligible', 'Terdaftar', 'Dijadwalkan', 'Sudah Dinilai', 'Validated', 'Tidak Mengikuti', 'Dibatalkan'];
        $gradeMap = [
            'A' => 94,
            'AB' => 88,
            'B' => 80,
            'BC' => 74,
            'C' => 66,
            'D' => 55,
            'E' => 38,
        ];
        $gradePattern = [
            1 => ['A', 'AB', 'B', 'B', 'BC', 'AB', 'B', 'A', 'BC', 'B'],
            2 => ['AB', 'B', 'BC', 'C', 'D', 'B', 'AB', 'C', 'BC', 'B'],
            3 => ['B', 'BC', 'C', 'D', 'E', 'D', 'C', 'BC', 'B', 'D'],
            4 => ['A', 'A', 'AB', 'B', 'B', 'AB', 'BC', 'B', 'A', 'AB'],
            5 => ['AB', 'B', 'BC', 'C', 'D', 'E', 'C', 'B', 'BC', 'D'],
        ];

        $studentCounter = 0;
        foreach ($classes as $class) {
            foreach ($gradePattern[$class['id']] as $grade) {
                $studentCounter++;
                $finalScore = $gradeMap[$grade];
                $status = $this->statusFromGrade($grade);

                $finalScores[] = [
                    'id' => $studentCounter,
                    'nim' => sprintf('23%05d', $studentCounter),
                    'student_name' => $studentNames[$studentCounter - 1],
                    'course_id' => $class['course_id'],
                    'course_name' => $class['course_name'],
                    'class_id' => $class['id'],
                    'class_name' => $class['class_name'],
                    'lecturer_name' => $class['lecturer_name'],
                    'final_score' => $finalScore,
                    'grade' => $grade,
                    'status' => $status,
                    'remedial_reason' => $status === 'Remedial' ? 'Nilai akhir di bawah batas lulus' : null,
                    'remedial_status' => $status === 'Remedial' ? $remedialStatuses[$studentCounter % count($remedialStatuses)] : null,
                    'validation_status' => $class['validation_status'],
                    'updated_at' => date('Y-m-d', strtotime('-' . (($studentCounter % 6) + 1) . ' days')),
                ];
            }
        }

        $remedialParticipants = [
            [
                'id' => 1,
                'nim' => '2300003',
                'student_name' => 'Citra',
                'course_id' => 2,
                'course_name' => 'Praktikum Struktur Data',
                'class_id' => 2,
                'class_name' => 'B',
                'final_score' => 74,
                'grade' => 'BC',
                'reason' => 'Bobot tugas belum mencukupi',
                'status' => 'Eligible',
                'remedial_date' => null,
                'validated_at' => null,
            ],
            [
                'id' => 2,
                'nim' => '2300004',
                'student_name' => 'Dewi',
                'course_id' => 2,
                'course_name' => 'Praktikum Struktur Data',
                'class_id' => 2,
                'class_name' => 'B',
                'final_score' => 66,
                'grade' => 'C',
                'reason' => 'Nilai laporan tidak mencapai ambang batas',
                'status' => 'Terdaftar',
                'remedial_date' => '2026-05-19',
                'validated_at' => null,
            ],
            [
                'id' => 3,
                'nim' => '2300013',
                'student_name' => 'Maya',
                'course_id' => 3,
                'course_name' => 'Praktikum Basis Data',
                'class_id' => 3,
                'class_name' => 'A',
                'final_score' => 55,
                'grade' => 'D',
                'reason' => 'Tidak lulus evaluasi praktik utama',
                'status' => 'Dijadwalkan',
                'remedial_date' => '2026-05-21',
                'validated_at' => null,
            ],
            [
                'id' => 4,
                'nim' => '2300022',
                'student_name' => 'Wawan',
                'course_id' => 1,
                'course_name' => 'Praktikum Pemrograman Dasar',
                'class_id' => 4,
                'class_name' => 'B',
                'final_score' => 74,
                'grade' => 'BC',
                'reason' => 'Rerata kuis dan modul rendah',
                'status' => 'Sudah Dinilai',
                'remedial_date' => '2026-05-14',
                'validated_at' => null,
            ],
            [
                'id' => 5,
                'nim' => '2300034',
                'student_name' => 'Helmi',
                'course_id' => 2,
                'course_name' => 'Praktikum Struktur Data',
                'class_id' => 5,
                'class_name' => 'C',
                'final_score' => 66,
                'grade' => 'C',
                'reason' => 'Nilai akhir revisi disetujui dosen',
                'status' => 'Validated',
                'remedial_date' => '2026-05-13',
                'validated_at' => '2026-05-15',
            ],
            [
                'id' => 6,
                'nim' => '2300035',
                'student_name' => 'Indah',
                'course_id' => 2,
                'course_name' => 'Praktikum Struktur Data',
                'class_id' => 5,
                'class_name' => 'C',
                'final_score' => 38,
                'grade' => 'E',
                'reason' => 'Tidak hadir pada remedial',
                'status' => 'Tidak Mengikuti',
                'remedial_date' => '2026-05-13',
                'validated_at' => null,
            ],
            [
                'id' => 7,
                'nim' => '2300041',
                'student_name' => 'Olivia',
                'course_id' => 1,
                'course_name' => 'Praktikum Pemrograman Dasar',
                'class_id' => 1,
                'class_name' => 'A',
                'final_score' => 66,
                'grade' => 'C',
                'reason' => 'Kesalahan pengumpulan berkas remedial',
                'status' => 'Dibatalkan',
                'remedial_date' => null,
                'validated_at' => null,
            ],
        ];

        foreach ($remedialParticipants as $index => $participant) {
            $remedialParticipants[$index]['badge_class'] = $this->remedialBadgeClass($participant['status']);
        }

        $activityLogs = [
            ['time' => '2026-05-15 10:20:00', 'user' => 'Dr. Rina Prameswari', 'role' => 'Dosen', 'activity' => 'Memvalidasi nilai kelas PRAK-IF101 A', 'module' => 'Validasi Nilai', 'detail' => 'Status berubah menjadi Validated'],
            ['time' => '2026-05-15 09:45:00', 'user' => 'Alya', 'role' => 'Asisten', 'activity' => 'Menginput nilai laporan untuk PRAK-IF101 A', 'module' => 'Input Nilai', 'detail' => '3 nilai komponen baru ditambahkan'],
            ['time' => '2026-05-15 09:10:00', 'user' => 'Sistem', 'role' => 'System', 'activity' => 'Mengunci nilai kelas PRAK-IF101 B', 'module' => 'Kunci Nilai', 'detail' => 'Lock otomatis setelah validasi'],
            ['time' => '2026-05-15 08:30:00', 'user' => 'Koordinator', 'role' => 'Koordinator Praktikum', 'activity' => 'Membuat request revisi nilai PRAK-IF205 B', 'module' => 'Revisi Nilai', 'detail' => 'Terdapat komponen kosong'],
            ['time' => '2026-05-14 16:15:00', 'user' => 'Nadia', 'role' => 'Asisten', 'activity' => 'Memvalidasi hasil remedial mahasiswa', 'module' => 'Remedial', 'detail' => '5 peserta remedial divalidasi'],
            ['time' => '2026-05-14 15:50:00', 'user' => 'Sistem', 'role' => 'System', 'activity' => 'Laporan nilai akhir diekspor', 'module' => 'Export Laporan', 'detail' => 'File PDF dan Excel berhasil dibuat'],
            ['time' => '2026-05-14 14:05:00', 'user' => 'Admin', 'role' => 'Admin', 'activity' => 'Mengimpor data mahasiswa praktikum', 'module' => 'Import Data', 'detail' => '50 data mahasiswa berhasil diimpor'],
            ['time' => '2026-05-14 11:55:00', 'user' => 'Dr. Bayu Hidayat', 'role' => 'Dosen', 'activity' => 'Meninjau distribusi nilai kelas PRAK-IF205 B', 'module' => 'Distribusi Nilai', 'detail' => 'Status reviu disimpan'],
        ];

        $validationLogs = [];
        foreach ($classes as $class) {
            $validationLogs[] = [
                'course_name' => $class['course_name'],
                'class_name' => $class['class_name'],
                'lecturer_name' => $class['lecturer_name'],
                'score_status' => $class['score_status'],
                'validation_status' => $class['validation_status'],
                'submit_date' => $class['submit_date'],
                'validation_date' => $class['validation_date'],
                'revision_count' => $class['revision_count'],
                'badge_class' => $this->statusBadgeClass($class['validation_status']),
            ];
        }

        return [
            'courses' => $courses,
            'classes' => $classes,
            'final_scores' => $finalScores,
            'remedial_participants' => $remedialParticipants,
            'activity_logs' => $activityLogs,
            'validation_logs' => $validationLogs,
            'filter_options' => [
                'academic_years' => ['2025/2026', '2024/2025'],
                'semesters' => ['Ganjil', 'Genap', 'Pendek'],
                'study_programs' => ['Informatika', 'Sistem Informasi'],
                'courses' => $courses,
                'classes' => array_map(static function (array $class): array {
                    return [
                        'id' => $class['id'],
                        'label' => $class['course_name'] . ' - Kelas ' . $class['class_name'],
                        'course_id' => $class['course_id'],
                    ];
                }, $classes),
                'lecturers' => array_values(array_unique(array_map(static fn (array $class): string => $class['lecturer_name'], $classes))),
                'score_statuses' => ['Draft', 'Submitted', 'Reviewed', 'Validated', 'Locked', 'Revision Requested', 'Revised'],
            ],
        ];
    }

    private function filterClasses(array $classes, array $filters): array
    {
        return array_values(array_filter($classes, static function (array $class) use ($filters): bool {
            if ($filters['academic_year'] !== '' && $filters['academic_year'] !== '2025/2026') {
                return false;
            }

            if ($filters['semester'] !== '' && $filters['semester'] !== 'Ganjil') {
                return false;
            }

            if ($filters['study_program'] !== '' && $class['study_program'] !== $filters['study_program']) {
                return false;
            }

            if ($filters['course_id'] !== '' && (string) $class['course_id'] !== (string) $filters['course_id']) {
                return false;
            }

            if ($filters['class_id'] !== '' && (string) $class['id'] !== (string) $filters['class_id']) {
                return false;
            }

            if ($filters['lecturer'] !== '' && $class['lecturer_name'] !== $filters['lecturer']) {
                return false;
            }

            if ($filters['score_status'] !== '' && $class['score_status'] !== $filters['score_status']) {
                return false;
            }

            return true;
        }));
    }

    private function filterFinalScores(array $finalScores, array $classes): array
    {
        $classIds = array_column($classes, 'id');

        return array_values(array_filter($finalScores, static fn (array $row): bool => in_array($row['class_id'], $classIds, true)));
    }

    private function filterRemedialParticipants(array $participants, array $classes): array
    {
        $classIds = array_column($classes, 'id');

        return array_values(array_filter($participants, static fn (array $row): bool => in_array($row['class_id'], $classIds, true)));
    }

    private function filterActivities(array $activities, array $classes, array $filters): array
    {
        if (empty($classes)) {
            return [];
        }

        $lecturers = array_unique(array_column($classes, 'lecturer_name'));

        return array_values(array_filter($activities, static function (array $row) use ($lecturers, $filters): bool {
            if ($filters['lecturer'] !== '' && ! in_array($row['user'], $lecturers, true) && $row['role'] !== 'Koordinator Praktikum') {
                return false;
            }

            if ($filters['score_status'] !== '' && ! str_contains($row['activity'], 'nilai') && ! str_contains($row['module'], 'Nilai')) {
                return false;
            }

            return true;
        }));
    }

    private function filterValidations(array $validationLogs, array $classes): array
    {
        if (empty($classes)) {
            return [];
        }

        $classKeySet = [];
        foreach ($classes as $class) {
            $classKeySet[$class['course_name'] . '|' . $class['class_name']] = true;
        }

        return array_values(array_filter($validationLogs, static function (array $row) use ($classKeySet): bool {
            return isset($classKeySet[$row['course_name'] . '|' . $row['class_name']]);
        }));
    }

    private function buildSummaryCards(array $classes, array $finalScores, array $remedialParticipants, array $validationLogs): array
    {
        $courseIds = array_unique(array_column($classes, 'course_id'));
        $lecturers = array_unique(array_column($classes, 'lecturer_name'));
        $assistants = array_unique(array_column($classes, 'assistant_name'));
        $students = array_unique(array_column($finalScores, 'nim'));

        return [
            ['title' => 'Total Mata Kuliah Praktikum Aktif', 'value' => count($courseIds), 'icon' => 'bi-journal-bookmark-fill', 'color' => 'primary', 'link' => '#monitoring-kelas'],
            ['title' => 'Total Kelas Praktikum Aktif', 'value' => count($classes), 'icon' => 'bi-collection-play-fill', 'color' => 'success', 'link' => '#monitoring-kelas'],
            ['title' => 'Total Dosen Pengampu', 'value' => count($lecturers), 'icon' => 'bi-person-badge-fill', 'color' => 'info', 'link' => '#validasi-nilai'],
            ['title' => 'Total Asisten Praktikum', 'value' => count($assistants), 'icon' => 'bi-people-fill', 'color' => 'warning', 'link' => '#validasi-nilai'],
            ['title' => 'Total Mahasiswa Terdaftar', 'value' => count($students), 'icon' => 'bi-mortarboard-fill', 'color' => 'dark', 'link' => '#remedial-monitoring'],
            ['title' => 'Total Mahasiswa Remedial', 'value' => count($remedialParticipants), 'icon' => 'bi-arrow-repeat', 'color' => 'danger', 'link' => '#remedial-monitoring'],
            ['title' => 'Total Nilai Sudah Tervalidasi', 'value' => count(array_filter($validationLogs, static fn (array $row): bool => in_array($row['validation_status'], ['Validated', 'Locked'], true))), 'icon' => 'bi-shield-check', 'color' => 'success', 'link' => '#validasi-nilai'],
            ['title' => 'Total Nilai Belum Lengkap', 'value' => count(array_filter($classes, static fn (array $row): bool => $row['progress_percent'] < 100 || $row['template_weight_total'] < 100)), 'icon' => 'bi-exclamation-triangle-fill', 'color' => 'secondary', 'link' => '#perlu-perhatian'],
        ];
    }

    private function buildProgressRows(array $classes): array
    {
        return array_map(static function (array $class): array {
            return [
                'course_name' => $class['course_name'],
                'class_name' => $class['class_name'],
                'lecturer_name' => $class['lecturer_name'],
                'student_count' => $class['student_count'],
                'complete_components' => $class['filled_components'],
                'incomplete_components' => $class['total_components'] - $class['filled_components'],
                'progress_percent' => $class['progress_percent'],
                'status' => $class['score_status'],
                'badge_class' => $class['badge_class'],
                'detail_url' => '#',
            ];
        }, $classes);
    }

    private function buildAttentionRows(array $classes): array
    {
        $rows = [];

        foreach ($classes as $class) {
            $issues = $class['issues'];

            if (empty($issues)) {
                continue;
            }

            $rows[] = [
                'course_name' => $class['course_name'],
                'class_name' => $class['class_name'],
                'problem' => implode('; ', $issues),
                'priority' => $class['priority'],
                'priority_badge_class' => $this->priorityBadgeClass($class['priority']),
                'deadline' => $class['deadline'],
                'detail_url' => '#',
            ];
        }

        return $rows;
    }

    private function buildRemedialRows(array $remedialParticipants, array $finalScores): array
    {
        $scoreLookup = [];
        foreach ($finalScores as $row) {
            $scoreLookup[$row['nim']] = $row;
        }

        return array_map(static function (array $participant) use ($scoreLookup): array {
            $source = $scoreLookup[$participant['nim']] ?? null;

            return [
                'nim' => $participant['nim'],
                'student_name' => $participant['student_name'],
                'course_name' => $participant['course_name'],
                'class_name' => $participant['class_name'],
                'final_score' => $source['final_score'] ?? $participant['final_score'],
                'grade' => $source['grade'] ?? $participant['grade'],
                'reason' => $participant['reason'],
                'status' => $participant['status'],
                'badge_class' => $participant['badge_class'],
                'detail_url' => '#',
            ];
        }, $remedialParticipants);
    }

    private function buildValidationRows(array $validationLogs): array
    {
        return array_map(static function (array $row): array {
            return [
                'course_name' => $row['course_name'],
                'class_name' => $row['class_name'],
                'lecturer_name' => $row['lecturer_name'],
                'score_status' => $row['score_status'],
                'validation_status' => $row['validation_status'],
                'submit_date' => $row['submit_date'] ?? '-',
                'validation_date' => $row['validation_date'] ?? '-',
                'revision_count' => $row['revision_count'],
                'badge_class' => $row['badge_class'],
                'detail_url' => '#',
            ];
        }, $validationLogs);
    }

    private function buildActivityRows(array $activities): array
    {
        return array_map(static function (array $row): array {
            return [
                'time' => $row['time'],
                'user' => $row['user'],
                'role' => $row['role'],
                'activity' => $row['activity'],
                'module' => $row['module'],
                'detail' => $row['detail'],
            ];
        }, $activities);
    }

    private function buildRemedialStats(array $remedialParticipants, array $finalScores): array
    {
        $counts = [
            'eligible' => 0,
            'registered' => 0,
            'graded' => 0,
            'validated' => 0,
            'not_attended' => 0,
        ];

        foreach ($remedialParticipants as $participant) {
            $status = $participant['status'];

            if ($status === 'Eligible') {
                $counts['eligible']++;
            }

            if (in_array($status, ['Terdaftar', 'Dijadwalkan', 'Sudah Dinilai', 'Validated', 'Tidak Mengikuti', 'Dibatalkan'], true)) {
                $counts['registered']++;
            }

            if (in_array($status, ['Sudah Dinilai', 'Validated'], true)) {
                $counts['graded']++;
            }

            if ($status === 'Validated') {
                $counts['validated']++;
            }

            if ($status === 'Tidak Mengikuti') {
                $counts['not_attended']++;
            }
        }

        $counts['eligible'] = max($counts['eligible'], count(array_filter($finalScores, static fn (array $row): bool => $row['status'] === 'Remedial')));

        return $counts;
    }

    private function buildGradeDistribution(array $finalScores): array
    {
        $order = ['A', 'AB', 'B', 'BC', 'C', 'D', 'E'];
        $counts = array_fill_keys($order, 0);

        foreach ($finalScores as $row) {
            if (isset($counts[$row['grade']])) {
                $counts[$row['grade']]++;
            }
        }

        return $counts;
    }

    private function buildStudentStatusDistribution(array $finalScores): array
    {
        $counts = ['Lulus' => 0, 'Remedial' => 0, 'Tidak Lulus' => 0];

        foreach ($finalScores as $row) {
            if (isset($counts[$row['status']])) {
                $counts[$row['status']]++;
            }
        }

        return $counts;
    }

    private function buildValidationTrend(array $activities, array $validations): array
    {
        return [
            ['week' => 'Minggu 1', 'submitted' => 7, 'validated' => 4, 'locked' => 2],
            ['week' => 'Minggu 2', 'submitted' => 9, 'validated' => 6, 'locked' => 4],
            ['week' => 'Minggu 3', 'submitted' => 11, 'validated' => 8, 'locked' => 6],
            ['week' => 'Minggu 4', 'submitted' => 13, 'validated' => 11, 'locked' => 9],
        ];
    }

    private function buildQuickActions(array $filters): array
    {
        $query = http_build_query($filters);

        return [
            ['label' => 'Monitoring Kelas', 'icon' => 'bi-collection', 'url' => site_url('coordinator/classes' . ($query !== '' ? '?' . $query : '')), 'color' => 'primary'],
            ['label' => 'Kelas Perlu Perhatian', 'icon' => 'bi-exclamation-triangle', 'url' => site_url('coordinator/attention' . ($query !== '' ? '?' . $query : '')), 'color' => 'warning'],
            ['label' => 'Monitoring Remedial', 'icon' => 'bi-arrow-repeat', 'url' => site_url('coordinator/remedial' . ($query !== '' ? '?' . $query : '')), 'color' => 'danger'],
            ['label' => 'Validasi Nilai', 'icon' => 'bi-shield-check', 'url' => site_url('coordinator/validation' . ($query !== '' ? '?' . $query : '')), 'color' => 'success'],
            ['label' => 'Aktivitas Terbaru', 'icon' => 'bi-clock-history', 'url' => site_url('coordinator/activity' . ($query !== '' ? '?' . $query : '')), 'color' => 'info'],
        ];
    }

    private function classPriority(array $class): string
    {
        $issues = $this->buildAttentionIssues($class);
        $deadlineNear = $this->isDeadlineNear($class['deadline']);

        if ($deadlineNear || count($issues) >= 3 || $class['progress_percent'] < 75) {
            return 'High';
        }

        if (count($issues) >= 2 || $class['progress_percent'] < 100) {
            return 'Medium';
        }

        return 'Low';
    }

    private function buildAttentionIssues(array $class): array
    {
        $issues = [];

        if ($class['progress_percent'] < 100) {
            $issues[] = 'Nilai belum lengkap';
        }

        if ($class['attendance_completion'] < 100) {
            $issues[] = 'Kehadiran belum lengkap';
        }

        if ($class['template_weight_total'] < 100) {
            $issues[] = 'Total bobot template belum 100%';
        }

        if ($class['remedial_count'] >= 3) {
            $issues[] = 'Banyak mahasiswa eligible remedial';
        }

        if (! in_array($class['validation_status'], ['Validated', 'Locked'], true)) {
            $issues[] = 'Nilai belum divalidasi dosen';
        }

        if ($class['score_status'] !== 'Locked' && $this->isDeadlineNear($class['deadline'])) {
            $issues[] = 'Nilai belum locked mendekati deadline';
        }

        return $issues;
    }

    private function isDeadlineNear(string $deadline): bool
    {
        $deadlineDate = strtotime($deadline);

        if ($deadlineDate === false) {
            return false;
        }

        $difference = (int) floor(($deadlineDate - strtotime('2026-05-15')) / 86400);

        return $difference <= 3;
    }

    private function countFilledValues(array $values): int
    {
        return count(array_filter($values, static fn ($value): bool => $value !== null));
    }

    private function statusFromGrade(string $grade): string
    {
        return match ($grade) {
            'A', 'AB', 'B' => 'Lulus',
            'BC' => 'Remedial',
            default => 'Tidak Lulus',
        };
    }

    private function statusBadgeClass(string $status): string
    {
        return match ($status) {
            'Validated', 'Lulus' => 'bg-success',
            'Locked' => 'bg-dark',
            'Submitted' => 'bg-primary',
            'Reviewed' => 'bg-info text-dark',
            'Revision Requested' => 'bg-warning text-dark',
            'Revised' => 'bg-secondary',
            'Draft' => 'bg-secondary',
            'Remedial' => 'bg-warning text-dark',
            'Tidak Lulus' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    private function remedialBadgeClass(string $status): string
    {
        return match ($status) {
            'Eligible' => 'bg-secondary',
            'Terdaftar', 'Dijadwalkan' => 'bg-primary',
            'Sudah Dinilai' => 'bg-info text-dark',
            'Validated' => 'bg-success',
            'Tidak Mengikuti' => 'bg-danger',
            'Dibatalkan' => 'bg-dark',
            default => 'bg-secondary',
        };
    }

    private function priorityBadgeClass(string $priority): string
    {
        return match ($priority) {
            'High' => 'bg-danger',
            'Medium' => 'bg-warning text-dark',
            default => 'bg-secondary',
        };
    }

    private function paginate(array $rows, int $perPage, string $pageKey): array
    {
        $page = max(1, (int) ($this->request->getGet($pageKey) ?? 1));
        $total = count($rows);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        return [
            'items' => array_slice($rows, $offset, $perPage),
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'totalPages' => $totalPages,
                'pageKey' => $pageKey,
            ],
        ];
    }

    private function normalizeValues(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_string($value)) {
            return array_values(array_filter(array_map('trim', preg_split('/[,|;]/', $value) ?: [])));
        }

        if (! is_array($value)) {
            return [(string) $value];
        }

        $normalized = [];

        foreach ($value as $item) {
            if (is_array($item)) {
                foreach (['role', 'name', 'permission', 'code'] as $key) {
                    if (isset($item[$key]) && $item[$key] !== '') {
                        $normalized[] = (string) $item[$key];
                        continue 2;
                    }
                }

                continue;
            }

            if ($item !== null && $item !== '') {
                $normalized[] = (string) $item;
            }
        }

        return array_values(array_unique($normalized));
    }
}
