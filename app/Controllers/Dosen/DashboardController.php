<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\LecturerModel;
use CodeIgniter\Exceptions\PageForbiddenException;

class DashboardController extends BaseController
{
    private const STATUS_ORDER = ['Draft', 'Submitted', 'Reviewed', 'Validated', 'Locked', 'Revision Requested'];

    private const STATUS_BADGES = [
        'Draft' => 'secondary',
        'Submitted' => 'warning',
        'Reviewed' => 'info',
        'Validated' => 'success',
        'Locked' => 'dark',
        'Revision Requested' => 'danger',
    ];

    private const RISK_BADGES = [
        'Nilai rendah' => 'danger',
        'Kehadiran kurang' => 'warning',
        'Komponen nilai belum lengkap' => 'info',
        'UAS belum hadir' => 'secondary',
        'Eligible remedial' => 'warning',
    ];

    private const REMEDIAL_BADGES = [
        'Eligible' => 'warning',
        'Terdaftar' => 'info',
        'Dijadwalkan' => 'primary',
        'Selesai' => 'success',
        'Ditolak' => 'danger',
    ];

    public function index(): string
    {
        if (! $this->canAccessDashboard()) {
            throw new PageForbiddenException('Anda tidak memiliki akses ke dashboard dosen.');
        }

        $data = $this->buildDashboardData();

        return view('dosen/dashboard/index', $data);
    }

    private function buildDashboardData(): array
    {
        $session = session();
        $userId = (string) ($session->get('user_id') ?? '');
        $displayName = (string) ($session->get('full_name') ?: $session->get('name') ?: $session->get('username') ?: 'Pengguna');
        $roles = $this->normalizeRoles($session->get('roles') ?? $session->get('role'));
        $activeRole = strtolower(trim((string) ($session->get('role_active') ?: $session->get('role') ?: ($roles[0] ?? ''))));

        $academicContext = $this->resolveAcademicContext();
        $lecturerContext = $this->resolveLecturerContext($userId, $displayName, $activeRole);

        $classRows = $this->loadClassRows($lecturerContext, $academicContext);

        if ($classRows === []) {
            $classRows = $this->buildFallbackClassRows($displayName, $academicContext, $lecturerContext['mode'] ?? 'lecturer');
        }

        $summary = $this->buildSummary($classRows);
        $validationRows = $this->loadValidationRows($classRows);
        $riskRows = $this->loadRiskRows($classRows);
        $remedialRows = $this->loadRemedialRows($classRows);
        $chartData = $this->buildChartData($classRows);

        return [
            'title' => 'Dashboard Dosen',
            'userName' => $displayName,
            'lecturerName' => $lecturerContext['lecturer_name'] ?? $displayName,
            'academicYear' => $academicContext['academic_year'],
            'semesterLabel' => $academicContext['semester_label'],
            'todayLabel' => $this->formatDateIndo(date('Y-m-d')),
            'summaryCards' => $summary['cards'],
            'quickActions' => $this->buildQuickActions(),
            'classRows' => $classRows,
            'validationRows' => array_slice($validationRows, 0, 6),
            'riskRows' => array_slice($riskRows, 0, 6),
            'remedialRows' => array_slice($remedialRows, 0, 6),
            'chartData' => $chartData,
            'classTotal' => $summary['class_total'],
            'studentTotal' => $summary['student_total'],
            'validationTotal' => $summary['validation_total'],
            'remedialTotal' => $summary['remedial_total'],
            'progressNilai' => $summary['progress_nilai'],
            'progressKehadiran' => $summary['progress_kehadiran'],
            'statusLegend' => $summary['status_legend'],
        ];
    }

    private function canAccessDashboard(): bool
    {
        $session = session();

        if ($session->get('is_logged_in') !== true) {
            return false;
        }

        $roles = $this->normalizeRoles($session->get('roles') ?? $session->get('role'));
        $activeRole = strtolower(trim((string) ($session->get('role_active') ?: $session->get('role') ?: ($roles[0] ?? ''))));

        if ($activeRole !== '' && in_array($activeRole, ['dosen', 'admin', 'koordinator'], true)) {
            return true;
        }

        return array_intersect($roles, ['dosen', 'admin', 'koordinator']) !== [];
    }

