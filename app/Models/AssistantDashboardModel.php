<?php

namespace App\Models;

use CodeIgniter\Model;

class AssistantDashboardModel extends Model
{
    protected $DBGroup = 'default';

    public function buildDashboardData(int $userId, string $assistantName, array $filters = [], array $permissions = []): array
    {
        $academic = $this->resolveAcademicContext();
        $filters = $this->normalizeFilters($filters, $academic);
        $assistantIds = $this->resolveAssistantIds($userId);

        $classRows = $this->loadClassRows($assistantIds, $filters);
        $groupRows = $this->loadGroupRows($assistantIds, $filters);
        $studentRows = $this->loadStudentRows($classRows, $groupRows);
        $pendingAttendanceRows = $this->loadPendingAttendanceRows($classRows);
        $incompleteScoreRows = $this->loadIncompleteScoreRows($classRows);
        $missingScoreRows = $this->loadMissingScoreRows($classRows);
        $remedialRows = $this->loadRemedialRows($classRows);
        $activityRows = $this->loadActivityRows($userId, $classRows);

        $summary = $this->buildSummary($classRows, $groupRows, $studentRows, $pendingAttendanceRows, $incompleteScoreRows, $remedialRows);

        return [
            'title' => 'Dashboard Asisten Praktikum',
            'assistantName' => $assistantName,
            'academicYear' => $filters['academic_year'],
            'semesterLabel' => $filters['semester_label'],
            'filters' => $filters,
            'filterOptions' => $this->buildFilterOptions($classRows, $groupRows),
            'summaryCards' => $summary['cards'],
            'classRows' => $classRows,
            'pendingAttendanceRows' => $pendingAttendanceRows,
            'incompleteScoreRows' => $incompleteScoreRows,
            'missingScoreRows' => $missingScoreRows,
            'remedialRows' => $remedialRows,
            'activityRows' => $activityRows,
            'quickActions' => $this->buildQuickActions($permissions),
            'canExport' => in_array('export.rekap.terbatas', $this->normalizeValues($permissions), true),
            'hasData' => $classRows !== [] || $groupRows !== [],
            'summary' => $summary['meta'],
        ];
    }

    private function resolveAcademicContext(): array
    {
        $month = (int) date('n');

        return [
            'academic_year' => $month >= 7 ? date('Y') . '/' . (date('Y') + 1) : (date('Y') - 1) . '/' . date('Y'),
            'semester' => $month >= 7 ? 'ganjil' : 'genap',
            'semester_label' => $month >= 7 ? 'Semester Ganjil' : 'Semester Genap',
        ];
    }

    private function normalizeFilters(array $filters, array $academic): array
    {
        return [
            'academic_year' => trim((string) ($filters['academic_year'] ?? '')) ?: $academic['academic_year'],
            'semester' => trim((string) ($filters['semester'] ?? '')) ?: $academic['semester'],
            'semester_label' => trim((string) ($filters['semester'] ?? '')) ?: $academic['semester_label'],
            'course_id' => trim((string) ($filters['course_id'] ?? '')),
            'class_id' => trim((string) ($filters['class_id'] ?? '')),
            'group_id' => trim((string) ($filters['group_id'] ?? '')),
            'status_class' => trim((string) ($filters['status_class'] ?? '')),
            'status_score' => trim((string) ($filters['status_score'] ?? '')),
            'status_attendance' => trim((string) ($filters['status_attendance'] ?? '')),
        ];
    }

    private function resolveAssistantIds(int $userId): array
    {
        $ids = [$userId];
        $db = db_connect();

        if ($userId > 0 && $db->tableExists('assistants')) {
            $assistant = $db->table('assistants')
                ->select('id')
                ->where('user_id', $userId)
                ->where('is_active', 1)
                ->get()
                ->getRowArray();

            if (isset($assistant['id'])) {
                $ids[] = (int) $assistant['id'];
            }
        }

        return array_values(array_unique(array_filter(array_map('intval', $ids))));
    }

