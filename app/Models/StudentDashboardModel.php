<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentDashboardModel extends Model
{
    protected $DBGroup = 'default';

    private const SCORE_STATUS_BADGES = [
        'Draft' => 'secondary',
        'Submitted' => 'info',
        'Reviewed' => 'primary',
        'Validated' => 'success',
        'Locked' => 'success',
        'Revision Requested' => 'warning',
    ];

    private const ACADEMIC_STATUS_BADGES = [
        'Lulus' => 'success',
        'Remedial' => 'warning',
        'Tidak Lulus' => 'danger',
        'Belum Lengkap' => 'secondary',
    ];

    private const CLASS_STATUS_BADGES = [
        'Aktif' => 'success',
        'Selesai' => 'primary',
        'Terkunci' => 'dark',
        'Diarsipkan' => 'secondary',
    ];

    private const ATTENDANCE_STATUS_BADGES = [
        'Aman' => 'success',
        'Perlu Perhatian' => 'warning',
        'Kurang Kehadiran' => 'danger',
        'Belum Ada Pertemuan' => 'secondary',
    ];

    private const REMEDIAL_STATUS_BADGES = [
        'Eligible' => 'warning',
        'Terdaftar' => 'info',
        'Dijadwalkan' => 'primary',
        'Sudah Dinilai' => 'success',
        'Validated' => 'success',
        'Tidak Mengikuti' => 'secondary',
        'Dibatalkan' => 'secondary',
    ];

    public function buildDashboardData(string $userId, string $displayName): array
    {
        $academic = $this->resolveAcademicContext();
        $student = $this->resolveStudentProfile($userId, $displayName);

        $classRows = $this->loadClassRows($student, $academic);
        $attendanceRows = $this->loadAttendanceSummaryRows($classRows, $student);
        $scoreRows = $this->loadScoreSummaryRows($classRows, $student);
        $remedialRows = $this->loadRemedialRows($classRows, $student);
        $notifications = $this->loadNotifications($student, $classRows);

        $summary = $this->buildSummary($classRows, $attendanceRows, $scoreRows, $remedialRows);

        return [
            'title' => 'Dashboard Mahasiswa',
            'studentProfile' => $student,
            'academicYear' => $academic['academic_year'],
            'semesterLabel' => $academic['semester_label'],
            'summaryCards' => $summary['cards'],
            'summaryMeta' => $summary['meta'],
            'classRows' => $classRows,
            'attendanceRows' => $attendanceRows,
            'scoreRows' => $scoreRows,
            'remedialRows' => $remedialRows,
            'notifications' => $notifications,
            'hasClasses' => $classRows !== [],
        ];
    }

    public function buildDetailData(string $userId, int $classId): array
    {
        $academic = $this->resolveAcademicContext();
        $student = $this->resolveStudentProfile($userId, '');
        $classInfo = $this->loadClassInfo($classId, $academic);
        $finalScore = $this->loadFinalScoreForStudent($classId, $student);
        $componentRows = $this->loadComponentRows($classId, $student);
        $attendanceRows = $this->loadAttendanceDetailRows($classId, $student);
        $remedialRows = $this->loadRemedialRows([['id' => $classId]], $student, true);

        return [
            'title' => 'Detail Praktikum',
            'studentProfile' => $student,
            'classInfo' => $classInfo,
            'finalScore' => $finalScore,
            'componentRows' => $componentRows,
            'attendanceRows' => $attendanceRows,
            'remedialRows' => $remedialRows,
            'backUrl' => site_url('mahasiswa/dashboard'),
        ];
    }

    public function getStudentClassIds(string $userId): array
    {
        $student = $this->resolveStudentProfile($userId, '');

        return $this->loadStudentClassIds($student);
    }

    private function resolveAcademicContext(): array
    {
        $month = (int) date('n');
        $academicYear = $month >= 7
            ? date('Y') . '/' . (date('Y') + 1)
            : (date('Y') - 1) . '/' . date('Y');

        return [
            'academic_year' => $academicYear,
            'semester_label' => $month >= 7 ? 'Semester Ganjil' : 'Semester Genap',
        ];
    }

    private function resolveStudentProfile(string $userId, string $displayName): array
    {
        $db = db_connect();
        $profile = [
            'student_id' => $userId,
            'user_id' => $userId,
            'student_number' => '-',
            'full_name' => $displayName !== '' ? $displayName : 'Mahasiswa',
            'study_program_id' => null,
            'study_program' => '-',
            'semester_active' => '',
            'academic_year_active' => '',
        ];

        if ($userId === '') {
            return $profile;
        }

        $userRow = null;
        if ($db->tableExists('users')) {
            $userRow = $db->table('users')->where('id', $userId)->get()->getRowArray();
        }

        if ($userRow !== null) {
            $profile['student_number'] = (string) ($userRow['student_number'] ?? $userRow['nim'] ?? $profile['student_number']);
            $profile['full_name'] = (string) ($userRow['full_name'] ?? $userRow['name'] ?? $profile['full_name']);
            $profile['study_program_id'] = $userRow['study_program_id'] ?? $profile['study_program_id'];
            $profile['semester_active'] = (string) ($userRow['semester_active'] ?? $userRow['semester'] ?? $profile['semester_active']);
            $profile['academic_year_active'] = (string) ($userRow['academic_year'] ?? $profile['academic_year_active']);
        }

        foreach (['students', 'mahasiswa'] as $table) {
            if (! $db->tableExists($table)) {
                continue;
            }

            $keyField = $db->fieldExists('user_id', $table) ? 'user_id' : 'id';
            $studentRow = $db->table($table)->where($keyField, $userId)->get()->getRowArray();

            if ($studentRow === null) {
                continue;
            }

            $profile['student_id'] = (int) ($studentRow['id'] ?? $profile['student_id']);
            $profile['student_number'] = (string) ($studentRow['student_number'] ?? $studentRow['nim'] ?? $profile['student_number']);
            $profile['full_name'] = (string) ($studentRow['full_name'] ?? $studentRow['name'] ?? $profile['full_name']);
            $profile['study_program_id'] = $studentRow['study_program_id'] ?? $profile['study_program_id'];
            $profile['semester_active'] = (string) ($studentRow['semester_active'] ?? $studentRow['semester'] ?? $profile['semester_active']);
            $profile['academic_year_active'] = (string) ($studentRow['academic_year'] ?? $profile['academic_year_active']);
            break;
        }

        $profile['study_program'] = $this->resolveStudyProgramLabel($profile['study_program_id']);

        return $profile;
    }

    private function resolveStudyProgramLabel(?int $studyProgramId): string
    {
        if ($studyProgramId === null || $studyProgramId <= 0) {
            return '-';
        }

        $db = db_connect();
        $candidates = [
            ['study_programs', ['name', 'study_program_name', 'program_name', 'label', 'nama']],
            ['program_studi', ['name', 'program_name', 'label', 'nama']],
        ];

        foreach ($candidates as [$table, $fields]) {
            if (! $db->tableExists($table)) {
                continue;
            }

            $labelField = $this->findFirstField($table, $fields);

            if ($labelField === null) {
                continue;
            }

            $row = $db->table($table)
                ->select('id, ' . $labelField)
                ->where('id', $studyProgramId)
                ->get()
                ->getRowArray();

            if ($row !== null) {
                return (string) ($row[$labelField] ?? '-');
            }
        }

        return '-';
    }

    private function loadStudentClassIds(array $student): array
    {
        $db = db_connect();
        $classIds = [];

        $baseUserId = (string) ($student['user_id'] ?? '');
        $baseStudentId = (int) ($student['student_id'] ?? 0);

        if ($baseUserId === '' && $baseStudentId <= 0) {
            return [];
        }

        if ($db->tableExists('class_students')) {
            $studentField = $this->resolveStudentField('class_students');
            $studentId = $this->resolveStudentIdentifier($student, $studentField);
            if ($this->isEmptyIdentifier($studentId, $studentField)) {
                return [];
            }
            $rows = $db->table('class_students')
                ->select('class_id')
                ->where($studentField, $studentId)
                ->get()
                ->getResultArray();

            $classIds = array_merge($classIds, array_map(static fn (array $row): int => (int) ($row['class_id'] ?? 0), $rows));
        }

        if ($classIds === [] && $db->tableExists('final_scores')) {
            $studentField = $this->resolveStudentField('final_scores');
            $studentId = $this->resolveStudentIdentifier($student, $studentField);
            if ($this->isEmptyIdentifier($studentId, $studentField)) {
                return [];
            }
            $rows = $db->table('final_scores')
                ->select('class_id')
                ->where($studentField, $studentId)
                ->get()
                ->getResultArray();

            $classIds = array_merge($classIds, array_map(static fn (array $row): int => (int) ($row['class_id'] ?? 0), $rows));
        }

        if ($classIds === [] && $db->tableExists('score_entries')) {
            $studentField = $this->resolveStudentField('score_entries');
            $studentId = $this->resolveStudentIdentifier($student, $studentField);
            if ($this->isEmptyIdentifier($studentId, $studentField)) {
                return [];
            }
            $rows = $db->table('score_entries')
                ->select('class_id')
                ->where($studentField, $studentId)
                ->get()
                ->getResultArray();

            $classIds = array_merge($classIds, array_map(static fn (array $row): int => (int) ($row['class_id'] ?? 0), $rows));
        }

        $classIds = array_values(array_unique(array_filter($classIds)));

        return $classIds;
    }

    private function loadClassRows(array $student, array $academic, ?int $onlyClassId = null): array
    {
        $db = db_connect();
        $classIds = $this->loadStudentClassIds($student);

        if ($onlyClassId !== null) {
            $classIds = array_values(array_filter($classIds, static fn (int $id): bool => $id === $onlyClassId));
        }

        if ($classIds === [] || ! $db->tableExists('practicum_classes') || ! $db->tableExists('courses')) {
            return [];
        }

        $builder = $db->table('practicum_classes pc');
        $builder->select([
            'pc.id',
            'pc.course_id',
            'pc.class_code',
            'pc.class_name',
            'pc.status',
            'pc.lecturer_id',
            'pc.assistant_id',
            'pc.academic_year_id',
            'pc.semester_id',
            'c.course_code',
            'c.course_name',
        ]);
        $builder->join('courses c', 'c.id = pc.course_id', 'left');

        if ($db->tableExists('lecturers')) {
            $builder->select('l.lecturer_name');
            $builder->join('lecturers l', 'l.id = pc.lecturer_id', 'left');
        }

        if ($db->tableExists('assistants')) {
            $builder->select('a.assistant_name');
            $builder->join('assistants a', 'a.id = pc.assistant_id', 'left');
        }

        if ($db->tableExists('academic_years')) {
            $yearField = $this->findFirstField('academic_years', ['academic_year', 'year_label', 'name', 'label']);
            if ($yearField !== null) {
                $builder->select('ay.' . $yearField . ' AS academic_year_label');
                $builder->join('academic_years ay', 'ay.id = pc.academic_year_id', 'left');
            }
        }

        if ($db->tableExists('semesters')) {
            $semesterField = $this->findFirstField('semesters', ['semester_name', 'name', 'label', 'semester']);
            if ($semesterField !== null) {
                $builder->select('s.' . $semesterField . ' AS semester_label');
                $builder->join('semesters s', 's.id = pc.semester_id', 'left');
            }
        }

        $rows = $builder->whereIn('pc.id', $classIds)
            ->orderBy('c.course_name', 'ASC')
            ->get()
            ->getResultArray();

        if ($rows === []) {
            return [];
        }

        $prepared = [];

        foreach ($rows as $row) {
            $classId = (int) ($row['id'] ?? 0);
            $status = $this->normalizeClassStatus((string) ($row['status'] ?? 'aktif'));
            $academicYear = (string) ($row['academic_year_label'] ?? $academic['academic_year']);
            $semesterLabel = $this->normalizeSemesterLabel((string) ($row['semester_label'] ?? ''), $academic);

            $prepared[] = [
                'id' => $classId,
                'course_id' => (int) ($row['course_id'] ?? 0),
                'course_name' => (string) ($row['course_name'] ?? '-'),
                'course_code' => (string) ($row['course_code'] ?? '-'),
                'class_name' => (string) ($row['class_name'] ?? $row['class_code'] ?? '-'),
                'lecturer_name' => (string) ($row['lecturer_name'] ?? '-'),
                'assistant_name' => (string) ($row['assistant_name'] ?? '-'),
                'academic_year' => $academicYear,
                'semester_label' => $semesterLabel,
                'status' => $status,
                'status_badge' => self::CLASS_STATUS_BADGES[$status] ?? 'secondary',
                'detail_url' => site_url('mahasiswa/praktikum/' . $classId . '/detail'),
            ];
        }

        return $prepared;
    }

    private function loadClassInfo(int $classId, array $academic): array
    {
        $db = db_connect();

        if (! $db->tableExists('practicum_classes') || ! $db->tableExists('courses')) {
            return [
                'id' => $classId,
                'course_name' => '-',
                'course_code' => '-',
                'class_name' => '-',
                'lecturer_name' => '-',
                'assistant_name' => '-',
                'academic_year' => $academic['academic_year'],
                'semester_label' => $academic['semester_label'],
                'status' => 'Aktif',
                'status_badge' => self::CLASS_STATUS_BADGES['Aktif'],
                'detail_url' => site_url('mahasiswa/praktikum/' . $classId . '/detail'),
            ];
        }

        $builder = $db->table('practicum_classes pc');
        $builder->select([
            'pc.id',
            'pc.course_id',
            'pc.class_code',
            'pc.class_name',
            'pc.status',
            'pc.lecturer_id',
            'pc.assistant_id',
            'pc.academic_year_id',
            'pc.semester_id',
            'c.course_code',
            'c.course_name',
        ]);
        $builder->join('courses c', 'c.id = pc.course_id', 'left');

        if ($db->tableExists('lecturers')) {
            $builder->select('l.lecturer_name');
            $builder->join('lecturers l', 'l.id = pc.lecturer_id', 'left');
        }

        if ($db->tableExists('assistants')) {
            $builder->select('a.assistant_name');
            $builder->join('assistants a', 'a.id = pc.assistant_id', 'left');
        }

        if ($db->tableExists('academic_years')) {
            $yearField = $this->findFirstField('academic_years', ['academic_year', 'year_label', 'name', 'label']);
            if ($yearField !== null) {
                $builder->select('ay.' . $yearField . ' AS academic_year_label');
                $builder->join('academic_years ay', 'ay.id = pc.academic_year_id', 'left');
            }
        }

        if ($db->tableExists('semesters')) {
            $semesterField = $this->findFirstField('semesters', ['semester_name', 'name', 'label', 'semester']);
            if ($semesterField !== null) {
                $builder->select('s.' . $semesterField . ' AS semester_label');
                $builder->join('semesters s', 's.id = pc.semester_id', 'left');
            }
        }

        $row = $builder->where('pc.id', $classId)->get()->getRowArray();

        if ($row === null) {
            return [
                'id' => $classId,
                'course_name' => '-',
                'course_code' => '-',
                'class_name' => '-',
                'lecturer_name' => '-',
                'assistant_name' => '-',
                'academic_year' => $academic['academic_year'],
                'semester_label' => $academic['semester_label'],
                'status' => 'Aktif',
                'status_badge' => self::CLASS_STATUS_BADGES['Aktif'],
                'detail_url' => site_url('mahasiswa/praktikum/' . $classId . '/detail'),
            ];
        }

        $status = $this->normalizeClassStatus((string) ($row['status'] ?? 'aktif'));
        $academicYear = (string) ($row['academic_year_label'] ?? $academic['academic_year']);
        $semesterLabel = $this->normalizeSemesterLabel((string) ($row['semester_label'] ?? ''), $academic);

        return [
            'id' => (int) ($row['id'] ?? $classId),
            'course_id' => (int) ($row['course_id'] ?? 0),
            'course_name' => (string) ($row['course_name'] ?? '-'),
            'course_code' => (string) ($row['course_code'] ?? '-'),
            'class_name' => (string) ($row['class_name'] ?? $row['class_code'] ?? '-'),
            'lecturer_name' => (string) ($row['lecturer_name'] ?? '-'),
            'assistant_name' => (string) ($row['assistant_name'] ?? '-'),
            'academic_year' => $academicYear,
            'semester_label' => $semesterLabel,
            'status' => $status,
            'status_badge' => self::CLASS_STATUS_BADGES[$status] ?? 'secondary',
            'detail_url' => site_url('mahasiswa/praktikum/' . $classId . '/detail'),
        ];
    }

    private function loadAttendanceSummaryRows(array $classRows, array $student): array
    {
        $db = db_connect();

        if ($classRows === [] || ! $db->tableExists('attendance_sessions') || ! $db->tableExists('attendance_records')) {
            return [];
        }

        $studentField = $this->resolveStudentField('attendance_records');
        $studentId = $this->resolveStudentIdentifier($student, $studentField);
        if ($this->isEmptyIdentifier($studentId, $studentField)) {
            return [];
        }
        $rows = [];

        foreach ($classRows as $classRow) {
            $classId = (int) ($classRow['id'] ?? 0);

            $totalSessions = (int) $db->table('attendance_sessions')
                ->where('class_id', $classId)
                ->countAllResults();

            $records = $db->table('attendance_records ar')
                ->select('ar.status')
                ->join('attendance_sessions s', 's.id = ar.session_id', 'left')
                ->where('s.class_id', $classId)
                ->where('ar.' . $studentField, $studentId)
                ->get()
                ->getResultArray();

            $counts = [
                'hadir' => 0,
                'izin' => 0,
                'sakit' => 0,
                'alfa' => 0,
                'susulan' => 0,
            ];

            foreach ($records as $record) {
                $bucket = $this->normalizeAttendanceStatus((string) ($record['status'] ?? ''));
                if (isset($counts[$bucket])) {
                    $counts[$bucket]++;
                } else {
                    $counts['alfa']++;
                }
            }

            $presentTotal = $counts['hadir'] + $counts['izin'] + $counts['sakit'] + $counts['susulan'];
            $percentage = $this->percentage($presentTotal, $totalSessions);
            $status = $this->attendanceStatusLabel($percentage, $totalSessions);

            $rows[] = [
                'class_id' => $classId,
                'course_name' => (string) ($classRow['course_name'] ?? '-'),
                'class_name' => (string) ($classRow['class_name'] ?? '-'),
                'total_sessions' => $totalSessions,
                'hadir' => $counts['hadir'],
                'izin' => $counts['izin'],
                'sakit' => $counts['sakit'],
                'alfa' => $counts['alfa'],
                'susulan' => $counts['susulan'],
                'attendance_percentage' => $percentage,
                'status' => $status,
                'status_badge' => self::ATTENDANCE_STATUS_BADGES[$status] ?? 'secondary',
            ];
        }

        return $rows;
    }

    private function loadAttendanceDetailRows(int $classId, array $student): array
    {
        $db = db_connect();

        if (! $db->tableExists('attendance_sessions') || ! $db->tableExists('attendance_records')) {
            return [];
        }

        $studentField = $this->resolveStudentField('attendance_records');
        $studentId = $this->resolveStudentIdentifier($student, $studentField);
        if ($this->isEmptyIdentifier($studentId, $studentField)) {
            return [];
        }
        $joinCondition = 'ar.session_id = s.id AND ar.' . $studentField . ' = ' . $this->formatIdentifierForSql($studentId, $studentField);

        $rows = $db->table('attendance_sessions s')
            ->select('s.id, s.meeting_no, s.session_date, ar.status, ar.notes')
            ->join('attendance_records ar', $joinCondition, 'left', false)
            ->where('s.class_id', $classId)
            ->orderBy('s.meeting_no', 'ASC')
            ->get()
            ->getResultArray();

        $prepared = [];

        foreach ($rows as $row) {
            $prepared[] = [
                'meeting_no' => (string) ($row['meeting_no'] ?? '-'),
                'session_date' => (string) ($row['session_date'] ?? '-'),
                'status' => $this->formatAttendanceLabel((string) ($row['status'] ?? 'Belum Diabsen')),
                'notes' => (string) ($row['notes'] ?? '-'),
            ];
        }

        return $prepared;
    }

    private function loadScoreSummaryRows(array $classRows, array $student): array
    {
        if ($classRows === []) {
            return [];
        }

        $classIds = array_values(array_map(static fn (array $row): int => (int) ($row['id'] ?? 0), $classRows));
        $scoreStats = $this->loadScoreStats($classIds, $student);
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

            $scoreStatus = $this->normalizeScoreStatus((string) ($finalRow['validation_status'] ?? $finalRow['status'] ?? ''));
            if ($scoreStatus === 'Draft' && $stat['filled'] > 0) {
                $scoreStatus = $stat['filled'] >= $stat['total'] && $stat['total'] > 0 ? 'Reviewed' : 'Submitted';
            }

            $academicStatus = $this->resolveAcademicStatus(
                $finalRow['final_score'] ?? null,
                (string) ($finalRow['grade_letter'] ?? ''),
                (string) ($remedialRow['status'] ?? '')
            );

            $rows[] = [
                'class_id' => $classId,
                'course_name' => (string) ($classRow['course_name'] ?? '-'),
                'final_score' => isset($finalRow['final_score']) ? (float) $finalRow['final_score'] : null,
                'grade_letter' => (string) ($finalRow['grade_letter'] ?? '-'),
                'score_status' => $scoreStatus,
                'score_status_badge' => self::SCORE_STATUS_BADGES[$scoreStatus] ?? 'secondary',
                'academic_status' => $academicStatus,
                'academic_badge' => self::ACADEMIC_STATUS_BADGES[$academicStatus] ?? 'secondary',
                'score_progress' => $progress,
                'missing_count' => (int) ($stat['missing'] ?? 0),
                'detail_url' => site_url('mahasiswa/praktikum/' . $classId . '/detail'),
            ];
        }

        return $rows;
    }

    private function loadScoreStats(array $classIds, array $student): array
    {
        $db = db_connect();

        if ($classIds === [] || ! $db->tableExists('score_entries')) {
            return [];
        }

        $studentField = $this->resolveStudentField('score_entries');
        $studentId = $this->resolveStudentIdentifier($student, $studentField);
        if ($this->isEmptyIdentifier($studentId, $studentField)) {
            return [];
        }

        $rows = $db->table('score_entries')
            ->select('class_id')
            ->select('SUM(CASE WHEN score_value IS NOT NULL THEN 1 ELSE 0 END) AS filled_count', false)
            ->select('SUM(CASE WHEN score_value IS NULL THEN 1 ELSE 0 END) AS missing_count', false)
            ->select('COUNT(*) AS total_count', false)
            ->whereIn('class_id', $classIds)
            ->where($studentField, $studentId)
            ->groupBy('class_id')
            ->get()
            ->getResultArray();

        $stats = [];

        foreach ($rows as $row) {
            $classId = (int) ($row['class_id'] ?? 0);
            $stats[$classId] = [
                'filled' => (int) ($row['filled_count'] ?? 0),
                'missing' => (int) ($row['missing_count'] ?? 0),
                'total' => (int) ($row['total_count'] ?? 0),
            ];
        }

        return $stats;
    }

    private function loadFinalScores(array $classIds, array $student): array
    {
        $db = db_connect();

        if ($classIds === [] || ! $db->tableExists('final_scores')) {
            return [];
        }

        $studentField = $this->resolveStudentField('final_scores');
        $studentId = $this->resolveStudentIdentifier($student, $studentField);
        if ($this->isEmptyIdentifier($studentId, $studentField)) {
            return [
                'final_score' => null,
                'grade_letter' => '-',
                'status' => 'Draft',
                'status_badge' => self::SCORE_STATUS_BADGES['Draft'],
                'academic_status' => 'Belum Lengkap',
                'academic_badge' => self::ACADEMIC_STATUS_BADGES['Belum Lengkap'],
                'notes' => '-',
            ];
        }

        $rows = $db->table('final_scores')
            ->select('class_id, final_score, grade_letter, status, validation_status, notes')
            ->whereIn('class_id', $classIds)
            ->where($studentField, $studentId)
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
        $db = db_connect();

        if (! $db->tableExists('final_scores')) {
            return [
                'final_score' => null,
                'grade_letter' => '-',
                'status' => 'Draft',
                'status_badge' => self::SCORE_STATUS_BADGES['Draft'],
                'academic_status' => 'Belum Lengkap',
                'academic_badge' => self::ACADEMIC_STATUS_BADGES['Belum Lengkap'],
                'notes' => '-',
            ];
        }

        $studentField = $this->resolveStudentField('final_scores');
        $studentId = $this->resolveStudentIdentifier($student, $studentField);
        if ($this->isEmptyIdentifier($studentId, $studentField)) {
            return [
                'final_score' => null,
                'grade_letter' => '-',
                'status' => 'Draft',
                'status_badge' => self::SCORE_STATUS_BADGES['Draft'],
                'academic_status' => 'Belum Lengkap',
                'academic_badge' => self::ACADEMIC_STATUS_BADGES['Belum Lengkap'],
                'notes' => '-',
            ];
        }

        $row = $db->table('final_scores')
            ->select('final_score, grade_letter, status, validation_status, notes')
            ->where('class_id', $classId)
            ->where($studentField, $studentId)
            ->get()
            ->getRowArray();

        $scoreStatus = $this->normalizeScoreStatus((string) ($row['validation_status'] ?? $row['status'] ?? ''));
        $academicStatus = $this->resolveAcademicStatus(
            $row['final_score'] ?? null,
            (string) ($row['grade_letter'] ?? ''),
            ''
        );

        return [
            'final_score' => isset($row['final_score']) ? (float) $row['final_score'] : null,
            'grade_letter' => (string) ($row['grade_letter'] ?? '-'),
            'status' => $scoreStatus,
            'status_badge' => self::SCORE_STATUS_BADGES[$scoreStatus] ?? 'secondary',
            'academic_status' => $academicStatus,
            'academic_badge' => self::ACADEMIC_STATUS_BADGES[$academicStatus] ?? 'secondary',
            'notes' => (string) ($row['notes'] ?? '-'),
        ];
    }

    private function loadRemedialMap(array $classIds, array $student): array
    {
        $db = db_connect();

        if ($classIds === [] || ! $db->tableExists('remedial_participants')) {
            return [];
        }

        $studentField = $this->resolveStudentField('remedial_participants');
        $studentId = $this->resolveStudentIdentifier($student, $studentField);
        if ($this->isEmptyIdentifier($studentId, $studentField)) {
            return [];
        }

        $rows = $db->table('remedial_participants')
            ->select('class_id, status, reason')
            ->whereIn('class_id', $classIds)
            ->where($studentField, $studentId)
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
        $db = db_connect();

        if ($classRows === [] || ! $db->tableExists('remedial_participants')) {
            return [];
        }

        $classIds = array_values(array_map(static fn (array $row): int => (int) ($row['id'] ?? 0), $classRows));
        $studentField = $this->resolveStudentField('remedial_participants');
        $studentId = $this->resolveStudentIdentifier($student, $studentField);
        if ($this->isEmptyIdentifier($studentId, $studentField)) {
            return [];
        }

        $builder = $db->table('remedial_participants rp');
        $builder->select([
            'rp.class_id',
            'rp.status',
            'rp.reason',
            'rp.remedial_date',
            'rp.remedial_period_id',
        ]);

        $typeField = $this->findFirstField('remedial_participants', ['type', 'remedial_type']);
        $notesField = $this->findFirstField('remedial_participants', ['notes', 'catatan']);

        if ($typeField !== null) {
            $builder->select('rp.' . $typeField . ' AS remedial_type');
        }

        if ($notesField !== null) {
            $builder->select('rp.' . $notesField . ' AS notes');
        }

        if ($db->tableExists('practicum_classes')) {
            $builder->select('pc.class_name');
            $builder->join('practicum_classes pc', 'pc.id = rp.class_id', 'left');

            if ($db->tableExists('courses')) {
                $builder->select('c.course_name, c.course_code');
                $builder->join('courses c', 'c.id = pc.course_id', 'left');
            }
        }

        if ($db->tableExists('remedial_periods')) {
            $periodNameField = $this->findFirstField('remedial_periods', ['period_name', 'name', 'label']);
            $periodStartField = $this->findFirstField('remedial_periods', ['start_date', 'start_at']);
            $periodEndField = $this->findFirstField('remedial_periods', ['end_date', 'end_at']);

            if ($periodNameField !== null) {
                $builder->select('rp2.' . $periodNameField . ' AS period_name');
            }
            if ($periodStartField !== null) {
                $builder->select('rp2.' . $periodStartField . ' AS period_start');
            }
            if ($periodEndField !== null) {
                $builder->select('rp2.' . $periodEndField . ' AS period_end');
            }

            $builder->join('remedial_periods rp2', 'rp2.id = rp.remedial_period_id', 'left');
        }

        if ($db->tableExists('final_scores')) {
            $builder->select('fs.final_score AS score_before, fs.grade_letter AS grade_before');
            $builder->join('final_scores fs', 'fs.class_id = rp.class_id AND fs.' . $this->resolveStudentField('final_scores') . ' = rp.' . $studentField, 'left', false);
        }

        if ($db->tableExists('remedial_scores')) {
            $scoreAfterField = $this->findFirstField('remedial_scores', ['score_after', 'new_score', 'score_value', 'final_score']);
            $studentFieldRemedial = $this->resolveStudentField('remedial_scores');
            if ($scoreAfterField !== null && $this->fieldExists('class_id', 'remedial_scores') && $this->fieldExists($studentFieldRemedial, 'remedial_scores')) {
                $builder->select('rs.' . $scoreAfterField . ' AS score_after');
                $builder->join('remedial_scores rs', 'rs.class_id = rp.class_id AND rs.' . $studentFieldRemedial . ' = rp.' . $studentField, 'left', false);
            }
        }

        $builder->whereIn('rp.class_id', $classIds);
        $builder->where('rp.' . $studentField, $studentId);
        $rows = $builder->orderBy('rp.remedial_date', 'DESC')->get()->getResultArray();

        $prepared = [];

        foreach ($rows as $row) {
            $status = $this->normalizeRemedialStatus((string) ($row['status'] ?? 'Eligible'));
            $periodLabel = (string) ($row['period_name'] ?? '');
            if ($periodLabel === '' && ($row['period_start'] ?? '') !== '') {
                $periodLabel = (string) ($row['period_start'] ?? '-') . ' - ' . (string) ($row['period_end'] ?? '-');
            }

            $prepared[] = [
                'class_id' => (int) ($row['class_id'] ?? 0),
                'course_name' => (string) ($row['course_name'] ?? '-'),
                'course_code' => (string) ($row['course_code'] ?? '-'),
                'class_name' => (string) ($row['class_name'] ?? '-'),
                'reason' => (string) ($row['reason'] ?? 'Memenuhi kriteria remedial'),
                'remedial_type' => (string) ($row['remedial_type'] ?? 'Remedial Praktikum'),
                'component_label' => (string) ($row['component_label'] ?? 'Nilai Akhir'),
                'schedule' => (string) ($row['remedial_date'] ?? $periodLabel ?: '-'),
                'status' => $status,
                'status_badge' => self::REMEDIAL_STATUS_BADGES[$status] ?? 'warning',
                'score_before' => isset($row['score_before']) ? (float) $row['score_before'] : null,
                'score_after' => isset($row['score_after']) ? (float) $row['score_after'] : null,
                'grade_before' => (string) ($row['grade_before'] ?? '-'),
                'notes' => (string) ($row['notes'] ?? '-'),
            ];
        }

        return $forDetail ? $prepared : $prepared;
    }

    private function loadNotifications(array $student, array $classRows): array
    {
        $db = db_connect();
        $rows = [];

        if ($db->tableExists('notifications')) {
            $studentField = $this->resolveStudentField('notifications');
            if (! $this->fieldExists($studentField, 'notifications')) {
                return [];
            }
            $studentId = $this->resolveStudentIdentifier($student, $studentField);
            if ($this->isEmptyIdentifier($studentId, $studentField)) {
                return [];
            }
            $rows = $db->table('notifications')
                ->select('title, message, type, created_at')
                ->where($studentField, $studentId)
                ->orderBy('created_at', 'DESC')
                ->limit(6)
                ->get()
                ->getResultArray();

            return array_map(function (array $row): array {
                $type = (string) ($row['type'] ?? 'info');

                return [
                    'title' => (string) ($row['title'] ?? 'Notifikasi'),
                    'message' => (string) ($row['message'] ?? '-'),
                    'time' => $this->formatDateTime((string) ($row['created_at'] ?? '')),
                    'badge' => $this->notificationBadge($type),
                ];
            }, $rows);
        }

        if ($db->tableExists('activity_logs')) {
            $userId = (string) ($student['user_id'] ?? '');
            if ($userId === '') {
                return [];
            }
            $rows = $db->table('activity_logs')
                ->select('activity, description, created_at')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit(6)
                ->get()
                ->getResultArray();

            return array_map(function (array $row): array {
                return [
                    'title' => (string) ($row['activity'] ?? 'Aktivitas'),
                    'message' => (string) ($row['description'] ?? '-'),
                    'time' => $this->formatDateTime((string) ($row['created_at'] ?? '')),
                    'badge' => 'secondary',
                ];
            }, $rows);
        }

        return [];
    }

    private function loadComponentRows(int $classId, array $student): array
    {
        $db = db_connect();

        if (! $db->tableExists('score_entries') || ! $this->fieldExists('component_id', 'score_entries')) {
            return [];
        }

        $studentField = $this->resolveStudentField('score_entries');
        $studentId = $this->resolveStudentIdentifier($student, $studentField);
        if ($this->isEmptyIdentifier($studentId, $studentField)) {
            return [];
        }
        $builder = $db->table('score_entries se');
        $builder->select('se.component_id, se.score_value, se.notes');
        $builder->where('se.class_id', $classId);
        $builder->where('se.' . $studentField, $studentId);

        $componentNameField = null;
        $componentWeightField = null;
        $subcomponentNameField = null;
        $subcomponentWeightField = null;

        if ($db->tableExists('assessment_components')) {
            $componentNameField = $this->findFirstField('assessment_components', ['component_name', 'name', 'label']);
            $componentWeightField = $this->findFirstField('assessment_components', ['weight', 'weight_percentage', 'percentage', 'bobot']);

            if ($componentNameField !== null) {
                $builder->select('ac.' . $componentNameField . ' AS component_name');
            }
            if ($componentWeightField !== null) {
                $builder->select('ac.' . $componentWeightField . ' AS component_weight');
            }

            $builder->join('assessment_components ac', 'ac.id = se.component_id', 'left');
        }

        $hasSubcomponent = $this->fieldExists('subcomponent_id', 'score_entries');

        if ($hasSubcomponent) {
            $builder->select('se.subcomponent_id');
        }

        if ($hasSubcomponent && $db->tableExists('assessment_subcomponents')) {
            $subcomponentNameField = $this->findFirstField('assessment_subcomponents', ['subcomponent_name', 'name', 'label']);
            $subcomponentWeightField = $this->findFirstField('assessment_subcomponents', ['weight', 'weight_percentage', 'percentage', 'bobot']);

            if ($subcomponentNameField !== null) {
                $builder->select('asub.' . $subcomponentNameField . ' AS subcomponent_name');
            }
            if ($subcomponentWeightField !== null) {
                $builder->select('asub.' . $subcomponentWeightField . ' AS subcomponent_weight');
            }

            $builder->join('assessment_subcomponents asub', 'asub.id = se.subcomponent_id', 'left');
        }

        $rows = $builder->orderBy('se.component_id', 'ASC')->get()->getResultArray();

        $prepared = [];

        foreach ($rows as $row) {
            $scoreValue = $row['score_value'];
            $componentName = (string) ($row['component_name'] ?? 'Komponen #' . (string) ($row['component_id'] ?? '-'));
            $subcomponentName = (string) ($row['subcomponent_name'] ?? '-');
            $weightValue = $row['subcomponent_weight'] ?? $row['component_weight'] ?? null;
            $weightValue = is_numeric($weightValue) ? (float) $weightValue : null;
            $weightedScore = null;

            if ($weightValue !== null && $scoreValue !== null && is_numeric($scoreValue)) {
                $weightedScore = ((float) $scoreValue) * ($weightValue / 100);
            }

            $prepared[] = [
                'component_name' => $componentName,
                'subcomponent_name' => $subcomponentName,
                'weight' => $weightValue,
                'score_value' => $scoreValue,
                'weighted_score' => $weightedScore,
                'notes' => (string) ($row['notes'] ?? '-'),
            ];
        }

        return $prepared;
    }

    private function buildSummary(array $classRows, array $attendanceRows, array $scoreRows, array $remedialRows): array
    {
        $totalClasses = count($classRows);
        $attendanceAverage = $this->averagePercent($attendanceRows, 'attendance_percentage');
        $scoreAverage = $this->averagePercent($scoreRows, 'score_progress');

        $passedCount = count(array_filter($scoreRows, static fn (array $row): bool => ($row['academic_status'] ?? '') === 'Lulus'));
        $remedialCount = count(array_filter($scoreRows, static fn (array $row): bool => ($row['academic_status'] ?? '') === 'Remedial'));
        $notFinalCount = count(array_filter($scoreRows, static fn (array $row): bool => ! in_array($row['score_status'] ?? '', ['Locked', 'Validated'], true)));
        $incompleteScores = count(array_filter($scoreRows, static fn (array $row): bool => (float) ($row['score_progress'] ?? 0) < 100));
        $missingEntries = array_sum(array_map(static fn (array $row): int => (int) ($row['missing_count'] ?? 0), $scoreRows));

        return [
            'cards' => [
                [
                    'title' => 'Praktikum Diikuti',
                    'value' => $totalClasses,
                    'color' => 'primary',
                    'icon' => 'bi-journal-check',
                    'description' => 'Total kelas praktikum aktif',
                ],
                [
                    'title' => 'Rata-rata Kehadiran',
                    'value' => $attendanceAverage . '%',
                    'color' => 'success',
                    'icon' => 'bi-calendar-check',
                    'description' => 'Rata-rata presensi',
                ],
                [
                    'title' => 'Praktikum Lulus',
                    'value' => $passedCount,
                    'color' => 'info',
                    'icon' => 'bi-award',
                    'description' => 'Status lulus saat ini',
                ],
                [
                    'title' => 'Praktikum Remedial',
                    'value' => $remedialCount,
                    'color' => 'warning',
                    'icon' => 'bi-arrow-repeat',
                    'description' => 'Perlu remedial',
                ],
                [
                    'title' => 'Belum Final',
                    'value' => $notFinalCount,
                    'color' => 'secondary',
                    'icon' => 'bi-hourglass-split',
                    'description' => 'Nilai belum terkunci',
                ],
                [
                    'title' => 'Nilai Kosong',
                    'value' => $incompleteScores,
                    'color' => 'danger',
                    'icon' => 'bi-clipboard-x',
                    'description' => $missingEntries > 0 ? $missingEntries . ' komponen belum dinilai' : 'Kelengkapan nilai',
                ],
            ],
            'meta' => [
                'total_classes' => $totalClasses,
                'attendance_average' => $attendanceAverage,
                'score_average' => $scoreAverage,
                'finalized' => max(0, $totalClasses - $notFinalCount),
                'not_final' => $notFinalCount,
            ],
        ];
    }

    private function normalizeClassStatus(string $status): string
    {
        $status = strtolower(trim($status));

        return match ($status) {
            'aktif', 'active' => 'Aktif',
            'selesai', 'completed', 'closed' => 'Selesai',
            'locked', 'terkunci' => 'Terkunci',
            'archived', 'diarsipkan' => 'Diarsipkan',
            default => 'Aktif',
        };
    }

    private function normalizeSemesterLabel(string $label, array $fallback): string
    {
        $label = trim($label);

        if ($label === '') {
            return $fallback['semester_label'];
        }

        $lower = strtolower($label);

        if (in_array($lower, ['ganjil', 'genap', 'pendek'], true)) {
            return 'Semester ' . ucfirst($lower);
        }

        return $label;
    }

    private function normalizeScoreStatus(string $status): string
    {
        $status = strtolower(trim($status));

        return match ($status) {
            'draft' => 'Draft',
            'submitted', 'submit' => 'Submitted',
            'reviewed', 'review' => 'Reviewed',
            'validated', 'valid' => 'Validated',
            'locked', 'final' => 'Locked',
            'revision requested', 'revisi', 'request revisi' => 'Revision Requested',
            default => 'Draft',
        };
    }

    private function resolveAcademicStatus(?float $finalScore, string $gradeLetter, string $remedialStatus): string
    {
        $remedialStatus = strtolower(trim($remedialStatus));
        if ($remedialStatus !== '') {
            return 'Remedial';
        }

        if ($finalScore === null && $gradeLetter === '') {
            return 'Belum Lengkap';
        }

        $gradeLetter = strtoupper(trim($gradeLetter));

        if ($gradeLetter !== '') {
            return in_array($gradeLetter, ['A', 'B', 'C'], true) ? 'Lulus' : 'Tidak Lulus';
        }

        if ($finalScore === null) {
            return 'Belum Lengkap';
        }

        return $finalScore >= 60 ? 'Lulus' : 'Tidak Lulus';
    }

    private function normalizeAttendanceStatus(string $status): string
    {
        $status = strtolower(trim($status));

        if ($status === '') {
            return 'alfa';
        }

        if (in_array($status, ['h', 'hadir', 'present', 'attendance'], true)) {
            return 'hadir';
        }

        if (in_array($status, ['i', 'izin', 'excused', 'permit'], true)) {
            return 'izin';
        }

        if (in_array($status, ['s', 'sakit', 'sick'], true)) {
            return 'sakit';
        }

        if (in_array($status, ['susulan', 'makeup', 'make-up'], true)) {
            return 'susulan';
        }

        if (in_array($status, ['a', 'alfa', 'alpha', 'absen', 'absent'], true)) {
            return 'alfa';
        }

        return 'alfa';
    }

    private function attendanceStatusLabel(float $percentage, int $totalSessions): string
    {
        if ($totalSessions <= 0) {
            return 'Belum Ada Pertemuan';
        }

        if ($percentage >= 75) {
            return 'Aman';
        }

        if ($percentage >= 60) {
            return 'Perlu Perhatian';
        }

        return 'Kurang Kehadiran';
    }

    private function formatAttendanceLabel(string $status): string
    {
        $status = trim($status);

        if ($status === '') {
            return 'Belum Diabsen';
        }

        return ucfirst($status);
    }

    private function normalizeRemedialStatus(string $status): string
    {
        $status = strtolower(trim($status));

        return match ($status) {
            'eligible', 'eligible remedial' => 'Eligible',
            'terdaftar', 'registered' => 'Terdaftar',
            'dijadwalkan', 'scheduled' => 'Dijadwalkan',
            'scored', 'sudah dinilai' => 'Sudah Dinilai',
            'validated' => 'Validated',
            'tidak mengikuti', 'absent', 'no-show' => 'Tidak Mengikuti',
            'dibatalkan', 'cancelled', 'canceled' => 'Dibatalkan',
            default => 'Eligible',
        };
    }

    private function averagePercent(array $rows, string $field): int
    {
        if ($rows === []) {
            return 0;
        }

        $values = [];

        foreach ($rows as $row) {
            if (isset($row[$field]) && is_numeric($row[$field])) {
                $values[] = (float) $row[$field];
            }
        }

        if ($values === []) {
            return 0;
        }

        return (int) round(array_sum($values) / count($values));
    }

    private function percentage(int $filled, int $expected): float
    {
        if ($expected <= 0) {
            return 0.0;
        }

        return round(($filled / $expected) * 100, 1);
    }

    private function notificationBadge(string $type): string
    {
        $type = strtolower(trim($type));

        return match ($type) {
            'success', 'validated', 'locked' => 'success',
            'warning', 'remedial', 'revision' => 'warning',
            'danger', 'rejected' => 'danger',
            default => 'info',
        };
    }

    private function formatDateTime(string $value): string
    {
        if ($value === '') {
            return '-';
        }

        $time = strtotime($value);

        if ($time === false) {
            return $value;
        }

        return date('d M Y H:i', $time);
    }

    private function resolveStudentIdentifier(array $student, string $field): string|int
    {
        $value = $student[$field] ?? null;

        if ($value === null || $value === '') {
            $value = $field === 'user_id'
                ? ($student['user_id'] ?? $student['student_id'] ?? '')
                : ($student['student_id'] ?? $student['user_id'] ?? 0);
        }

        if ($field === 'user_id') {
            return (string) $value;
        }

        return (int) $value;
    }

    private function isEmptyIdentifier(string|int $value, string $field): bool
    {
        if ($field === 'user_id') {
            return (string) $value === '';
        }

        return (int) $value <= 0;
    }

    private function formatIdentifierForSql(string|int $value, string $field): string
    {
        if ($field === 'user_id') {
            return db_connect()->escape((string) $value);
        }

        return (string) (int) $value;
    }

    private function resolveStudentField(string $table): string
    {
        $db = db_connect();

        if ($this->fieldExists('student_id', $table)) {
            return 'student_id';
        }

        if ($this->fieldExists('user_id', $table)) {
            return 'user_id';
        }

        if ($this->fieldExists('mahasiswa_id', $table)) {
            return 'mahasiswa_id';
        }

        return 'student_id';
    }

    private function fieldExists(string $field, string $table): bool
    {
        $db = db_connect();

        return $db->tableExists($table) && $db->fieldExists($field, $table);
    }

    private function findFirstField(string $table, array $candidates): ?string
    {
        foreach ($candidates as $field) {
            if ($this->fieldExists($field, $table)) {
                return $field;
            }
        }

        return null;
    }
}