    private function resolveAcademicContext(): array
    {
        $month = (int) date('n');
        $academicYear = $month >= 7
            ? date('Y') . '/' . (date('Y') + 1)
            : (date('Y') - 1) . '/' . date('Y');

        $semesterLabel = $month >= 7 ? 'Semester Ganjil' : 'Semester Genap';

        return [
            'academic_year' => $academicYear,
            'semester_label' => $semesterLabel,
        ];
    }

    private function resolveLecturerContext(string $userId, string $displayName, string $activeRole): array
    {
        if (in_array($activeRole, ['admin', 'koordinator'], true)) {
            return [
                'mode' => 'global',
                'user_id' => $userId,
                'lecturer_id' => null,
                'lecturer_name' => $displayName,
            ];
        }

        $lecturerModel = new LecturerModel();
        $lecturer = null;

        if ($userId !== '') {
            $lecturer = $lecturerModel->where('user_id', $userId)->first();
        }

        if ($lecturer === null && $displayName !== '') {
            $lecturer = $lecturerModel->where('lecturer_name', $displayName)->first();
        }

        return [
            'mode' => 'lecturer',
            'user_id' => $userId,
            'lecturer_id' => $lecturer['id'] ?? null,
            'lecturer_name' => $lecturer['lecturer_name'] ?? $displayName,
        ];
    }