    private function loadClassRows(array $assistantIds, array $filters): array
    {
        $db = db_connect();

        if (! $db->tableExists('practicum_classes') || ! $db->tableExists('courses')) {
            return [];
        }

        $builder = $db->table('practicum_classes pc');
        $builder->select('pc.id, pc.course_id, pc.class_code, pc.class_name, pc.status, pc.deadline_at, pc.assistant_id, c.course_code, c.course_name');
        $builder->join('courses c', 'c.id = pc.course_id', 'left');
        $builder->whereIn('pc.assistant_id', $assistantIds);

        if ($filters['course_id'] !== '') {
            $builder->where('pc.course_id', (int) $filters['course_id']);
        }

        if ($filters['class_id'] !== '') {
            $builder->where('pc.id', (int) $filters['class_id']);
        }

        if ($filters['status_class'] !== '') {
            $builder->where('pc.status', $filters['status_class']);
        }

        $rows = $builder->groupBy('pc.id')->orderBy('pc.class_name', 'ASC')->get()->getResultArray();

        if ($rows === []) {
            return [];
        }

        $classIds = array_values(array_map(static fn (array $row): int => (int) ($row['id'] ?? 0), $rows));
        $studentCounts = $this->countStudentsByClass($classIds);
        $sessionCounts = $this->countSessionsByClass($classIds);
        $attendanceCounts = $this->countAttendanceByClass($classIds);
        $scoreCounts = $this->countScoreByClass($classIds);
        $finalScores = $this->loadFinalScores($classIds);
        $remedialCounts = $this->countRemedialByClass($classIds);

        $prepared = [];

        foreach ($rows as $row) {
            $classId = (int) ($row['id'] ?? 0);
            $studentCount = (int) ($studentCounts[$classId] ?? 0);
            $sessionCount = (int) ($sessionCounts[$classId] ?? 0);
            $attendanceFilled = (int) ($attendanceCounts[$classId] ?? 0);
            $scoreFilled = (int) ($scoreCounts[$classId] ?? 0);
            $attendanceExpected = max(0, $studentCount * max(1, $sessionCount));
            $scoreExpected = max(0, $studentCount * max(1, $this->countRequiredComponents()));
            $status = $this->normalizeClassStatus((string) ($row['status'] ?? 'active'));
            $finalRow = $finalScores[$classId] ?? [];

            $prepared[] = [
                'id' => $classId,
                'course_id' => (int) ($row['course_id'] ?? 0),
                'course_name' => (string) ($row['course_name'] ?? '-'),
                'course_code' => (string) ($row['course_code'] ?? '-'),
                'class_name' => (string) ($row['class_name'] ?? '-'),
                'group_name' => '-',
                'student_count' => $studentCount,
                'session_count' => $sessionCount,
                'attendance_progress' => $this->percentage($attendanceFilled, $attendanceExpected),
                'score_progress' => $this->percentage($scoreFilled, $scoreExpected),
                'attendance_status' => $this->attendanceStatus($attendanceFilled, $attendanceExpected),
                'score_status' => $this->scoreStatus($scoreFilled, $scoreExpected),
                'status' => $status,
                'status_badge' => $this->statusBadge($status),
                'remedial_count' => (int) ($remedialCounts[$classId] ?? 0),
                'final_score' => isset($finalRow['final_score']) ? (float) $finalRow['final_score'] : null,
                'grade_letter' => (string) ($finalRow['grade_letter'] ?? '-'),
                'can_input_attendance' => in_array($status, ['Aktif', 'Selesai'], true),
                'can_input_score' => in_array($status, ['Aktif', 'Selesai'], true),
                'can_view_recap' => true,
                'detail_url' => site_url('assistant/dashboard#kelas'),
                'attendance_url' => site_url('assistant/dashboard#absensi'),
                'score_url' => site_url('assistant/dashboard#nilai'),
                'recap_url' => site_url('assistant/dashboard#rekap'),
            ];
        }

        return $prepared;
    }

