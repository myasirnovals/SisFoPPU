<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentDashboardModel extends Model
{
    protected $DBGroup = 'default';

    // ─── Konstanta Badge ─────────────────────────────────────────────────
    private const SCORE_STATUS_BADGES = [
        'draft'     => 'secondary',
        'submitted' => 'info',
        'reviewed'  => 'primary',
        'validated' => 'success',
        'locked'    => 'success',
        'revision_requested' => 'warning',
        'revised'   => 'info',
    ];

    private const ACADEMIC_STATUS_BADGES = [
        'Lulus'         => 'success',
        'Remedial'      => 'warning',
        'Tidak Lulus'   => 'danger',
        'Belum Lengkap' => 'secondary',
    ];

    private const CLASS_STATUS_BADGES = [
        'draft'      => 'secondary',
        'aktif'      => 'success',
        'selesai'    => 'primary',
        'terkunci'   => 'dark',
        'diarsipkan' => 'secondary',
    ];

    private const ATTENDANCE_STATUS_BADGES = [
        'Aman'              => 'success',
        'Perlu Perhatian'   => 'warning',
        'Kurang Kehadiran'  => 'danger',
        'Belum Ada Pertemuan' => 'secondary',
    ];

    private const REMEDIAL_STATUS_BADGES = [
        'eligible'        => 'warning',
        'terdaftar'       => 'info',
        'dijadwalkan'     => 'primary',
        'sudah_dinilai'   => 'success',
        'validated'       => 'success',
        'tidak_mengikuti' => 'secondary',
        'dibatalkan'      => 'secondary',
    ];

    // ─── Cache ─────────────────────────────────────────────────────────────
    private array $dashboardDataCache = [];

    // ═══════════════════════════════════════════════════════════════════════
    //  PUBLIC API
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Build complete dashboard data for a student
     */
    public function buildDashboardData(string $userNim, string $displayName = ''): array
    {
        $academic = $this->resolveAcademicContext();
        $student  = $this->resolveStudentProfile($userNim, $displayName);

        $classRows      = $this->loadClassRows($student);
        $attendanceRows = $this->loadAttendanceSummaryRows($classRows, $student);
        $scoreRows      = $this->loadScoreSummaryRows($classRows, $student);
        $remedialRows   = $this->loadRemedialRows($classRows, $student);
        $notifications  = $this->loadNotifications($student);

        $summary = $this->buildSummary($classRows, $attendanceRows, $scoreRows, $remedialRows);

        return [
            'title'            => 'Dashboard Mahasiswa',
            'studentProfile'   => $student,
            'academicYear'     => $academic['academic_year'],
            'semesterLabel'    => $academic['semester_label'],
            'summaryCards'     => $summary['cards'],
            'summaryMeta'      => $summary['meta'],
            'classRows'        => $classRows,
            'attendanceRows'   => $attendanceRows,
            'scoreRows'        => $scoreRows,
            'remedialRows'     => $remedialRows,
            'notifications'    => $notifications,
            'hasClasses'       => !empty($classRows),
        ];
    }

    /**
     * Build detail data for a specific class
     */
    public function buildDetailData(string $userNim, int $classId): array
    {
        $academic = $this->resolveAcademicContext();
        $student  = $this->resolveStudentProfile($userNim, '');

        $classInfo      = $this->loadSingleClassInfo($classId, $academic);
        $finalScore     = $this->loadFinalScoreForStudent($classId, $student);
        $componentRows  = $this->loadComponentRows($classId, $student);
        $attendanceRows = $this->loadAttendanceDetailRows($classId, $student);
        $remedialRows   = $this->loadRemedialRows([['id' => $classId]], $student);

        return [
            'title'          => 'Detail Praktikum',
            'studentProfile' => $student,
            'classInfo'      => $classInfo,
            'finalScore'     => $finalScore,
            'componentRows'  => $componentRows,
            'attendanceRows' => $attendanceRows,
            'remedialRows'   => $remedialRows,
            'backUrl'        => site_url('mahasiswa/dashboard'),
        ];
    }

    // ─── Getter methods (with caching) ─────────────────────────────────────

    public function getBaseData(string $userNim, string $displayName = ''): array
    {
        $data = $this->dashboardData($userNim, $displayName);
        return [
            'studentProfile' => $data['studentProfile'],
            'academicYear'   => $data['academicYear'],
            'semesterLabel'  => $data['semesterLabel'],
        ];
    }

    public function getSummaryCards(string $userNim): array
    {
        return $this->dashboardData($userNim)['summaryCards'];
    }

    public function getSummaryMeta(string $userNim): array
    {
        return $this->dashboardData($userNim)['summaryMeta'];
    }

    public function getClassRows(string $userNim): array
    {
        return $this->dashboardData($userNim)['classRows'];
    }

    public function getAttendanceRows(string $userNim): array
    {
        return $this->dashboardData($userNim)['attendanceRows'];
    }

    public function getScoreRows(string $userNim): array
    {
        return $this->dashboardData($userNim)['scoreRows'];
    }

    public function getRemedialRows(string $userNim): array
    {
        return $this->dashboardData($userNim)['remedialRows'];
    }

    public function getNotifications(string $userNim): array
    {
        return $this->dashboardData($userNim)['notifications'];
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  PRIVATE HELPERS
    // ═══════════════════════════════════════════════════════════════════════

    private function dashboardData(string $userNim, string $displayName = ''): array
    {
        $key = $userNim;

        if (!isset($this->dashboardDataCache[$key])) {
            $this->dashboardDataCache[$key] = $this->buildDashboardData($userNim, $displayName);
        }

        return $this->dashboardDataCache[$key];
    }

    // ─── Academic Context ──────────────────────────────────────────────────

    private function resolveAcademicContext(): array
    {
        $month = (int) date('n');
        $year  = (int) date('Y');

        if ($month >= 7) {
            $academicYear = $year . '/' . ($year + 1);
            $semesterLabel = 'Semester Ganjil';
        } else {
            $academicYear = ($year - 1) . '/' . $year;
            $semesterLabel = 'Semester Genap';
        }

        return [
            'academic_year'  => $academicYear,
            'semester_label' => $semesterLabel,
        ];
    }

    // ─── Student Profile ───────────────────────────────────────────────────

    private function resolveStudentProfile(string $userNim, string $displayName): array
    {
        $db = $this->db;

        $profile = [
            'user_id'              => $userNim,
            'student_number'       => $userNim,
            'full_name'            => $displayName !== '' ? $displayName : 'Mahasiswa',
            'email'                => '-',
            'study_program_id'     => null,
            'study_program'        => '-',
            'class_year'           => null,
            'semester_active'      => '',
            'academic_year_active' => '',
            'status'               => 'aktif',
        ];

        if ($userNim === '') {
            return $profile;
        }

        // Get from users table — TIDAK punya deleted_at
        $userRow = $db->table('users')
            ->where('id', $userNim)
            ->get()
            ->getRowArray();

        if ($userRow !== null) {
            $profile['full_name'] = (string) ($userRow['full_name'] ?? $profile['full_name']);
            $profile['email']     = (string) ($userRow['email'] ?? '-');
        }

        // Get from students table — PUNYA deleted_at
        $studentRow = $db->table('students')
            ->where('user_nim', $userNim)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if ($studentRow !== null) {
            $profile['study_program_id'] = $studentRow['study_program_id'] ?? null;
            $profile['class_year']       = $studentRow['class_year'] ?? null;
            $profile['status']           = $studentRow['status'] ?? 'aktif';
            $profile['study_program']    = $this->resolveStudyProgramLabel($profile['study_program_id']);
        }

        return $profile;
    }

    private function resolveStudyProgramLabel(?int $studyProgramId): string
    {
        if ($studyProgramId === null || $studyProgramId <= 0) {
            return '-';
        }

        // study_programs PUNYA deleted_at
        $row = $this->db->table('study_programs')
            ->select('program_name')
            ->where('id', $studyProgramId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        return $row['program_name'] ?? '-';
    }

    // ─── Class Rows ────────────────────────────────────────────────────────

    private function loadClassRows(array $student): array
    {
        $db = $this->db;
        $userNim = $student['user_id'];

        // class_students TIDAK punya deleted_at
        $classIds = $db->table('class_students')
            ->select('practicum_class_id')
            ->where('student_nim', $userNim)
            ->where('enrollment_status', 'aktif')
            ->get()
            ->getResultArray();

        $classIdList = array_values(array_filter(array_map(
            static fn(array $row): int => (int) ($row['practicum_class_id'] ?? 0),
            $classIds
        )));

        if (empty($classIdList)) {
            return [];
        }

        // Build class details
        $builder = $db->table('practicum_classes pc');
        $builder->select([
            'pc.id',
            'pc.course_id',
            'pc.class_code',
            'pc.class_name',
            'pc.status',
            'pc.academic_year_id',
            'pc.semester_id',
            'mk.kode_mk as course_code',
            'mk.nama_mk as course_name',
        ]);

        // mata_kuliah TIDAK punya deleted_at
        $builder->join('mata_kuliah mk', 'mk.id = pc.course_id', 'left');

        // academic_years TIDAK punya deleted_at
        $builder->select('ay.year_code as academic_year_label');
        $builder->join('academic_years ay', 'ay.id = pc.academic_year_id', 'left');

        // semesters TIDAK punya deleted_at
        $builder->select('s.semester_name as semester_label');
        $builder->join('semesters s', 's.id = pc.semester_id', 'left');

        // class_lecturers TIDAK punya deleted_at
        $builder->select('u_lect.full_name as lecturer_name');
        $builder->join('class_lecturers cl', 'cl.practicum_class_id = pc.id AND cl.role_type = \'pengampu\'', 'left');
        $builder->join('users u_lect', 'u_lect.id = cl.lecturer_id', 'left');

        // class_assistants TIDAK punya deleted_at
        $builder->select('u_asst.full_name as assistant_name');
        $builder->join('class_assistants ca', 'ca.practicum_class_id = pc.id AND ca.is_main = 1', 'left');
        $builder->join('users u_asst', 'u_asst.id = ca.assistant_id', 'left');

        // practicum_classes PUNYA deleted_at
        $builder->where('pc.deleted_at', null);
        $builder->whereIn('pc.id', $classIdList);
        $builder->orderBy('mk.nama_mk', 'ASC');

        $rows = $builder->get()->getResultArray();

        $prepared = [];
        foreach ($rows as $row) {
            $classId = (int) ($row['id'] ?? 0);
            $status  = $this->normalizeClassStatus((string) ($row['status'] ?? 'aktif'));

            $prepared[] = [
                'id'              => $classId,
                'course_id'       => (int) ($row['course_id'] ?? 0),
                'course_name'     => (string) ($row['course_name'] ?? '-'),
                'course_code'     => (string) ($row['course_code'] ?? '-'),
                'class_name'      => (string) ($row['class_name'] ?? $row['class_code'] ?? '-'),
                'lecturer_name'   => (string) ($row['lecturer_name'] ?? '-'),
                'assistant_name'  => (string) ($row['assistant_name'] ?? '-'),
                'academic_year'   => (string) ($row['academic_year_label'] ?? '-'),
                'semester_label'  => (string) ($row['semester_label'] ?? '-'),
                'status'          => $status,
                'status_badge'    => self::CLASS_STATUS_BADGES[$status] ?? 'secondary',
                'detail_url'      => site_url('mahasiswa/praktikum/' . $classId . '/detail'),
            ];
        }

        return $prepared;
    }

    private function loadSingleClassInfo(int $classId, array $academic): array
    {
        $db = $this->db;

        $builder = $db->table('practicum_classes pc');
        $builder->select([
            'pc.id',
            'pc.course_id',
            'pc.class_code',
            'pc.class_name',
            'pc.status',
            'mk.kode_mk as course_code',
            'mk.nama_mk as course_name',
            'ay.year_code as academic_year_label',
            's.semester_name as semester_label',
        ]);
        $builder->join('mata_kuliah mk', 'mk.id = pc.course_id', 'left');
        $builder->join('academic_years ay', 'ay.id = pc.academic_year_id', 'left');
        $builder->join('semesters s', 's.id = pc.semester_id', 'left');

        $builder->select('u_lect.full_name as lecturer_name');
        $builder->join('class_lecturers cl', 'cl.practicum_class_id = pc.id AND cl.role_type = \'pengampu\'', 'left');
        $builder->join('users u_lect', 'u_lect.id = cl.lecturer_id', 'left');

        $builder->select('u_asst.full_name as assistant_name');
        $builder->join('class_assistants ca', 'ca.practicum_class_id = pc.id AND ca.is_main = 1', 'left');
        $builder->join('users u_asst', 'u_asst.id = ca.assistant_id', 'left');

        $builder->where('pc.id', $classId);
        $builder->where('pc.deleted_at', null);

        $row = $builder->get()->getRowArray();

        if ($row === null) {
            return [
                'id'             => $classId,
                'course_name'    => '-',
                'course_code'    => '-',
                'class_name'     => '-',
                'lecturer_name'  => '-',
                'assistant_name' => '-',
                'academic_year'  => $academic['academic_year'],
                'semester_label' => $academic['semester_label'],
                'status'         => 'aktif',
                'status_badge'   => self::CLASS_STATUS_BADGES['aktif'],
                'detail_url'     => site_url('mahasiswa/praktikum/' . $classId . '/detail'),
            ];
        }

        $status = $this->normalizeClassStatus((string) ($row['status'] ?? 'aktif'));

        return [
            'id'             => (int) ($row['id'] ?? $classId),
            'course_id'      => (int) ($row['course_id'] ?? 0),
            'course_name'    => (string) ($row['course_name'] ?? '-'),
            'course_code'    => (string) ($row['course_code'] ?? '-'),
            'class_name'     => (string) ($row['class_name'] ?? $row['class_code'] ?? '-'),
            'lecturer_name'  => (string) ($row['lecturer_name'] ?? '-'),
            'assistant_name' => (string) ($row['assistant_name'] ?? '-'),
            'academic_year'  => (string) ($row['academic_year_label'] ?? $academic['academic_year']),
            'semester_label' => (string) ($row['semester_label'] ?? $academic['semester_label']),
            'status'         => $status,
            'status_badge'   => self::CLASS_STATUS_BADGES[$status] ?? 'secondary',
            'detail_url'     => site_url('mahasiswa/praktikum/' . $classId . '/detail'),
        ];
    }

    // ─── Attendance ────────────────────────────────────────────────────────

    /**
     * PERBAIKAN: Gunakan mapping class_students.id ke attendance_records.student_id
     * karena student_id di attendance_records adalah INT (class_students.id),
     * bukan CHAR (users.id / NIM)
     */
    private function loadAttendanceSummaryRows(array $classRows, array $student): array
    {
        $db = $this->db;
        $userNim = $student['user_id'];

        if (empty($classRows)) {
            return [];
        }

        // Pre-fetch class_students IDs untuk semua kelas sekaligus
        $classIds = array_values(array_map(
            static fn(array $row): int => (int) ($row['id'] ?? 0),
            $classRows
        ));

        // Mapping: practicum_class_id => class_students.id
        $csRows = $db->table('class_students')
            ->select('id, practicum_class_id')
            ->whereIn('practicum_class_id', $classIds)
            ->where('student_nim', $userNim)
            ->get()
            ->getResultArray();

        $csMap = [];
        foreach ($csRows as $csRow) {
            $csMap[(int)$csRow['practicum_class_id']] = (int)$csRow['id'];
        }

        $rows = [];

        foreach ($classRows as $classRow) {
            $classId = (int) ($classRow['id'] ?? 0);
            $csId = $csMap[$classId] ?? 0;

            // attendance_sessions PUNYA deleted_at
            $totalSessions = (int) $db->table('attendance_sessions')
                ->where('practicum_class_id', $classId)
                ->where('deleted_at', null)
                ->countAllResults();

            $counts = [
                'hadir'   => 0,
                'izin'    => 0,
                'sakit'   => 0,
                'alfa'    => 0,
                'susulan' => 0,
            ];

            if ($csId > 0) {
                // attendance_records TIDAK punya deleted_at
                // attendance_statuses TIDAK punya deleted_at
                $records = $db->table('attendance_records ar')
                    ->select('ar.attendance_status_id, ars.code as status_code, ars.name as status_name')
                    ->join('attendance_sessions s', 's.id = ar.attendance_session_id', 'inner')
                    ->join('attendance_statuses ars', 'ars.id = ar.attendance_status_id', 'left')
                    ->where('s.practicum_class_id', $classId)
                    ->where('s.deleted_at', null)
                    ->where('ar.student_id', $csId)  // ✅ Gunakan class_students.id (INT)
                    ->get()
                    ->getResultArray();

                foreach ($records as $record) {
                    $bucket = $this->normalizeAttendanceStatus((string) ($record['status_code'] ?? ''));
                    if (isset($counts[$bucket])) {
                        $counts[$bucket]++;
                    }
                }
            }

            $presentTotal = $counts['hadir'] + $counts['izin'] + $counts['sakit'] + $counts['susulan'];
            $percentage   = $this->percentage($presentTotal, $totalSessions);
            $status       = $this->attendanceStatusLabel($percentage, $totalSessions);

            $rows[] = [
                'class_id'              => $classId,
                'course_name'           => (string) ($classRow['course_name'] ?? '-'),
                'class_name'            => (string) ($classRow['class_name'] ?? '-'),
                'total_sessions'        => $totalSessions,
                'hadir'                 => $counts['hadir'],
                'izin'                  => $counts['izin'],
                'sakit'                 => $counts['sakit'],
                'alfa'                  => $counts['alfa'],
                'susulan'               => $counts['susulan'],
                'attendance_percentage' => $percentage,
                'status'                => $status,
                'status_badge'          => self::ATTENDANCE_STATUS_BADGES[$status] ?? 'secondary',
            ];
        }

        return $rows;
    }

    /**
     * PERBAIKAN: Sama seperti summary, gunakan class_students.id mapping
     */
    private function loadAttendanceDetailRows(int $classId, array $student): array
    {
        $db = $this->db;
        $userNim = $student['user_id'];

        // Cari class_students.id untuk mapping ke attendance_records.student_id
        $csRow = $db->table('class_students')
            ->select('id')
            ->where('practicum_class_id', $classId)
            ->where('student_nim', $userNim)
            ->get()
            ->getRow();

        $studentRecordId = $csRow ? (int)$csRow->id : 0;

        // attendance_sessions PUNYA deleted_at
        // attendance_records TIDAK punya deleted_at
        // attendance_statuses TIDAK punya deleted_at
        $rows = $db->table('attendance_sessions s')
            ->select('s.id, s.meeting_no, s.session_date, s.topic, ars.name as status_name, ar.notes')
            ->join('attendance_records ar', 'ar.attendance_session_id = s.id AND ar.student_id = ' . $studentRecordId, 'left')
            ->join('attendance_statuses ars', 'ars.id = ar.attendance_status_id', 'left')
            ->where('s.practicum_class_id', $classId)
            ->where('s.deleted_at', null)
            ->orderBy('s.meeting_no', 'ASC')
            ->get()
            ->getResultArray();

        $prepared = [];
        foreach ($rows as $row) {
            $statusName = (string) ($row['status_name'] ?? '');
            $prepared[] = [
                'meeting_no'   => (string) ($row['meeting_no'] ?? '-'),
                'session_date' => (string) ($row['session_date'] ?? '-'),
                'topic'        => (string) ($row['topic'] ?? '-'),
                'status'       => $statusName !== '' ? $statusName : 'Belum Diabsen',
                'notes'        => (string) ($row['notes'] ?? '-'),
            ];
        }

        return $prepared;
    }

    // ─── Scores ────────────────────────────────────────────────────────────

    private function loadScoreSummaryRows(array $classRows, array $student): array
    {
        if (empty($classRows)) {
            return [];
        }

        $classIds = array_values(array_map(
            static fn(array $row): int => (int) ($row['id'] ?? 0),
            $classRows
        ));

        $scoreStats  = $this->loadScoreStats($classIds, $student);
        $finalScores = $this->loadFinalScores($classIds, $student);
        $remedialMap = $this->loadRemedialMap($classIds, $student);

        $rows = [];

        foreach ($classRows as $classRow) {
            $classId = (int) ($classRow['id'] ?? 0);
            $stat = $scoreStats[$classId] ?? ['filled' => 0, 'total' => 0, 'missing' => 0];
            $finalRow = $finalScores[$classId] ?? [];
            $remedialRow = $remedialMap[$classId] ?? [];

            $progress = 0.0;
            if ($stat['total'] > 0) {
                $progress = $this->percentage($stat['filled'], $stat['total']);
            } elseif (($finalRow['final_score'] ?? null) !== null) {
                $progress = 100.0;
            }

            $scoreStatus = $this->normalizeScoreStatus(
                (string) ($finalRow['validation_status'] ?? $finalRow['status'] ?? '')
            );

            if ($scoreStatus === 'draft' && $stat['filled'] > 0) {
                $scoreStatus = ($stat['filled'] >= $stat['total'] && $stat['total'] > 0)
                    ? 'reviewed'
                    : 'submitted';
            }

            $academicStatus = $this->resolveAcademicStatus(
                isset($finalRow['final_score']) ? (float) $finalRow['final_score'] : null,
                (string) ($finalRow['grade_letter'] ?? ''),
                (string) ($remedialRow['status'] ?? '')
            );

            $rows[] = [
                'class_id'           => $classId,
                'course_name'        => (string) ($classRow['course_name'] ?? '-'),
                'final_score'        => isset($finalRow['final_score']) ? (float) $finalRow['final_score'] : null,
                'grade_letter'       => (string) ($finalRow['grade_letter'] ?? '-'),
                'score_status'       => $scoreStatus,
                'score_status_badge' => self::SCORE_STATUS_BADGES[$scoreStatus] ?? 'secondary',
                'academic_status'    => $academicStatus,
                'academic_badge'     => self::ACADEMIC_STATUS_BADGES[$academicStatus] ?? 'secondary',
                'score_progress'     => $progress,
                'missing_count'      => (int) ($stat['missing'] ?? 0),
                'detail_url'         => site_url('mahasiswa/praktikum/' . $classId . '/detail'),
            ];
        }

        return $rows;
    }

    private function loadScoreStats(array $classIds, array $student): array
    {
        $db = $this->db;
        $userNim = $student['user_id'];

        if (empty($classIds)) {
            return [];
        }

        $csRows = $db->table('class_students')
            ->select('id, practicum_class_id')
            ->whereIn('practicum_class_id', $classIds)
            ->where('student_nim', $userNim)
            ->get()
            ->getResultArray();

        $csIds = array_column($csRows, 'id');
        if (empty($csIds)) {
            return [];
        }

        // score_entries PUNYA deleted_at
        $rows = $db->table('score_entries')
            ->select('practicum_class_id as class_id')
            ->select('SUM(CASE WHEN score_value IS NOT NULL THEN 1 ELSE 0 END) AS filled_count', false)
            ->select('SUM(CASE WHEN score_value IS NULL THEN 1 ELSE 0 END) AS missing_count', false)
            ->select('COUNT(*) AS total_count', false)
            ->whereIn('practicum_class_id', $classIds)
            ->whereIn('student_id', $csIds)
            ->where('deleted_at', null)
            ->groupBy('practicum_class_id')
            ->get()
            ->getResultArray();

        $stats = [];
        foreach ($rows as $row) {
            $classId = (int) ($row['class_id'] ?? 0);
            $stats[$classId] = [
                'filled'  => (int) ($row['filled_count'] ?? 0),
                'missing' => (int) ($row['missing_count'] ?? 0),
                'total'   => (int) ($row['total_count'] ?? 0),
            ];
        }

        return $stats;
    }

    private function loadFinalScores(array $classIds, array $student): array
    {
        $db = $this->db;
        $userNim = $student['user_id'];

        if (empty($classIds)) {
            return [];
        }

        $csRows = $db->table('class_students')
            ->select('id, practicum_class_id')
            ->whereIn('practicum_class_id', $classIds)
            ->where('student_nim', $userNim)
            ->get()
            ->getResultArray();

        $csIds = array_column($csRows, 'id');
        if (empty($csIds)) {
            return [];
        }

        // final_scores PUNYA deleted_at
        $rows = $db->table('final_scores')
            ->select('practicum_class_id as class_id, final_score, grade_letter, status, validation_status, notes')
            ->whereIn('practicum_class_id', $classIds)
            ->whereIn('student_id', $csIds)
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        $scores = [];
        foreach ($rows as $row) {
            $scores[(int) ($row['class_id'] ?? 0)] = $row;
        }

        return $scores;
    }

    private function loadFinalScoreForStudent(int $classId, array $student): array
    {
        $db = $this->db;
        $userNim = $student['user_id'];

        $csRow = $db->table('class_students')
            ->select('id')
            ->where('practicum_class_id', $classId)
            ->where('student_nim', $userNim)
            ->get()
            ->getRow();

        $studentRecordId = $csRow ? (int)$csRow->id : 0;

        // final_scores PUNYA deleted_at
        $row = $db->table('final_scores')
            ->select('final_score, grade_letter, status, validation_status, notes')
            ->where('practicum_class_id', $classId)
            ->where('student_id', $studentRecordId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        $scoreStatus = $this->normalizeScoreStatus(
            (string) ($row['validation_status'] ?? $row['status'] ?? '')
        );
        $academicStatus = $this->resolveAcademicStatus(
            isset($row['final_score']) ? (float) $row['final_score'] : null,
            (string) ($row['grade_letter'] ?? ''),
            ''
        );

        return [
            'final_score'      => isset($row['final_score']) ? (float) $row['final_score'] : null,
            'grade_letter'     => (string) ($row['grade_letter'] ?? '-'),
            'status'           => $scoreStatus,
            'status_badge'     => self::SCORE_STATUS_BADGES[$scoreStatus] ?? 'secondary',
            'academic_status'  => $academicStatus,
            'academic_badge'   => self::ACADEMIC_STATUS_BADGES[$academicStatus] ?? 'secondary',
            'notes'            => (string) ($row['notes'] ?? '-'),
        ];
    }

    // ─── Components ──────────────────────────────────────────────────────

    private function loadComponentRows(int $classId, array $student): array
    {
        $db = $this->db;
        $userNim = $student['user_id'];

        $csRow = $db->table('class_students')
            ->select('id')
            ->where('practicum_class_id', $classId)
            ->where('student_nim', $userNim)
            ->get()
            ->getRow();

        $studentRecordId = $csRow ? (int)$csRow->id : 0;

        if ($studentRecordId === 0) {
            return [];
        }

        // score_entries PUNYA deleted_at
        $builder = $db->table('score_entries se');
        $builder->select('se.component_id, se.subcomponent_id, se.score_value, se.max_score, se.notes');
        $builder->where('se.practicum_class_id', $classId);
        $builder->where('se.student_id', $studentRecordId);
        $builder->where('se.deleted_at', null);

        // assessment_components PUNYA deleted_at
        $builder->select('ac.component_name, ac.weight as component_weight, ac.max_score as component_max_score');
        $builder->join('assessment_components ac', 'ac.id = se.component_id AND ac.deleted_at IS NULL', 'left');

        // assessment_subcomponents PUNYA deleted_at
        $builder->select('asc.subcomponent_name, asc.weight as subcomponent_weight, asc.max_score as subcomponent_max_score');
        $builder->join('assessment_subcomponents asc', 'asc.id = se.subcomponent_id AND asc.deleted_at IS NULL', 'left');

        $builder->orderBy('ac.sort_order', 'ASC');
        $builder->orderBy('asc.sort_order', 'ASC');

        $rows = $builder->get()->getResultArray();

        $prepared = [];
        foreach ($rows as $row) {
            $scoreValue = $row['score_value'];
            $componentName = (string) ($row['component_name'] ?? 'Komponen #' . ($row['component_id'] ?? '-'));
            $subcomponentName = (string) ($row['subcomponent_name'] ?? '-');

            $weightValue = $row['subcomponent_weight'] ?? $row['component_weight'] ?? null;
            $weightValue = is_numeric($weightValue) ? (float) $weightValue : null;

            $maxScore = $row['subcomponent_max_score'] ?? $row['component_max_score'] ?? 100;
            $maxScore = is_numeric($maxScore) ? (float) $maxScore : 100;

            $weightedScore = null;
            if ($weightValue !== null && $scoreValue !== null && is_numeric($scoreValue)) {
                $weightedScore = ((float) $scoreValue) * ($weightValue / 100);
            }

            $prepared[] = [
                'component_name'    => $componentName,
                'subcomponent_name' => $subcomponentName,
                'weight'            => $weightValue,
                'max_score'         => $maxScore,
                'score_value'       => $scoreValue,
                'weighted_score'    => $weightedScore,
                'notes'             => (string) ($row['notes'] ?? '-'),
            ];
        }

        return $prepared;
    }

    // ─── Remedial ────────────────────────────────────────────────────────

    private function loadRemedialMap(array $classIds, array $student): array
    {
        $db = $this->db;
        $userNim = $student['user_id'];

        if (empty($classIds)) {
            return [];
        }

        // $classIds sudah berupa array of int, tidak perlu array_map lagi!
        $classIdList = array_values(array_filter($classIds));

        $csRows = $db->table('class_students')
            ->select('id, practicum_class_id')
            ->whereIn('practicum_class_id', $classIdList)
            ->where('student_nim', $userNim)
            ->get()
            ->getResultArray();

        $csIds = array_column($csRows, 'id');
        if (empty($csIds)) {
            return [];
        }

        // remedial_participants PUNYA deleted_at
        $rows = $db->table('remedial_participants')
            ->select('practicum_class_id as class_id, status, reason')
            ->whereIn('practicum_class_id', $classIdList)
            ->whereIn('student_id', $csIds)
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($rows as $row) {
            $map[(int) ($row['class_id'] ?? 0)] = $row;
        }

        return $map;
    }

    private function loadRemedialRows(array $classRows, array $student, bool $forDetail = false): array
    {
        $db = $this->db;
        $userNim = $student['user_id'];

        if (empty($classRows)) {
            return [];
        }

        $classIdList = [];
        foreach ($classRows as $row) {
            if (is_array($row)) {
                $classIdList[] = (int) ($row['id'] ?? 0);
            } elseif (is_int($row)) {
                $classIdList[] = $row;
            }
        }
        $classIdList = array_values(array_filter($classIdList));

        if (empty($classIdList)) {
            return [];
        }

        // Ambil class_students.id untuk mapping ke student_id di remedial_participants
        $csRows = $db->table('class_students')
            ->select('id, practicum_class_id')
            ->whereIn('practicum_class_id', $classIdList)
            ->where('student_nim', $userNim)
            ->get()
            ->getResultArray();

        $csMap = [];
        foreach ($csRows as $csRow) {
            $csMap[(int)$csRow['practicum_class_id']] = (int)$csRow['id'];
        }

        if (empty($csMap)) {
            return [];
        }

        $csIds = array_values($csMap);

        // Query remedial_participants dengan join ke remedial_periods
        $builder = $db->table('remedial_participants rp');
        $builder->select([
            'rp.id as participant_id',
            'rp.remedial_period_id',
            'rp.student_id',
            'rp.practicum_class_id',
            'rp.status',
            'rp.reason',
            'rp.before_score',
            'rp.after_score',
            'rp.max_after_score',
            'rp.validated_by',
            'rp.validated_at',
        ]);

        // Join remedial_periods
        $builder->select('rpd.remedial_code, rpd.title as period_title, rpd.start_date, rpd.end_date, rpd.registration_deadline, rpd.status as period_status');
        $builder->join('remedial_periods rpd', 'rpd.id = rp.remedial_period_id AND rpd.deleted_at IS NULL', 'left');

        // Join practicum_classes untuk info kelas
        $builder->select('pc.class_code, pc.class_name, pc.course_id');
        $builder->join('practicum_classes pc', 'pc.id = rp.practicum_class_id AND pc.deleted_at IS NULL', 'left');

        // Join mata_kuliah (bukan coursemodel!)
        $builder->select('mk.kode_mk as course_code, mk.nama_mk as course_name');
        $builder->join('mata_kuliah mk', 'mk.id = pc.course_id', 'left');

        // Join remedial_results untuk hasil akhir
        $builder->select('rr.final_score_before, rr.final_score_after, rr.grade_letter_before, rr.grade_letter_after, rr.is_passed, rr.validation_status as result_validation_status, rr.notes as result_notes');
        $builder->join('remedial_results rr', 'rr.remedial_participant_id = rp.id', 'left');

        $builder->whereIn('rp.practicum_class_id', $classIdList);
        $builder->whereIn('rp.student_id', $csIds);
        $builder->where('rp.deleted_at', null);
        $builder->orderBy('rpd.start_date', 'DESC');
        $builder->orderBy('rp.created_at', 'DESC');

        $rows = $builder->get()->getResultArray();

        $prepared = [];
        foreach ($rows as $row) {
            $status = $this->normalizeRemedialStatus((string) ($row['status'] ?? ''));
            $periodStatus = strtolower((string) ($row['period_status'] ?? ''));

            // Tentukan jenis remedial berdasarkan data
            $remedialType = $this->resolveRemedialType($row);

            // Format jadwal
            $schedule = $this->formatRemedialSchedule(
                (string) ($row['start_date'] ?? ''),
                (string) ($row['end_date'] ?? ''),
                (string) ($row['registration_deadline'] ?? '')
            );

            // Tentukan komponen yang diremedial
            $componentLabel = $this->loadRemedialComponentsLabel((int) ($row['remedial_period_id'] ?? 0));

            $prepared[] = [
                'participant_id'      => (int) ($row['participant_id'] ?? 0),
                'period_id'           => (int) ($row['remedial_period_id'] ?? 0),
                'class_id'            => (int) ($row['practicum_class_id'] ?? 0),
                'course_name'         => (string) ($row['course_name'] ?? '-'),
                'course_code'         => (string) ($row['course_code'] ?? '-'),
                'class_name'          => (string) ($row['class_name'] ?? $row['class_code'] ?? '-'),
                'reason'              => (string) ($row['reason'] ?? '-'),
                'remedial_type'       => $remedialType,
                'component_label'     => $componentLabel,
                'schedule'            => $schedule,
                'status'              => $this->formatRemedialStatusLabel($status),
                'status_badge'        => self::REMEDIAL_STATUS_BADGES[$status] ?? 'secondary',
                'score_before'        => isset($row['before_score']) ? (float) $row['before_score'] : null,
                'score_after'         => isset($row['after_score']) ? (float) $row['after_score'] : null,
                'max_after_score'     => isset($row['max_after_score']) ? (float) $row['max_after_score'] : null,
                'final_score_before'  => isset($row['final_score_before']) ? (float) $row['final_score_before'] : null,
                'final_score_after'   => isset($row['final_score_after']) ? (float) $row['final_score_after'] : null,
                'grade_before'        => (string) ($row['grade_letter_before'] ?? '-'),
                'grade_after'         => (string) ($row['grade_letter_after'] ?? '-'),
                'is_passed'           => (bool) ($row['is_passed'] ?? false),
                'period_status'       => $periodStatus,
                'period_title'        => (string) ($row['period_title'] ?? $row['remedial_code'] ?? '-'),
                'notes'               => (string) ($row['result_notes'] ?? $row['reason'] ?? '-'),
                'validated_at'        => (string) ($row['validated_at'] ?? '-'),
            ];
        }

        return $prepared;
    }

    /**
     * Resolve jenis remedial berdasarkan data
     */
    private function resolveRemedialType(array $row): string
    {
        $beforeScore = $row['before_score'] ?? null;
        $afterScore = $row['after_score'] ?? null;

        if ($beforeScore !== null && $afterScore !== null) {
            return 'Nilai Akhir';
        }

        // Cek apakah ada komponen spesifik
        $db = $this->db;
        $hasComponents = $db->table('remedial_scores')
            ->where('remedial_participant_id', (int) ($row['participant_id'] ?? 0))
            ->countAllResults() > 0;

        return $hasComponents ? 'Komponen Tertentu' : 'Nilai Akhir';
    }

    /**
     * Load label komponen remedial
     */
    private function loadRemedialComponentsLabel(int $periodId): string
    {
        if ($periodId <= 0) {
            return 'Semua Komponen';
        }

        $db = $this->db;

        $rows = $db->table('remedial_components rc')
            ->select('ac.component_name')
            ->join('assessment_components ac', 'ac.id = rc.component_id AND ac.deleted_at IS NULL', 'left')
            ->where('rc.remedial_period_id', $periodId)
            ->get()
            ->getResultArray();

        if (empty($rows)) {
            return 'Semua Komponen';
        }

        $names = array_filter(array_column($rows, 'component_name'));

        if (empty($names)) {
            return 'Semua Komponen';
        }

        return implode(', ', array_slice($names, 0, 3)) . (count($names) > 3 ? '...' : '');
    }

    /**
     * Format jadwal remedial
     */
    private function formatRemedialSchedule(string $startDate, string $endDate, string $deadline): string
    {
        $parts = [];

        if ($startDate !== '' && $startDate !== '0000-00-00') {
            $parts[] = $this->formatDateOnly($startDate) . ' s.d. ' . ($endDate !== '' && $endDate !== '0000-00-00' ? $this->formatDateOnly($endDate) : '?');
        }

        if ($deadline !== '' && $deadline !== '0000-00-00') {
            $parts[] = 'Daftar: ' . $this->formatDateOnly($deadline);
        }

        return empty($parts) ? '-' : implode(' | ', $parts);
    }

    /**
     * Format tanggal saja
     */
    private function formatDateOnly(string $value): string
    {
        if ($value === '' || $value === '0000-00-00') return '-';
        $time = strtotime($value);
        return $time !== false ? date('d M Y', $time) : $value;
    }

    /**
     * Format label status remedial untuk tampilan
     */
    private function formatRemedialStatusLabel(string $status): string
    {
        return match ($status) {
            'eligible'        => 'Eligible',
            'terdaftar'       => 'Terdaftar',
            'dijadwalkan'     => 'Dijadwalkan',
            'sudah_dinilai'   => 'Sudah Dinilai',
            'validated'       => 'Tervalidasi',
            'tidak_mengikuti' => 'Tidak Mengikuti',
            'dibatalkan'      => 'Dibatalkan',
            default           => ucfirst($status),
        };
    }

    private function loadNotifications(array $student): array
    {
        $db = $this->db;
        $userNim = $student['user_id'];

        if ($userNim === '') {
            return [];
        }

        $items = [];

        // ─── 1. Notifikasi dari tabel notifications ─────────────────────
        $notifRows = $db->table('notifications')
            ->select('title, message, type, reference_type, reference_id, is_read, created_at')
            ->where('user_id', $userNim)
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        foreach ($notifRows as $row) {
            $type = (string) ($row['type'] ?? 'info');
            $refType = (string) ($row['reference_type'] ?? '');

            // Tentukan icon dan kategori berdasarkan reference_type
            $icon = $this->resolveNotificationIcon($refType, $type);
            $category = $this->resolveNotificationCategory($refType);

            $items[] = [
                'id'          => null, // notifications tidak punya id di select
                'source'      => 'notification',
                'title'       => (string) ($row['title'] ?? 'Notifikasi'),
                'message'     => (string) ($row['message'] ?? '-'),
                'time'        => $this->formatDateTime((string) ($row['created_at'] ?? '')),
                'badge'       => $this->notificationBadge($type),
                'icon'        => $icon,
                'category'    => $category,
                'is_read'     => (bool) ($row['is_read'] ?? false),
                'reference_type' => $refType,
                'reference_id'   => (string) ($row['reference_id'] ?? ''),
            ];
        }

        // ─── 2. Aktivitas dari tabel activity_logs ────────────────────────
        // Ambil log yang relevan untuk mahasiswa: nilai, validasi, remedial, absensi
        $logRows = $db->table('activity_logs')
            ->select('action, module, target_type, target_id, description, created_at')
            ->where('user_id', $userNim)
            ->whereIn('module', ['score', 'validation', 'remedial', 'attendance', 'practicum', 'notification'])
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        foreach ($logRows as $row) {
            $module = (string) ($row['module'] ?? '');
            $action = (string) ($row['action'] ?? '');
            $desc = (string) ($row['description'] ?? '');

            // Buat title dan message dari log
            $title = $this->formatLogTitle($module, $action, $row['target_type'] ?? '');
            $message = $desc !== '' ? $desc : $title;

            $items[] = [
                'id'          => null,
                'source'      => 'activity',
                'title'       => $title,
                'message'     => $message,
                'time'        => $this->formatDateTime((string) ($row['created_at'] ?? '')),
                'badge'       => $this->resolveLogBadge($module, $action),
                'icon'        => $this->resolveLogIcon($module),
                'category'    => $this->resolveLogCategory($module),
                'is_read'     => true, // log dianggap sudah dibaca
                'reference_type' => (string) ($row['target_type'] ?? ''),
                'reference_id'   => (string) ($row['target_id'] ?? ''),
            ];
        }

        // ─── 3. Sort berdasarkan waktu (terbaru dulu) ───────────────────
        usort($items, function (array $a, array $b): int {
            // Parse kembali untuk sorting (formatDateTime mengubah format)
            // Gunakan created_at asli jika perlu, tapi untuk sekarang
            // kita sort berdasarkan urutan query (sudah DESC)
            return 0; // Sudah terurut dari query masing-masing
        });

        // Batasi total 20 item terbaru
        return array_slice($items, 0, 20);
    }

    /**
     * Resolve icon untuk notifikasi berdasarkan reference_type
     */
    private function resolveNotificationIcon(string $refType, string $type): string
    {
        return match (strtolower($refType)) {
            'score', 'nilai'           => 'bi-clipboard-data',
            'validation', 'validasi'   => 'bi-check-circle',
            'remedial'                 => 'bi-arrow-repeat',
            'attendance', 'kehadiran'  => 'bi-calendar-check',
            'practicum', 'praktikum'   => 'bi-journal-check',
            'revision', 'revisi'       => 'bi-pencil-square',
            default => match (strtolower($type)) {
                'success' => 'bi-check-circle-fill',
                'warning' => 'bi-exclamation-triangle-fill',
                'danger'  => 'bi-x-circle-fill',
                default   => 'bi-bell-fill',
            },
        };
    }

    /**
     * Resolve kategori notifikasi
     */
    private function resolveNotificationCategory(string $refType): string
    {
        return match (strtolower($refType)) {
            'score', 'nilai'           => 'Nilai',
            'validation', 'validasi'   => 'Validasi',
            'remedial'                 => 'Remedial',
            'attendance', 'kehadiran'  => 'Kehadiran',
            'practicum', 'praktikum'   => 'Praktikum',
            'revision', 'revisi'       => 'Revisi',
            default                    => 'Umum',
        };
    }

    /**
     * Format title dari activity log
     */
    private function formatLogTitle(string $module, string $action, string $targetType): string
    {
        $moduleLabel = match (strtolower($module)) {
            'score'      => 'Nilai',
            'validation' => 'Validasi',
            'remedial'   => 'Remedial',
            'attendance' => 'Kehadiran',
            'practicum'  => 'Praktikum',
            default      => ucfirst($module),
        };

        $actionLabel = match (strtolower($action)) {
            'create', 'input'    => 'Input',
            'update', 'change'   => 'Perubahan',
            'delete'             => 'Penghapusan',
            'validate', 'approved' => 'Validasi',
            'lock'               => 'Penguncian',
            'submit'             => 'Pengiriman',
            'request_revision'   => 'Permintaan Revisi',
            default              => ucfirst($action),
        };

        $targetLabel = match (strtolower($targetType)) {
            'score_entry'   => 'Nilai Komponen',
            'final_score'   => 'Nilai Akhir',
            'remedial_participant' => 'Peserta Remedial',
            'attendance_record'    => 'Absensi',
            default         => ucfirst($targetType),
        };

        return "{$actionLabel} {$moduleLabel}" . ($targetLabel !== '' ? " - {$targetLabel}" : '');
    }

    /**
     * Resolve badge untuk activity log
     */
    private function resolveLogBadge(string $module, string $action): string
    {
        $key = strtolower($module . '.' . $action);

        return match (true) {
            str_contains($key, 'validate') || str_contains($key, 'approve') => 'success',
            str_contains($key, 'lock') => 'dark',
            str_contains($key, 'delete') => 'danger',
            str_contains($key, 'update') || str_contains($key, 'change') => 'warning',
            str_contains($key, 'create') || str_contains($key, 'input') => 'info',
            str_contains($key, 'remedial') => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Resolve icon untuk activity log
     */
    private function resolveLogIcon(string $module): string
    {
        return match (strtolower($module)) {
            'score'      => 'bi-clipboard-data',
            'validation' => 'bi-check-circle',
            'remedial'   => 'bi-arrow-repeat',
            'attendance' => 'bi-calendar-check',
            'practicum'  => 'bi-journal-check',
            'notification' => 'bi-bell',
            default      => 'bi-activity',
        };
    }

    /**
     * Resolve kategori untuk activity log
     */
    private function resolveLogCategory(string $module): string
    {
        return match (strtolower($module)) {
            'score'      => 'Nilai',
            'validation' => 'Validasi',
            'remedial'   => 'Remedial',
            'attendance' => 'Kehadiran',
            'practicum'  => 'Praktikum',
            default      => 'Aktivitas',
        };
    }

    // ─── Summary Builder ───────────────────────────────────────────────────

    private function buildSummary(array $classRows, array $attendanceRows, array $scoreRows, array $remedialRows): array
    {
        $totalClasses = count($classRows);
        $attendanceAverage = $this->averagePercent($attendanceRows, 'attendance_percentage');
        $scoreAverage = $this->averagePercent($scoreRows, 'score_progress');

        $passedCount = count(array_filter($scoreRows, static fn(array $row): bool => ($row['academic_status'] ?? '') === 'Lulus'));
        $remedialCount = count(array_filter($scoreRows, static fn(array $row): bool => ($row['academic_status'] ?? '') === 'Remedial'));
        $notFinalCount = count(array_filter($scoreRows, static fn(array $row): bool => !in_array($row['score_status'] ?? '', ['locked', 'validated'], true)));
        $incompleteScores = count(array_filter($scoreRows, static fn(array $row): bool => (float) ($row['score_progress'] ?? 0) < 100));
        $missingEntries = array_sum(array_map(static fn(array $row): int => (int) ($row['missing_count'] ?? 0), $scoreRows));

        return [
            'cards' => [
                [
                    'title'       => 'Praktikum Diikuti',
                    'value'       => $totalClasses,
                    'color'       => 'primary',
                    'icon'        => 'bi-journal-check',
                    'description' => 'Total kelas praktikum aktif',
                ],
                [
                    'title'       => 'Rata-rata Kehadiran',
                    'value'       => $attendanceAverage . '%',
                    'color'       => 'success',
                    'icon'        => 'bi-calendar-check',
                    'description' => 'Rata-rata presensi',
                ],
                [
                    'title'       => 'Praktikum Lulus',
                    'value'       => $passedCount,
                    'color'       => 'info',
                    'icon'        => 'bi-award',
                    'description' => 'Status lulus saat ini',
                ],
                [
                    'title'       => 'Praktikum Remedial',
                    'value'       => $remedialCount,
                    'color'       => 'warning',
                    'icon'        => 'bi-arrow-repeat',
                    'description' => 'Perlu remedial',
                ],
                [
                    'title'       => 'Belum Final',
                    'value'       => $notFinalCount,
                    'color'       => 'secondary',
                    'icon'        => 'bi-hourglass-split',
                    'description' => 'Nilai belum terkunci',
                ],
                [
                    'title'       => 'Nilai Kosong',
                    'value'       => $incompleteScores,
                    'color'       => 'danger',
                    'icon'        => 'bi-clipboard-x',
                    'description' => $missingEntries > 0 ? $missingEntries . ' komponen belum dinilai' : 'Kelengkapan nilai',
                ],
            ],
            'meta' => [
                'total_classes'       => $totalClasses,
                'attendance_average'  => $attendanceAverage,
                'score_average'       => $scoreAverage,
                'finalized'           => max(0, $totalClasses - $notFinalCount),
                'not_final'           => $notFinalCount,
            ],
        ];
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  NORMALIZERS & FORMATTERS
    // ═══════════════════════════════════════════════════════════════════════

    private function normalizeClassStatus(string $status): string
    {
        $status = strtolower(trim($status));
        return match ($status) {
            'aktif', 'active'              => 'aktif',
            'selesai', 'completed', 'closed' => 'selesai',
            'terkunci', 'locked'           => 'terkunci',
            'diarsipkan', 'archived'       => 'diarsipkan',
            default                       => 'aktif',
        };
    }

    private function normalizeScoreStatus(string $status): string
    {
        $status = strtolower(trim($status));
        return match ($status) {
            'draft'                       => 'draft',
            'submitted', 'submit'         => 'submitted',
            'reviewed', 'review'          => 'reviewed',
            'validated', 'valid', 'approved' => 'validated',
            'locked', 'final'             => 'locked',
            'revision requested', 'revisi', 'request revisi', 'revision_requested' => 'revision_requested',
            'revised'                     => 'revised',
            default                       => 'draft',
        };
    }

    private function normalizeAttendanceStatus(string $status): string
    {
        $status = strtolower(trim($status));
        if ($status === '') return 'alfa';

        return match (true) {
            in_array($status, ['h', 'hadir', 'present', 'attendance']) => 'hadir',
            in_array($status, ['i', 'izin', 'excused', 'permit'])      => 'izin',
            in_array($status, ['s', 'sakit', 'sick'])                   => 'sakit',
            in_array($status, ['susulan', 'makeup', 'make-up'])         => 'susulan',
            in_array($status, ['a', 'alfa', 'alpha', 'absen', 'absent']) => 'alfa',
            default                                                     => 'alfa',
        };
    }

    private function attendanceStatusLabel(float $percentage, int $totalSessions): string
    {
        if ($totalSessions <= 0) return 'Belum Ada Pertemuan';
        if ($percentage >= 75) return 'Aman';
        if ($percentage >= 60) return 'Perlu Perhatian';
        return 'Kurang Kehadiran';
    }

    private function formatAttendanceLabel(string $status): string
    {
        $status = trim($status);
        return $status !== '' ? ucfirst($status) : 'Belum Diabsen';
    }

    private function normalizeRemedialStatus(string $status): string
    {
        $status = strtolower(trim($status));
        return match ($status) {
            'eligible', 'eligible remedial' => 'eligible',
            'terdaftar', 'registered'       => 'terdaftar',
            'dijadwalkan', 'scheduled'      => 'dijadwalkan',
            'scored', 'sudah dinilai'       => 'sudah_dinilai',
            'validated'                     => 'validated',
            'tidak mengikuti', 'absent', 'no-show' => 'tidak_mengikuti',
            'dibatalkan', 'cancelled', 'canceled' => 'dibatalkan',
            default                         => 'eligible',
        };
    }

    private function resolveAcademicStatus(?float $finalScore, string $gradeLetter, string $remedialStatus): string
    {
        $remedialStatus = strtolower(trim($remedialStatus));
        if ($remedialStatus !== '' && $remedialStatus !== 'eligible') {
            return 'Remedial';
        }

        if ($finalScore === null && $gradeLetter === '') {
            return 'Belum Lengkap';
        }

        $gradeLetter = strtoupper(trim($gradeLetter));
        if ($gradeLetter !== '') {
            return in_array($gradeLetter, ['A', 'B', 'C'], true) ? 'Lulus' : 'Tidak Lulus';
        }

        if ($finalScore === null) return 'Belum Lengkap';

        return $finalScore >= 60 ? 'Lulus' : 'Tidak Lulus';
    }

    // ─── Math Helpers ────────────────────────────────────────────────────

    private function averagePercent(array $rows, string $field): int
    {
        if (empty($rows)) return 0;

        $values = [];
        foreach ($rows as $row) {
            if (isset($row[$field]) && is_numeric($row[$field])) {
                $values[] = (float) $row[$field];
            }
        }

        if (empty($values)) return 0;

        return (int) round(array_sum($values) / count($values));
    }

    private function percentage(int $filled, int $expected): float
    {
        if ($expected <= 0) return 0.0;
        return round(($filled / $expected) * 100, 1);
    }

    // ─── Format Helpers ──────────────────────────────────────────────────

    private function notificationBadge(string $type): string
    {
        $type = strtolower(trim($type));
        return match ($type) {
            'success', 'validated', 'locked' => 'success',
            'warning', 'remedial', 'revision'  => 'warning',
            'danger', 'rejected'               => 'danger',
            default                            => 'info',
        };
    }

    private function formatDateTime(string $value): string
    {
        if ($value === '') return '-';
        $time = strtotime($value);
        return $time !== false ? date('d M Y H:i', $time) : $value;
    }

        // ═══════════════════════════════════════════════════════════════════════
    //  PRAKTIKUM PAGE SPECIFIC
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Get detailed class rows for Praktikum page
     * Includes additional info like group, enrollment date, etc.
     */
    public function getPracticumRows(string $userNim): array
    {
        $student = $this->resolveStudentProfile($userNim, '');
        return $this->loadClassRowsDetailed($student);
    }

    /**
     * Load class rows with detailed info for Praktikum page
     */
    private function loadClassRowsDetailed(array $student): array
    {
        $db = $this->db;
        $userNim = $student['user_id'];

        if ($userNim === '') {
            return [];
        }

        // Get enrollment info from class_students (TIDAK punya deleted_at)
        $enrollments = $db->table('class_students cs')
            ->select([
                'cs.practicum_class_id',
                'cs.group_id',
                'cs.enrollment_status',
                'cs.enrolled_at',
                'pg.group_name',
                'pg.group_code',
            ])
            ->join('practicum_groups pg', 'pg.id = cs.group_id', 'left')
            ->where('cs.student_nim', $userNim)
            ->get()
            ->getResultArray();

        if (empty($enrollments)) {
            return [];
        }

        $classIdList = array_values(array_filter(array_map(
            static fn(array $row): int => (int) ($row['practicum_class_id'] ?? 0),
            $enrollments
        )));

        $enrollmentMap = [];
        foreach ($enrollments as $e) {
            $enrollmentMap[(int) ($e['practicum_class_id'] ?? 0)] = $e;
        }

        // Build class details with all joins
        $builder = $db->table('practicum_classes pc');
        $builder->select([
            'pc.id',
            'pc.course_id',
            'pc.class_code',
            'pc.class_name',
            'pc.status',
            'pc.academic_year_id',
            'pc.semester_id',
            'pc.deadline_at',
            'pc.description',
            'mk.kode_mk as course_code',
            'mk.nama_mk as course_name',
            'mk.sks as credits',
        ]);

        // Mata kuliah (TIDAK punya deleted_at)
        $builder->join('mata_kuliah mk', 'mk.id = pc.course_id', 'left');

        // Academic year (TIDAK punya deleted_at)
        $builder->select('ay.year_code as academic_year_label');
        $builder->join('academic_years ay', 'ay.id = pc.academic_year_id', 'left');

        // Semester (TIDAK punya deleted_at)
        $builder->select('s.semester_name as semester_label, s.semester_number');
        $builder->join('semesters s', 's.id = pc.semester_id', 'left');

        // Laboratory (TIDAK punya deleted_at)
        $builder->select('l.room_name as laboratory_name, l.room_code as laboratory_code');
        $builder->join('laboratories l', 'l.id = pc.laboratory_id', 'left');

        // Template (PUNYA deleted_at)
        $builder->select('at.template_name, at.template_code');
        $builder->join('assessment_templates at', 'at.id = pc.template_id AND at.deleted_at IS NULL', 'left');

        // Main lecturer
        $builder->select('u_lect.full_name as lecturer_name');
        $builder->join('class_lecturers cl', 'cl.practicum_class_id = pc.id AND cl.role_type = \'pengampu\'', 'left');
        $builder->join('users u_lect', 'u_lect.id = cl.lecturer_id', 'left');

        // Main assistant
        $builder->select('u_asst.full_name as assistant_name');
        $builder->join('class_assistants ca', 'ca.practicum_class_id = pc.id AND ca.is_main = 1', 'left');
        $builder->join('users u_asst', 'u_asst.id = ca.assistant_id', 'left');

        // Coordinator
        $builder->select('u_coor.full_name as coordinator_name');
        $builder->join('class_lecturers cl2', 'cl2.practicum_class_id = pc.id AND cl2.role_type = \'koordinator\'', 'left');
        $builder->join('users u_coor', 'u_coor.id = cl2.lecturer_id', 'left');

        $builder->where('pc.deleted_at', null);
        $builder->whereIn('pc.id', $classIdList);
        $builder->orderBy('mk.nama_mk', 'ASC');

        $rows = $builder->get()->getResultArray();

        $prepared = [];
        foreach ($rows as $row) {
            $classId = (int) ($row['id'] ?? 0);
            $enroll = $enrollmentMap[$classId] ?? [];
            $status = $this->normalizeClassStatus((string) ($row['status'] ?? 'aktif'));

            $prepared[] = [
                'id'                => $classId,
                'course_id'         => (int) ($row['course_id'] ?? 0),
                'course_name'       => (string) ($row['course_name'] ?? '-'),
                'course_code'       => (string) ($row['course_code'] ?? '-'),
                'credits'           => (int) ($row['credits'] ?? 0),
                'class_name'        => (string) ($row['class_name'] ?? $row['class_code'] ?? '-'),
                'class_code'        => (string) ($row['class_code'] ?? '-'),
                'lecturer_name'     => (string) ($row['lecturer_name'] ?? '-'),
                'assistant_name'    => (string) ($row['assistant_name'] ?? '-'),
                'coordinator_name'  => (string) ($row['coordinator_name'] ?? '-'),
                'academic_year'     => (string) ($row['academic_year_label'] ?? '-'),
                'semester_label'    => (string) ($row['semester_label'] ?? '-'),
                'semester_number'   => (int) ($row['semester_number'] ?? 1),
                'laboratory_name'   => (string) ($row['laboratory_name'] ?? '-'),
                'template_name'     => (string) ($row['template_name'] ?? '-'),
                'status'            => $status,
                'status_badge'      => self::CLASS_STATUS_BADGES[$status] ?? 'secondary',
                'deadline_at'       => (string) ($row['deadline_at'] ?? '-'),
                'description'       => (string) ($row['description'] ?? '-'),
                // Enrollment-specific
                'group_name'        => (string) ($enroll['group_name'] ?? '-'),
                'group_code'        => (string) ($enroll['group_code'] ?? '-'),
                'enrollment_status' => (string) ($enroll['enrollment_status'] ?? 'aktif'),
                'enrolled_at'       => (string) ($enroll['enrolled_at'] ?? '-'),
                'detail_url'        => site_url('mahasiswa/praktikum/' . $classId . '/detail'),
            ];
        }

        return $prepared;
    }
}