    private function loadClassRows(array $lecturerContext, array $academicContext): array
    {
        $db = db_connect();

        try {
            if (! $db->tableExists('practicum_classes') || ! $db->tableExists('courses')) {
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
                'pc.lecturer_id',
                'pc.status',
                'pc.deadline_at',
                'c.course_code',
                'c.course_name',
                'c.credits',
                'l.lecturer_name',
            ]);
            $builder->join('courses c', 'c.id = pc.course_id', 'left');
            $builder->join('lecturers l', 'l.id = pc.lecturer_id', 'left');
            $builder->where('pc.status', 'active');

            if ($lecturerContext['mode'] === 'lecturer' && $lecturerContext['lecturer_id'] !== null) {
                $builder->where('pc.lecturer_id', (int) $lecturerContext['lecturer_id']);
            }

            $rows = $builder->get()->getResultArray();

            if ($rows === []) {
                return [];
            }

            $classIds = array_values(array_filter(array_map(static fn (array $row): int => (int) ($row['id'] ?? 0), $rows)));
            $studentCounts = $this->loadStudentCounts($classIds);
            $scoreStats = $this->loadScoreStats($classIds);
            $finalStats = $this->loadFinalStats($classIds);
            $remedialCounts = $this->loadRemedialCounts($classIds);

            $preparedRows = [];

            foreach ($rows as $row) {
                $classId = (int) ($row['id'] ?? 0);
                $studentCount = (int) ($studentCounts[$classId] ?? 0);
                $scoreStat = $scoreStats[$classId] ?? ['filled' => 0, 'expected' => 0, 'component_count' => 0];
                $finalStat = $finalStats[$classId] ?? ['average_score' => null, 'validation_status' => 'Draft', 'low_count' => 0];

                $preparedRows[] = [
                    'id' => $classId,
                    'academic_year' => $academicContext['academic_year'],
                    'semester' => $academicContext['semester_label'],
                    'course_name' => (string) ($row['course_name'] ?? '-'),
                    'course_code' => (string) ($row['course_code'] ?? '-'),
                    'class_name' => (string) ($row['class_name'] ?? $row['class_code'] ?? '-'),
                    'lecturer_name' => (string) ($row['lecturer_name'] ?? $lecturerContext['lecturer_name'] ?? '-'),
                    'student_count' => $studentCount,
                    'progress_nilai' => $this->percentage($scoreStat['filled'] ?? 0, $scoreStat['expected'] ?? 0),
                    'progress_kehadiran' => 0,
                    'average_score' => $finalStat['average_score'] !== null ? round((float) $finalStat['average_score'], 1) : 0.0,
                    'status' => $status = $this->normalizeStatus((string) ($finalStat['validation_status'] ?? $row['status'] ?? 'Draft')),
                    'status_badge' => $this->statusBadgeClass($status),
                    'low_students' => (int) ($finalStat['low_count'] ?? 0),
                    'remedial_count' => (int) ($remedialCounts[$classId] ?? 0),
                    'detail_url' => site_url('dosen/dashboard#kelas-saya'),
                    'input_url' => site_url('dosen/dashboard#kelas-saya'),
                    'rekap_url' => site_url('dosen/dashboard#kelas-saya'),
                    'validation_url' => site_url('dosen/dashboard#validasi-nilai'),
                    'remedial_url' => site_url('dosen/dashboard#remedial'),
                    'report_url' => site_url('dosen/dashboard#laporan'),
                ];
            }

            return $preparedRows;
        } catch (\Throwable) {
            return [];
        }
    }

    private function loadStudentCounts(array $classIds): array
    {
        if ($classIds === []) {
            return [];
        }

        $db = db_connect();

        try {
            if ($db->tableExists('class_students')) {
                $rows = $db->table('class_students')
                    ->select('class_id, COUNT(DISTINCT student_id) AS total')
                    ->whereIn('class_id', $classIds)
                    ->groupBy('class_id')
                    ->get()
                    ->getResultArray();

                $counts = [];

                foreach ($rows as $row) {
                    $counts[(int) ($row['class_id'] ?? 0)] = (int) ($row['total'] ?? 0);
                }

                return $counts;
            }
        } catch (\Throwable) {
        }

        return [];
    }

    private function loadScoreStats(array $classIds): array
    {
        if ($classIds === []) {
            return [];
        }

        $db = db_connect();

        try {
            if (! $db->tableExists('score_entries')) {
                return [];
            }

            $rows = $db->table('score_entries')
                ->select('class_id, student_id, component_id, score_value')
                ->whereIn('class_id', $classIds)
                ->get()
                ->getResultArray();

            $stats = [];

            foreach ($rows as $row) {
                $classId = (int) ($row['class_id'] ?? 0);
                $stats[$classId]['students'][(int) ($row['student_id'] ?? 0)] = true;
                $stats[$classId]['components'][(int) ($row['component_id'] ?? 0)] = true;
                if ($row['score_value'] !== null && $row['score_value'] !== '') {
                    $stats[$classId]['filled'] = ($stats[$classId]['filled'] ?? 0) + 1;
                }
            }

            foreach ($stats as $classId => $stat) {
                $studentCount = count($stat['students'] ?? []);
                $componentCount = max(count($stat['components'] ?? []), 1);
                $expected = max($studentCount * $componentCount, $stat['filled'] ?? 0, 1);

                $stats[$classId] = [
                    'filled' => (int) ($stat['filled'] ?? 0),
                    'expected' => $expected,
                    'component_count' => $componentCount,
                ];
            }

            return $stats;
        } catch (\Throwable) {
            return [];
        }
    }

    private function loadFinalStats(array $classIds): array
    {
        if ($classIds === []) {
            return [];
        }

        $db = db_connect();

        try {
            if (! $db->tableExists('final_scores')) {
                return [];
            }

            $rows = $db->table('final_scores')
                ->select('class_id, student_id, final_score, grade_letter, status, validation_status')
                ->whereIn('class_id', $classIds)
                ->get()
                ->getResultArray();

            $stats = [];

            foreach ($rows as $row) {
                $classId = (int) ($row['class_id'] ?? 0);
                $score = $row['final_score'];

                if ($score !== null && $score !== '') {
                    $stats[$classId]['scores'][] = (float) $score;
                    if ((float) $score < 61) {
                        $stats[$classId]['low_count'] = ($stats[$classId]['low_count'] ?? 0) + 1;
                    }
                }

                $validationStatus = $this->normalizeStatus((string) ($row['validation_status'] ?? $row['status'] ?? 'Draft'));
                $stats[$classId]['validation_status'] = $this->pickHigherStatus($stats[$classId]['validation_status'] ?? 'Draft', $validationStatus);
            }

            foreach ($stats as $classId => $stat) {
                $scores = $stat['scores'] ?? [];
                $stats[$classId]['average_score'] = $scores === [] ? null : array_sum($scores) / count($scores);
                $stats[$classId]['low_count'] = (int) ($stat['low_count'] ?? 0);
                $stats[$classId]['validation_status'] = (string) ($stat['validation_status'] ?? 'Draft');
            }

            return $stats;
        } catch (\Throwable) {
            return [];
        }
    }

    private function loadRemedialCounts(array $classIds): array
    {
        if ($classIds === []) {
            return [];
        }

        $db = db_connect();

        try {
            if (! $db->tableExists('remedial_participants')) {
                return [];
            }

            $rows = $db->table('remedial_participants')
                ->select('class_id, status')
                ->whereIn('class_id', $classIds)
                ->get()
                ->getResultArray();

            $counts = [];

            foreach ($rows as $row) {
                $classId = (int) ($row['class_id'] ?? 0);
                $status = $this->normalizeRemedialStatus((string) ($row['status'] ?? 'Eligible'));
                if (in_array($status, ['Eligible', 'Terdaftar', 'Dijadwalkan'], true)) {
                    $counts[$classId] = ($counts[$classId] ?? 0) + 1;
                }
            }

            return $counts;
        } catch (\Throwable) {
            return [];
        }
    }

    private function loadValidationRows(array $classRows): array
    {
        $db = db_connect();

        try {
            if (! $db->tableExists('score_entries') && ! $db->tableExists('final_scores')) {
                return [];
            }

            $rows = [];
            $classMap = [];

            foreach ($classRows as $classRow) {
                $classMap[(int) ($classRow['id'] ?? 0)] = $classRow;
            }

            if ($db->tableExists('score_entries')) {
                $scoreEntries = $db->table('score_entries')
                    ->select('class_id, student_id, component_id, score_value, submitted_by, submitted_at')
                    ->whereIn('class_id', array_keys($classMap))
                    ->orderBy('submitted_at', 'DESC')
                    ->get()
                    ->getResultArray();

                foreach ($scoreEntries as $entry) {
                    if ($entry['score_value'] === null || $entry['score_value'] === '') {
                        continue;
                    }

                    $classId = (int) ($entry['class_id'] ?? 0);
                    if (! isset($classMap[$classId])) {
                        continue;
                    }

                    $rows[] = [
                        'course_name' => (string) ($classMap[$classId]['course_name'] ?? '-'),
                        'class_name' => (string) ($classMap[$classId]['class_name'] ?? '-'),
                        'component_name' => 'Komponen #' . (string) ($entry['component_id'] ?? '-'),
                        'submitted_by' => (string) ($entry['submitted_by'] ?? 'Asisten Praktikum'),
                        'submitted_at' => $this->formatDateTime((string) ($entry['submitted_at'] ?? '')),
                        'status' => 'Submitted',
                        'badge_class' => self::STATUS_BADGES['Submitted'],
                        'review_url' => site_url('dosen/dashboard#validasi-nilai'),
                    ];
                }
            }

            if ($db->tableExists('final_scores')) {
                $finalScores = $db->table('final_scores')
                    ->select('class_id, student_id, final_score, status, validation_status, validated_by, validated_at, updated_at')
                    ->whereIn('class_id', array_keys($classMap))
                    ->whereIn('validation_status', ['Submitted', 'Reviewed'])
                    ->get()
                    ->getResultArray();

                foreach ($finalScores as $score) {
                    $classId = (int) ($score['class_id'] ?? 0);
                    if (! isset($classMap[$classId])) {
                        continue;
                    }

                    $status = $this->normalizeStatus((string) ($score['validation_status'] ?? $score['status'] ?? 'Submitted'));

                    $rows[] = [
                        'course_name' => (string) ($classMap[$classId]['course_name'] ?? '-'),
                        'class_name' => (string) ($classMap[$classId]['class_name'] ?? '-'),
                        'component_name' => 'Nilai Akhir',
                        'submitted_by' => 'Sistem',
                        'submitted_at' => $this->formatDateTime((string) ($score['validated_at'] ?? $score['updated_at'] ?? '')),
                        'status' => $status,
                        'badge_class' => self::STATUS_BADGES[$status] ?? 'warning',
                        'review_url' => site_url('dosen/dashboard#validasi-nilai'),
                    ];
                }
            }

            return array_slice($rows, 0, 6);
        } catch (\Throwable) {
            return [];
        }
    }

    private function loadRiskRows(array $classRows): array
    {
        $db = db_connect();

        try {
            if (! $db->tableExists('final_scores')) {
                return $this->buildFallbackRiskRows($classRows);
            }

            $classMap = [];
            foreach ($classRows as $classRow) {
                $classMap[(int) ($classRow['id'] ?? 0)] = $classRow;
            }

            $studentRows = [];

            $finalScores = $db->table('final_scores fs')
                ->select('fs.class_id, fs.student_id, fs.final_score, fs.grade_letter, fs.validation_status, u.name AS student_name')
                ->join('users u', 'u.id = fs.student_id', 'left')
                ->whereIn('fs.class_id', array_keys($classMap))
                ->get()
                ->getResultArray();

            foreach ($finalScores as $score) {
                $classId = (int) ($score['class_id'] ?? 0);
                $finalScore = $score['final_score'];
                $studentName = (string) ($score['student_name'] ?? ('Mahasiswa ' . ($score['student_id'] ?? '-')));

                if (! isset($classMap[$classId])) {
                    continue;
                }

                if ($finalScore === null || (float) $finalScore < 61) {
                    $riskStatus = $finalScore === null ? 'Komponen nilai belum lengkap' : 'Nilai rendah';

                    $studentRows[] = [
                        'nim' => 'STD-' . str_pad((string) ($score['student_id'] ?? 0), 4, '0', STR_PAD_LEFT),
                        'student_name' => $studentName,
                        'course_name' => (string) ($classMap[$classId]['course_name'] ?? '-'),
                        'class_name' => (string) ($classMap[$classId]['class_name'] ?? '-'),
                        'temporary_score' => $finalScore === null ? '-' : number_format((float) $finalScore, 1),
                        'attendance' => 0,
                        'risk_status' => $riskStatus,
                        'badge_class' => self::RISK_BADGES[$riskStatus] ?? 'danger',
                        'detail_url' => site_url('dosen/dashboard#kelas-saya'),
                    ];
                }
            }

            if ($studentRows === []) {
                return $this->buildFallbackRiskRows($classRows);
            }

            return array_slice($studentRows, 0, 6);
        } catch (\Throwable) {
            return $this->buildFallbackRiskRows($classRows);
        }
    }

    private function loadRemedialRows(array $classRows): array
    {
        $db = db_connect();

        try {
            if (! $db->tableExists('remedial_participants')) {
                return $this->buildFallbackRemedialRows($classRows);
            }

            $classMap = [];
            foreach ($classRows as $classRow) {
                $classMap[(int) ($classRow['id'] ?? 0)] = $classRow;
            }

            $rows = [];

            $participants = $db->table('remedial_participants rp')
                ->select('rp.class_id, rp.student_id, rp.status, rp.reason, u.name AS student_name, fs.final_score, fs.grade_letter')
                ->join('users u', 'u.id = rp.student_id', 'left')
                ->join('final_scores fs', 'fs.class_id = rp.class_id AND fs.student_id = rp.student_id', 'left')
                ->whereIn('rp.class_id', array_keys($classMap))
                ->get()
                ->getResultArray();

            foreach ($participants as $participant) {
                $classId = (int) ($participant['class_id'] ?? 0);
                if (! isset($classMap[$classId])) {
                    continue;
                }

                $status = $this->normalizeRemedialStatus((string) ($participant['status'] ?? 'Eligible'));

                if (! in_array($status, ['Eligible', 'Terdaftar', 'Dijadwalkan'], true)) {
                    continue;
                }

                $rows[] = [
                    'nim' => 'STD-' . str_pad((string) ($participant['student_id'] ?? 0), 4, '0', STR_PAD_LEFT),
                    'student_name' => (string) ($participant['student_name'] ?? 'Mahasiswa'),
                    'course_name' => (string) ($classMap[$classId]['course_name'] ?? '-'),
                    'class_name' => (string) ($classMap[$classId]['class_name'] ?? '-'),
                    'score' => $participant['final_score'] !== null ? number_format((float) $participant['final_score'], 1) : '-',
                    'grade' => (string) ($participant['grade_letter'] ?? '-'),
                    'reason' => (string) ($participant['reason'] ?? 'Memenuhi kriteria remedial'),
                    'status' => $status,
                    'badge_class' => self::REMEDIAL_BADGES[$status] ?? 'warning',
                    'detail_url' => site_url('dosen/dashboard#remedial'),
                    'manage_url' => site_url('dosen/dashboard#remedial'),
                ];
            }

            if ($rows === []) {
                return $this->buildFallbackRemedialRows($classRows);
            }

            return array_slice($rows, 0, 6);
        } catch (\Throwable) {
            return $this->buildFallbackRemedialRows($classRows);
        }
    }

    private function buildFallbackClassRows(string $displayName, array $academicContext, string $mode): array
    {
        $lecturerA = $mode === 'global' ? 'Dr. Budi Santoso' : $displayName;
        $lecturerB = $mode === 'global' ? 'Dr. Rina Prameswari' : $displayName;

        return [
            [
                'id' => 101,
                'academic_year' => $academicContext['academic_year'],
                'semester' => $academicContext['semester_label'],
                'course_name' => 'Praktikum Pemrograman Dasar',
                'course_code' => 'PRAK-IF101',
                'class_name' => 'A',
                'lecturer_name' => $lecturerA,
                'student_count' => 28,
                'progress_nilai' => 92,
                'progress_kehadiran' => 88,
                'average_score' => 84.7,
                'status' => 'Validated',
                'status_badge' => $this->statusBadgeClass('Validated'),
                'low_students' => 2,
                'remedial_count' => 3,
                'detail_url' => site_url('dosen/dashboard#kelas-saya'),
                'input_url' => site_url('dosen/dashboard#kelas-saya'),
                'rekap_url' => site_url('dosen/dashboard#kelas-saya'),
                'validation_url' => site_url('dosen/dashboard#validasi-nilai'),
                'remedial_url' => site_url('dosen/dashboard#remedial'),
                'report_url' => site_url('dosen/dashboard#laporan'),
            ],
            [
                'id' => 102,
                'academic_year' => $academicContext['academic_year'],
                'semester' => $academicContext['semester_label'],
                'course_name' => 'Praktikum Struktur Data',
                'course_code' => 'PRAK-IF205',
                'class_name' => 'B',
                'lecturer_name' => $lecturerB,
                'student_count' => 24,
                'progress_nilai' => 78,
                'progress_kehadiran' => 81,
                'average_score' => 76.9,
                'status' => 'Reviewed',
                'status_badge' => $this->statusBadgeClass('Reviewed'),
                'low_students' => 5,
                'remedial_count' => 4,
                'detail_url' => site_url('dosen/dashboard#kelas-saya'),
                'input_url' => site_url('dosen/dashboard#kelas-saya'),
                'rekap_url' => site_url('dosen/dashboard#kelas-saya'),
                'validation_url' => site_url('dosen/dashboard#validasi-nilai'),
                'remedial_url' => site_url('dosen/dashboard#remedial'),
                'report_url' => site_url('dosen/dashboard#laporan'),
            ],
            [
                'id' => 103,
                'academic_year' => $academicContext['academic_year'],
                'semester' => $academicContext['semester_label'],
                'course_name' => 'Praktikum Basis Data',
                'course_code' => 'PRAK-SI310',
                'class_name' => 'C',
                'lecturer_name' => $lecturerA,
                'student_count' => 30,
                'progress_nilai' => 64,
                'progress_kehadiran' => 73,
                'average_score' => 68.4,
                'status' => 'Submitted',
                'status_badge' => $this->statusBadgeClass('Submitted'),
                'low_students' => 7,
                'remedial_count' => 6,
                'detail_url' => site_url('dosen/dashboard#kelas-saya'),
                'input_url' => site_url('dosen/dashboard#kelas-saya'),
                'rekap_url' => site_url('dosen/dashboard#kelas-saya'),
                'validation_url' => site_url('dosen/dashboard#validasi-nilai'),
                'remedial_url' => site_url('dosen/dashboard#remedial'),
                'report_url' => site_url('dosen/dashboard#laporan'),
            ],
        ];
    }

    private function buildFallbackRiskRows(array $classRows): array
    {
        $courseA = $classRows[0] ?? null;
        $courseB = $classRows[1] ?? $courseA;

        return [
            [
                'nim' => '23001001',
                'student_name' => 'Sinta Rahma',
                'course_name' => (string) ($courseA['course_name'] ?? 'Praktikum Pemrograman Dasar'),
                'class_name' => (string) ($courseA['class_name'] ?? 'A'),
                'temporary_score' => '58.5',
                'attendance' => 62,
                'risk_status' => 'Nilai rendah',
                'badge_class' => self::RISK_BADGES['Nilai rendah'],
                'detail_url' => site_url('dosen/dashboard#kelas-saya'),
            ],
            [
                'nim' => '23001008',
                'student_name' => 'Fajar Maulana',
                'course_name' => (string) ($courseB['course_name'] ?? 'Praktikum Struktur Data'),
                'class_name' => (string) ($courseB['class_name'] ?? 'B'),
                'temporary_score' => '64.0',
                'attendance' => 58,
                'risk_status' => 'Kehadiran kurang',
                'badge_class' => self::RISK_BADGES['Kehadiran kurang'],
                'detail_url' => site_url('dosen/dashboard#kelas-saya'),
            ],
            [
                'nim' => '23001011',
                'student_name' => 'Nadira Putri',
                'course_name' => (string) ($courseA['course_name'] ?? 'Praktikum Basis Data'),
                'class_name' => (string) ($courseA['class_name'] ?? 'C'),
                'temporary_score' => '-',
                'attendance' => 76,
                'risk_status' => 'Komponen nilai belum lengkap',
                'badge_class' => self::RISK_BADGES['Komponen nilai belum lengkap'],
                'detail_url' => site_url('dosen/dashboard#kelas-saya'),
            ],
        ];
    }

    private function buildFallbackRemedialRows(array $classRows): array
    {
        $courseA = $classRows[0] ?? null;
        $courseB = $classRows[1] ?? $courseA;

        return [
            [
                'nim' => '23001001',
                'student_name' => 'Sinta Rahma',
                'course_name' => (string) ($courseA['course_name'] ?? 'Praktikum Pemrograman Dasar'),
                'class_name' => (string) ($courseA['class_name'] ?? 'A'),
                'score' => '58.5',
                'grade' => 'D',
                'reason' => 'Nilai akhir di bawah batas lulus',
                'status' => 'Eligible',
                'badge_class' => self::REMEDIAL_BADGES['Eligible'],
                'detail_url' => site_url('dosen/dashboard#remedial'),
                'manage_url' => site_url('dosen/dashboard#remedial'),
            ],
            [
                'nim' => '23001008',
                'student_name' => 'Fajar Maulana',
                'course_name' => (string) ($courseB['course_name'] ?? 'Praktikum Struktur Data'),
                'class_name' => (string) ($courseB['class_name'] ?? 'B'),
                'score' => '64.0',
                'grade' => 'C',
                'reason' => 'Kehadiran belum memenuhi minimum',
                'status' => 'Terdaftar',
                'badge_class' => self::REMEDIAL_BADGES['Terdaftar'],
                'detail_url' => site_url('dosen/dashboard#remedial'),
                'manage_url' => site_url('dosen/dashboard#remedial'),
            ],
        ];
    }

    private function buildSummary(array $classRows): array
    {
        $classTotal = count($classRows);
        $studentTotal = array_sum(array_map(static fn (array $row): int => (int) ($row['student_count'] ?? 0), $classRows));
        $validationTotal = count(array_filter($classRows, static fn (array $row): bool => in_array((string) ($row['status'] ?? 'Draft'), ['Submitted', 'Reviewed'], true)));
        $remedialTotal = array_sum(array_map(static fn (array $row): int => (int) ($row['remedial_count'] ?? 0), $classRows));

        $progressNilai = $classTotal > 0
            ? (int) round(array_sum(array_map(static fn (array $row): int => (int) ($row['progress_nilai'] ?? 0), $classRows)) / $classTotal)
            : 0;

        $progressKehadiran = $classTotal > 0
            ? (int) round(array_sum(array_map(static fn (array $row): int => (int) ($row['progress_kehadiran'] ?? 0), $classRows)) / $classTotal)
            : 0;

        $statusCounts = [];
        foreach (self::STATUS_ORDER as $status) {
            $statusCounts[$status] = 0;
        }

        foreach ($classRows as $row) {
            $status = $this->normalizeStatus((string) ($row['status'] ?? 'Draft'));
            $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
        }

        return [
            'cards' => [
                [
                    'title' => 'Total Kelas Diampu',
                    'value' => number_format($classTotal),
                    'icon' => 'bi-journal-bookmark-fill',
                    'color' => 'primary',
                    'description' => 'Kelas aktif yang Anda ampu',
                ],
                [
                    'title' => 'Total Mahasiswa',
                    'value' => number_format($studentTotal),
                    'icon' => 'bi-people-fill',
                    'color' => 'success',
                    'description' => 'Mahasiswa lintas kelas aktif',
                ],
                [
                    'title' => 'Nilai Perlu Validasi',
                    'value' => number_format($validationTotal),
                    'icon' => 'bi-shield-check',
                    'color' => 'warning',
                    'description' => 'Submission yang belum final',
                ],
                [
                    'title' => 'Mahasiswa Remedial',
                    'value' => number_format($remedialTotal),
                    'icon' => 'bi-arrow-repeat',
                    'color' => 'danger',
                    'description' => 'Eligible atau sedang remedial',
                ],
                [
                    'title' => 'Progress Input Nilai',
                    'value' => $progressNilai . '%',
                    'icon' => 'bi-graph-up-arrow',
                    'color' => 'info',
                    'description' => 'Rata-rata kelengkapan input nilai',
                ],
                [
                    'title' => 'Progress Kehadiran',
                    'value' => $progressKehadiran . '%',
                    'icon' => 'bi-calendar-check',
                    'color' => 'secondary',
                    'description' => 'Rata-rata kelengkapan absensi',
                ],
            ],
            'class_total' => $classTotal,
            'student_total' => $studentTotal,
            'validation_total' => $validationTotal,
            'remedial_total' => $remedialTotal,
            'progress_nilai' => $progressNilai,
            'progress_kehadiran' => $progressKehadiran,
            'status_legend' => $statusCounts,
        ];
    }

    private function buildQuickActions(): array
    {
        $base = site_url('dosen/dashboard');

        return [
            ['label' => 'Kelas Saya', 'icon' => 'bi-journal-text', 'color' => 'primary', 'url' => $base . '#kelas-saya'],
            ['label' => 'Input Nilai', 'icon' => 'bi-pencil-square', 'color' => 'warning', 'url' => $base . '#kelas-saya'],
            ['label' => 'Rekapitulasi', 'icon' => 'bi-table', 'color' => 'info', 'url' => $base . '#kelas-saya'],
            ['label' => 'Validasi Nilai', 'icon' => 'bi-shield-check', 'color' => 'success', 'url' => $base . '#validasi-nilai'],
            ['label' => 'Remedial', 'icon' => 'bi-arrow-repeat', 'color' => 'danger', 'url' => $base . '#remedial'],
            ['label' => 'Laporan', 'icon' => 'bi-file-earmark-text', 'color' => 'secondary', 'url' => $base . '#laporan'],
        ];
    }

    private function buildChartData(array $classRows): array
    {
        $labels = [];
        $values = [];

        foreach ($classRows as $row) {
            $labels[] = (string) ($row['course_code'] ?? 'Kelas');
            $values[] = (int) ($row['progress_nilai'] ?? 0);
        }

        if ($labels === []) {
            $labels = ['Belum ada data'];
            $values = [0];
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function normalizeRoles(mixed $roles): array
    {
        if (! is_array($roles)) {
            $roles = $roles === null ? [] : explode(',', (string) $roles);
        }

        return array_values(array_filter(array_map(static fn ($role): string => strtolower(trim((string) $role)), $roles)));
    }

    private function normalizeStatus(string $status): string
    {
        $status = trim($status);

        return match (strtolower($status)) {
            'submitted' => 'Submitted',
            'reviewed' => 'Reviewed',
            'validated' => 'Validated',
            'locked' => 'Locked',
            'revision requested', 'revision_requested' => 'Revision Requested',
            default => 'Draft',
        };
    }

    private function statusBadgeClass(string $status): string
    {
        return self::STATUS_BADGES[$this->normalizeStatus($status)] ?? 'secondary';
    }

    private function pickHigherStatus(string $current, string $incoming): string
    {
        $currentIndex = array_search($this->normalizeStatus($current), self::STATUS_ORDER, true);
        $incomingIndex = array_search($this->normalizeStatus($incoming), self::STATUS_ORDER, true);

        $currentIndex = $currentIndex === false ? 0 : (int) $currentIndex;
        $incomingIndex = $incomingIndex === false ? 0 : (int) $incomingIndex;

        return self::STATUS_ORDER[max($currentIndex, $incomingIndex)] ?? 'Draft';
    }

    private function normalizeRemedialStatus(string $status): string
    {
        $status = trim($status);

        return match (strtolower($status)) {
            'eligible' => 'Eligible',
            'registered', 'terdaftar' => 'Terdaftar',
            'scheduled', 'dijadwalkan' => 'Dijadwalkan',
            'done', 'selesai' => 'Selesai',
            'rejected', 'ditolak' => 'Ditolak',
            default => 'Eligible',
        };
    }

    private function percentage(int $filled, int $expected): int
    {
        if ($expected <= 0) {
            return 0;
        }

        return (int) round(($filled / $expected) * 100);
    }

    private function formatDateIndo(string $date): string
    {
        if ($date === '') {
            return '-';
        }

        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return $date;
        }

        $day = date('d', $timestamp);
        $month = $months[date('m', $timestamp)] ?? date('m', $timestamp);
        $year = date('Y', $timestamp);

        return $day . ' ' . $month . ' ' . $year;
    }

    private function formatDateTime(string $dateTime): string
    {
        if ($dateTime === '') {
            return '-';
        }

        $timestamp = strtotime($dateTime);

        return $timestamp === false ? $dateTime : date('d/m/Y H:i', $timestamp);
    }
}