    private function loadGroupRows(array $assistantIds, array $filters): array
    {
        $db = db_connect();

        if (! $db->tableExists('practicum_groups')) {
            return [];
        }

        $builder = $db->table('practicum_groups pg');
        $builder->select('pg.id, pg.class_id, pg.group_code, pg.group_name, pg.status, c.course_code, c.course_name, pc.class_name');
        $builder->join('practicum_classes pc', 'pc.id = pg.class_id', 'left');
        $builder->join('courses c', 'c.id = pc.course_id', 'left');
        $builder->whereIn('pg.assistant_id', $assistantIds);

        if ($filters['group_id'] !== '') {
            $builder->where('pg.id', (int) $filters['group_id']);
        }

        $rows = $builder->groupBy('pg.id')->orderBy('pg.group_name', 'ASC')->get()->getResultArray();

        if ($rows === []) {
            return [];
        }

        $groupIds = array_values(array_map(static fn (array $row): int => (int) ($row['id'] ?? 0), $rows));
        $studentCounts = $this->countStudentsByGroup($groupIds);
        $sessionCounts = $this->countSessionsByGroup($groupIds);
        $attendanceCounts = $this->countAttendanceByGroup($groupIds);
        $scoreCounts = $this->countScoreByGroup($groupIds);
        $finalScores = $this->loadGroupFinalScores($groupIds);

        $prepared = [];

        foreach ($rows as $row) {
            $groupId = (int) ($row['id'] ?? 0);
            $studentCount = (int) ($studentCounts[$groupId] ?? 0);
            $sessionCount = (int) ($sessionCounts[$groupId] ?? 0);
            $attendanceFilled = (int) ($attendanceCounts[$groupId] ?? 0);
            $scoreFilled = (int) ($scoreCounts[$groupId] ?? 0);
            $attendanceExpected = max(0, $studentCount * max(1, $sessionCount));
            $scoreExpected = max(0, $studentCount * max(1, $this->countRequiredComponents()));
            $status = $this->normalizeClassStatus((string) ($row['status'] ?? 'active'));
            $finalRow = $finalScores[$groupId] ?? [];

            $prepared[] = [
                'id' => $groupId,
                'course_id' => (int) ($row['class_id'] ?? 0),
                'course_name' => (string) ($row['course_name'] ?? '-'),
                'course_code' => (string) ($row['course_code'] ?? '-'),
                'class_name' => (string) ($row['class_name'] ?? '-'),
                'group_name' => (string) ($row['group_name'] ?? '-'),
                'student_count' => $studentCount,
                'session_count' => $sessionCount,
                'attendance_progress' => $this->percentage($attendanceFilled, $attendanceExpected),
                'score_progress' => $this->percentage($scoreFilled, $scoreExpected),
                'attendance_status' => $this->attendanceStatus($attendanceFilled, $attendanceExpected),
                'score_status' => $this->scoreStatus($scoreFilled, $scoreExpected),
                'status' => $status,
                'status_badge' => $this->statusBadge($status),
                'remedial_count' => 0,
                'final_score' => isset($finalRow['final_score']) ? (float) $finalRow['final_score'] : null,
                'grade_letter' => (string) ($finalRow['grade_letter'] ?? '-'),
                'can_input_attendance' => true,
                'can_input_score' => true,
                'can_view_recap' => true,
                'detail_url' => site_url('assistant/dashboard#kelompok'),
                'attendance_url' => site_url('assistant/dashboard#absensi'),
                'score_url' => site_url('assistant/dashboard#nilai'),
                'recap_url' => site_url('assistant/dashboard#rekap'),
            ];
        }

        return $prepared;
    }

    private function loadStudentRows(array $classRows, array $groupRows): array
    {
        $db = db_connect();

        if (! $db->tableExists('class_students')) {
            return [];
        }

        $classIds = array_values(array_map(static fn (array $row): int => (int) ($row['id'] ?? 0), $classRows));
        $groupIds = array_values(array_map(static fn (array $row): int => (int) ($row['id'] ?? 0), $groupRows));

        $builder = $db->table('class_students cs');
        $builder->select('cs.class_id, cs.student_id, u.student_number, u.full_name');
        $builder->join('users u', 'u.id = cs.student_id', 'left');

        if ($classIds !== []) {
            $builder->whereIn('cs.class_id', $classIds);
        }

        if ($groupIds !== [] && $db->fieldExists('group_id', 'class_students')) {
            $builder->orWhereIn('cs.group_id', $groupIds);
        }

        return $builder->get()->getResultArray();
    }

    private function loadPendingAttendanceRows(array $classRows): array
    {
        $db = db_connect();

        if (! $db->tableExists('attendance_sessions') || ! $db->tableExists('attendance_records')) {
            return [];
        }

        $classMap = $this->indexById($classRows);
        $classIds = array_keys($classMap);

        if ($classIds === []) {
            return [];
        }

        $sessions = $db->table('attendance_sessions')
            ->select('id, class_id, meeting_no, session_date, status')
            ->whereIn('class_id', $classIds)
            ->get()
            ->getResultArray();

        $studentCounts = $this->countStudentsByClass($classIds);
        $rows = [];

        foreach ($sessions as $session) {
            $sessionId = (int) ($session['id'] ?? 0);
            $classId = (int) ($session['class_id'] ?? 0);
            $filled = $this->countAttendanceRecordsBySession($sessionId);
            $expected = (int) ($studentCounts[$classId] ?? 0);
            $status = $this->attendanceRowStatus($filled, $expected);

            if ($status === 'Lengkap') {
                continue;
            }

            $classRow = $classMap[$classId] ?? [];

            $rows[] = [
                'course_name' => (string) ($classRow['course_name'] ?? '-'),
                'class_label' => trim((string) ($classRow['class_name'] ?? '-') . ' / ' . (string) ($classRow['group_name'] ?? '-')),
                'meeting_no' => (string) ($session['meeting_no'] ?? '-'),
                'session_date' => $this->formatDateIndo((string) ($session['session_date'] ?? '')),
                'student_count' => $expected,
                'attendance_filled' => $filled,
                'status' => $status,
                'status_badge' => $this->badgeForAttendanceStatus($status),
                'action_url' => site_url('assistant/dashboard#absensi'),
            ];
        }

        return $rows;
    }

    private function loadIncompleteScoreRows(array $classRows): array
    {
        $db = db_connect();

        if (! $db->tableExists('score_entries')) {
            return [];
        }

        $classMap = $this->indexById($classRows);
        $rows = [];

        foreach ($classMap as $classId => $classRow) {
            $studentCount = (int) ($this->countStudentsByClass([$classId])[$classId] ?? 0);
            $expected = max(0, $studentCount * max(1, $this->countRequiredComponents()));
            $filled = $this->countScoreEntries($classId);
            $status = $this->scoreStatus($filled, $expected);

            if ($status === 'Lengkap') {
                continue;
            }

            $rows[] = [
                'course_name' => (string) ($classRow['course_name'] ?? '-'),
                'class_label' => trim((string) ($classRow['class_name'] ?? '-') . ' / ' . (string) ($classRow['group_name'] ?? '-')),
                'component_name' => 'Komponen Penilaian',
                'subcomponent_name' => 'Sub-komponen wajib',
                'students_pending' => max(0, $expected - $filled),
                'deadline' => ! empty($classRow['deadline_at']) ? $this->formatDateIndo((string) $classRow['deadline_at']) : '-',
                'status' => $status,
                'status_badge' => $this->badgeForScoreStatus($status),
                'action_url' => site_url('assistant/dashboard#nilai'),
            ];
        }

        return $rows;
    }

    private function loadMissingScoreRows(array $classRows): array
    {
        $db = db_connect();

        if (! $db->tableExists('score_entries')) {
            return [];
        }

        $classMap = $this->indexById($classRows);
        $classIds = array_keys($classMap);

        if ($classIds === []) {
            return [];
        }

        $rows = $db->table('score_entries se')
            ->select('se.class_id, se.student_id, se.component_id, se.score_value, u.student_number, u.full_name')
            ->join('users u', 'u.id = se.student_id', 'left')
            ->whereIn('se.class_id', $classIds)
            ->groupStart()
            ->where('se.score_value IS NULL', null, false)
            ->orWhere('se.score_value', 0)
            ->orWhere('se.score_value <', 0)
            ->orWhere('se.score_value >', 100)
            ->groupEnd()
            ->limit(10)
            ->get()
            ->getResultArray();

        $prepared = [];

        foreach ($rows as $row) {
            $classRow = $classMap[(int) ($row['class_id'] ?? 0)] ?? [];
            $status = $this->missingScoreStatus($row['score_value'] ?? null);

            $prepared[] = [
                'student_number' => (string) ($row['student_number'] ?? '-'),
                'student_name' => (string) ($row['full_name'] ?? '-'),
                'course_name' => (string) ($classRow['course_name'] ?? '-'),
                'class_label' => trim((string) ($classRow['class_name'] ?? '-') . ' / ' . (string) ($classRow['group_name'] ?? '-')),
                'component_name' => 'Komponen Penilaian',
                'subcomponent_name' => 'Sub-komponen wajib',
                'status' => $status,
                'status_badge' => $this->missingScoreBadge($status),
                'action_url' => site_url('assistant/dashboard#nilai'),
            ];
        }

        return $prepared;
    }

    private function loadRemedialRows(array $classRows): array
    {
        $db = db_connect();

        if (! $db->tableExists('remedial_participants')) {
            return [];
        }

        $classMap = $this->indexById($classRows);
        $classIds = array_keys($classMap);

        if ($classIds === []) {
            return [];
        }

        $rows = $db->table('remedial_participants rp')
            ->select('rp.class_id, rp.student_id, rp.status, fs.final_score, fs.grade_letter, u.student_number, u.full_name')
            ->join('users u', 'u.id = rp.student_id', 'left')
            ->join('final_scores fs', 'fs.class_id = rp.class_id AND fs.student_id = rp.student_id', 'left')
            ->whereIn('rp.class_id', $classIds)
            ->orderBy('rp.created_at', 'DESC')
            ->limit(12)
            ->get()
            ->getResultArray();

        $prepared = [];

        foreach ($rows as $row) {
            $classRow = $classMap[(int) ($row['class_id'] ?? 0)] ?? [];
            $status = $this->normalizeRemedialStatus((string) ($row['status'] ?? 'eligible'));

            $prepared[] = [
                'student_number' => (string) ($row['student_number'] ?? '-'),
                'student_name' => (string) ($row['full_name'] ?? '-'),
                'course_name' => (string) ($classRow['course_name'] ?? '-'),
                'class_name' => (string) ($classRow['class_name'] ?? '-'),
                'final_score' => isset($row['final_score']) ? (float) $row['final_score'] : null,
                'grade_letter' => (string) ($row['grade_letter'] ?? '-'),
                'status' => $status,
                'status_badge' => $this->remedialBadge($status),
                'action_url' => site_url('assistant/dashboard#remedial'),
            ];
        }

        return $prepared;
    }

    private function loadActivityRows(int $userId, array $classRows): array
    {
        $db = db_connect();

        if (! $db->tableExists('activity_logs')) {
            return [];
        }

        $rows = $db->table('activity_logs')
            ->select('activity, description, created_at')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(8)
            ->get()
            ->getResultArray();

        $context = isset($classRows[0]['course_name']) ? (string) $classRows[0]['course_name'] : '-';

        return array_map(static function (array $row) use ($context): array {
            return [
                'time' => (string) ($row['created_at'] ?? ''),
                'activity' => (string) ($row['description'] ?? $row['activity'] ?? '-'),
                'context' => $context,
                'status' => 'Terkini',
            ];
        }, $rows);
    }

    private function buildSummary(array $classRows, array $groupRows, array $studentRows, array $pendingAttendanceRows, array $incompleteScoreRows, array $remedialRows): array
    {
        $studentIds = array_values(array_unique(array_filter(array_map(static fn (array $row): int => (int) ($row['student_id'] ?? 0), $studentRows))));

        return [
            'cards' => [
                ['title' => 'Kelas Ditangani', 'value' => count($classRows), 'color' => 'primary', 'icon' => 'bi-journal-bookmark', 'link' => '#kelas'],
                ['title' => 'Kelompok Praktikum', 'value' => count($groupRows), 'color' => 'info', 'icon' => 'bi-collection', 'link' => '#kelompok'],
                ['title' => 'Mahasiswa', 'value' => count($studentIds), 'color' => 'success', 'icon' => 'bi-people', 'link' => '#mahasiswa'],
                ['title' => 'Absensi Belum Lengkap', 'value' => count($pendingAttendanceRows), 'color' => 'warning', 'icon' => 'bi-calendar-x', 'link' => '#absensi'],
                ['title' => 'Nilai Belum Lengkap', 'value' => count($incompleteScoreRows), 'color' => 'warning', 'icon' => 'bi-clipboard-data', 'link' => '#nilai'],
                ['title' => 'Potensi Remedial', 'value' => count($remedialRows), 'color' => 'danger', 'icon' => 'bi-exclamation-triangle', 'link' => '#remedial'],
            ],
            'meta' => [
                'kelas' => count($classRows),
                'kelompok' => count($groupRows),
                'mahasiswa' => count($studentIds),
                'absensi' => count($pendingAttendanceRows),
                'nilai' => count($incompleteScoreRows),
                'remedial' => count($remedialRows),
            ],
        ];
    }

    private function buildFilterOptions(array $classRows, array $groupRows): array
    {
        $courses = [];
        $classes = [];
        $groups = [];
        $statuses = [];

        foreach (array_merge($classRows, $groupRows) as $row) {
            if (! empty($row['course_id'])) {
                $courses[(int) $row['course_id']] = [
                    'id' => (int) $row['course_id'],
                    'label' => (string) ($row['course_code'] ?? '-') . ' - ' . (string) ($row['course_name'] ?? '-'),
                ];
            }

            if (! empty($row['id'])) {
                $classes[(int) $row['id']] = [
                    'id' => (int) $row['id'],
                    'label' => trim((string) ($row['course_code'] ?? '-') . ' / ' . (string) ($row['class_name'] ?? '-') . ' / ' . (string) ($row['group_name'] ?? '-')),
                ];
            }

            if (! empty($row['group_name'])) {
                $groups[(int) $row['id']] = [
                    'id' => (int) $row['id'],
                    'label' => (string) $row['group_name'],
                ];
            }

            if (! empty($row['status'])) {
                $statuses[] = (string) $row['status'];
            }
        }

        $academic = $this->resolveAcademicContext();

        return [
            'academic_years' => [$academic['academic_year']],
            'semesters' => ['ganjil', 'genap'],
            'courses' => array_values($courses),
            'classes' => array_values($classes),
            'groups' => array_values($groups),
            'class_statuses' => array_values(array_unique($statuses)),
            'attendance_statuses' => ['Belum Diabsen', 'Sebagian', 'Lengkap'],
            'score_statuses' => ['Belum Mulai', 'Dalam Proses', 'Hampir Lengkap', 'Lengkap', 'Tertutup'],
        ];
    }

    private function buildQuickActions(array $permissions): array
    {
        $actions = [
            ['label' => 'Input Kehadiran', 'icon' => 'bi-calendar-check', 'color' => 'primary', 'url' => '#absensi'],
            ['label' => 'Input Nilai', 'icon' => 'bi-pencil-square', 'color' => 'warning', 'url' => '#nilai'],
            ['label' => 'Lihat Rekap Nilai', 'icon' => 'bi-clipboard-data', 'color' => 'info', 'url' => '#rekap'],
            ['label' => 'Lihat Catatan Praktikum', 'icon' => 'bi-journal-text', 'color' => 'secondary', 'url' => '#catatan'],
            ['label' => 'Lihat Jadwal Remedial', 'icon' => 'bi-calendar-event', 'color' => 'danger', 'url' => '#remedial'],
        ];

        if (in_array('export.rekap.terbatas', $this->normalizeValues($permissions), true)) {
            $actions[] = ['label' => 'Export Rekap Terbatas', 'icon' => 'bi-download', 'color' => 'success', 'url' => '#export'];
        }

        return $actions;
    }

    private function countStudentsByClass(array $classIds): array
    {
        $db = db_connect();

        if ($classIds === [] || ! $db->tableExists('class_students')) {
            return [];
        }

        $rows = $db->table('class_students')
            ->select('class_id, COUNT(DISTINCT student_id) AS total')
            ->whereIn('class_id', $classIds)
            ->groupBy('class_id')
            ->get()
            ->getResultArray();

        return array_column($rows, 'total', 'class_id');
    }

    private function countSessionsByClass(array $classIds): array
    {
        $db = db_connect();

        if ($classIds === [] || ! $db->tableExists('attendance_sessions')) {
            return [];
        }

        $rows = $db->table('attendance_sessions')
            ->select('class_id, COUNT(*) AS total')
            ->whereIn('class_id', $classIds)
            ->groupBy('class_id')
            ->get()
            ->getResultArray();

        return array_column($rows, 'total', 'class_id');
    }

    private function countAttendanceByClass(array $classIds): array
    {
        $db = db_connect();

        if ($classIds === [] || ! $db->tableExists('attendance_records') || ! $db->tableExists('attendance_sessions')) {
            return [];
        }

        $rows = $db->table('attendance_records ar')
            ->select('s.class_id, COUNT(*) AS total')
            ->join('attendance_sessions s', 's.id = ar.session_id', 'left')
            ->whereIn('s.class_id', $classIds)
            ->where('ar.status IS NOT NULL', null, false)
            ->groupBy('s.class_id')
            ->get()
            ->getResultArray();

        return array_column($rows, 'total', 'class_id');
    }

    private function countScoreByClass(array $classIds): array
    {
        $db = db_connect();

        if ($classIds === [] || ! $db->tableExists('score_entries')) {
            return [];
        }

        $rows = $db->table('score_entries')
            ->select('class_id, COUNT(*) AS total')
            ->whereIn('class_id', $classIds)
            ->where('score_value IS NOT NULL', null, false)
            ->groupBy('class_id')
            ->get()
            ->getResultArray();

        return array_column($rows, 'total', 'class_id');
    }

    private function countRemedialByClass(array $classIds): array
    {
        $db = db_connect();

        if ($classIds === [] || ! $db->tableExists('remedial_participants')) {
            return [];
        }

        $rows = $db->table('remedial_participants')
            ->select('class_id, COUNT(*) AS total')
            ->whereIn('class_id', $classIds)
            ->groupBy('class_id')
            ->get()
            ->getResultArray();

        return array_column($rows, 'total', 'class_id');
    }

    private function countRequiredComponents(): int
    {
        $db = db_connect();

        if ($db->tableExists('assessment_subcomponents')) {
            return max(1, (int) $db->table('assessment_subcomponents')->countAllResults());
        }

        if ($db->tableExists('assessment_components')) {
            return max(1, (int) $db->table('assessment_components')->countAllResults());
        }

        return 1;
    }

    private function countAttendanceRecordsBySession(int $sessionId): int
    {
        $db = db_connect();

        if (! $db->tableExists('attendance_records')) {
            return 0;
        }

        return (int) $db->table('attendance_records')
            ->where('session_id', $sessionId)
            ->countAllResults();
    }

    private function countScoreEntries(int $classId): int
    {
        $db = db_connect();

        if (! $db->tableExists('score_entries')) {
            return 0;
        }

        return (int) $db->table('score_entries')
            ->where('class_id', $classId)
            ->where('score_value IS NOT NULL', null, false)
            ->countAllResults();
    }

    private function loadFinalScores(array $classIds): array
    {
        $db = db_connect();

        if ($classIds === [] || ! $db->tableExists('final_scores')) {
            return [];
        }

        $rows = $db->table('final_scores')
            ->whereIn('class_id', $classIds)
            ->get()
            ->getResultArray();

        $grouped = [];

        foreach ($rows as $row) {
            $grouped[(int) ($row['class_id'] ?? 0)] = $row;
        }

        return $grouped;
    }

    private function loadGroupFinalScores(array $groupIds): array
    {
        return [];
    }

    private function countStudentsByGroup(array $groupIds): array
    {
        return [];
    }

    private function countSessionsByGroup(array $groupIds): array
    {
        return [];
    }

    private function countAttendanceByGroup(array $groupIds): array
    {
        return [];
    }

    private function countScoreByGroup(array $groupIds): array
    {
        return [];
    }

    private function indexById(array $rows): array
    {
        $indexed = [];

        foreach ($rows as $row) {
            if (isset($row['id'])) {
                $indexed[(int) $row['id']] = $row;
            }
        }

        return $indexed;
    }

    private function normalizeValues($values): array
    {
        if (! is_array($values)) {
            $values = [$values];
        }

        return array_values(array_unique(array_filter(array_map(static fn ($value): string => strtolower(trim((string) $value)), $values))));
    }

    private function normalizeClassStatus(string $status): string
    {
        return match (strtolower(trim($status))) {
            'active', 'aktif' => 'Aktif',
            'completed', 'selesai' => 'Selesai',
            'locked', 'terkunci' => 'Terkunci',
            'archived', 'diarsipkan' => 'Diarsipkan',
            default => 'Aktif',
        };
    }

    private function normalizeRemedialStatus(string $status): string
    {
        return match (strtolower(trim($status))) {
            'eligible' => 'Eligible',
            'registered', 'terdaftar' => 'Terdaftar',
            'scheduled', 'dijadwalkan' => 'Dijadwalkan',
            'scored', 'sudah dinilai' => 'Sudah Dinilai',
            'validated' => 'Validated',
            'not_attend', 'tidak mengikuti' => 'Tidak Mengikuti',
            'cancelled', 'dibatalkan' => 'Dibatalkan',
            default => 'Eligible',
        };
    }

    private function attendanceStatus(int $filled, int $expected): string
    {
        if ($expected <= 0 || $filled <= 0) {
            return 'Belum Diabsen';
        }

        if ($filled >= $expected) {
            return 'Lengkap';
        }

        return 'Sebagian';
    }

    private function scoreStatus(int $filled, int $expected): string
    {
        if ($expected <= 0 || $filled <= 0) {
            return 'Belum Mulai';
        }

        if ($filled >= $expected) {
            return 'Lengkap';
        }

        if ($filled >= ($expected * 0.75)) {
            return 'Hampir Lengkap';
        }

        return 'Dalam Proses';
    }

    private function percentage(int $filled, int $expected): int
    {
        if ($expected <= 0) {
            return 0;
        }

        return (int) round(($filled / $expected) * 100);
    }

    private function badgeForAttendanceStatus(string $status): string
    {
        return match ($status) {
            'Lengkap' => 'success',
            'Sebagian' => 'warning',
            default => 'secondary',
        };
    }

    private function badgeForScoreStatus(string $status): string
    {
        return match ($status) {
            'Lengkap' => 'success',
            'Hampir Lengkap' => 'info',
            'Dalam Proses' => 'warning',
            'Tertutup' => 'dark',
            default => 'secondary',
        };
    }

    private function remedialBadge(string $status): string
    {
        return match ($status) {
            'Terdaftar', 'Dijadwalkan' => 'primary',
            'Validated', 'Sudah Dinilai' => 'success',
            'Tidak Mengikuti', 'Dibatalkan' => 'danger',
            'Eligible' => 'warning',
            default => 'info',
        };
    }

    private function statusBadge(string $status): string
    {
        return match ($status) {
            'Aktif' => 'success',
            'Selesai' => 'primary',
            'Terkunci' => 'dark',
            'Diarsipkan' => 'secondary',
            default => 'secondary',
        };
    }

    private function missingScoreStatus(mixed $value): string
    {
        if ($value === null || $value === '') {
            return 'Belum Dinilai';
        }

        if (! is_numeric($value)) {
            return 'Tidak Valid';
        }

        $numeric = (float) $value;

        if ($numeric === 0.0) {
            return 'Nilai 0';
        }

        if ($numeric < 0.0 || $numeric > 100.0) {
            return 'Tidak Valid';
        }

        return 'Belum Dinilai';
    }

    private function missingScoreBadge(string $status): string
    {
        return match ($status) {
            'Nilai 0' => 'info',
            'Tidak Valid' => 'danger',
            default => 'warning',
        };
    }

    private function formatDateIndo(string $date): string
    {
        if ($date === '') {
            return '-';
        }

        $timestamp = strtotime($date);

        if ($timestamp === false) {
            return $date;
        }

        $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

        return date('d', $timestamp) . ' ' . ($months[(int) date('n', $timestamp)] ?? '-') . ' ' . date('Y', $timestamp);
    }
}
