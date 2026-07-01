<?php
$student = $student ?? [];
$enrollment = $enrollment ?? [];
$class = $class ?? [];
$scores = $scores ?? [];
$attendanceSummary = $attendanceSummary ?? [];
$finalScore = $finalScore ?? [];

// Calculate attendance percentage
$totalSessions = $attendanceSummary['total_sessions'] ?? 0;
$hadir = $attendanceSummary['hadir'] ?? 0;
$attendancePct = $totalSessions > 0 ? round(($hadir / $totalSessions) * 100, 1) : 0;

// Determine attendance color
$attendanceColor = $attendancePct >= 80 ? 'success' : ($attendancePct >= 60 ? 'warning' : 'danger');
?>
<div class="popup-detail">
    <!-- Profile Header -->
    <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3">
        <div class="flex-shrink-0">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm"
                style="width: 64px; height: 64px; font-size: 28px; font-weight: 600;">
                <?= strtoupper(substr($student['full_name'] ?? 'M', 0, 1)) ?>
            </div>
        </div>
        <div class="flex-grow-1">
            <h5 class="mb-1 fw-bold"><?= esc($student['full_name'] ?? 'Mahasiswa') ?></h5>
            <div class="d-flex flex-wrap gap-3 text-muted small">
                <span><i class="bi bi-person-badge me-1"></i><?= esc($student['id'] ?? '-') ?></span>
                <span><i class="bi bi-envelope me-1"></i><?= esc($student['email'] ?? '-') ?></span>
                <span><i class="bi bi-calendar me-1"></i>Angkatan <?= esc($student['class_year'] ?? '-') ?></span>
            </div>
        </div>
        <div class="text-end">
            <?php
            $statusClass = match ($enrollment['enrollment_status'] ?? 'aktif') {
                'aktif' => 'bg-success',
                'drop' => 'bg-danger',
                'lulus' => 'bg-primary',
                'remedial' => 'bg-warning',
                default => 'bg-secondary'
            };
            $statusLabel = match ($enrollment['enrollment_status'] ?? 'aktif') {
                'aktif' => 'Aktif',
                'drop' => 'Drop',
                'lulus' => 'Lulus',
                'remedial' => 'Remedial',
                default => 'Aktif'
            };
            ?>
            <span class="badge <?= $statusClass ?> px-3 py-2"><?= $statusLabel ?></span>
        </div>
    </div>

    <div class="row g-3">
        <!-- Left Column: Info & Attendance -->
        <div class="col-md-4">
            <!-- Class Info -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold border-bottom-0 pt-3">
                    <i class="bi bi-book me-1 text-primary"></i>Info Kelas
                </div>
                <div class="card-body small">
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Mata Kuliah:</span>
                        <span class="fw-medium text-end"><?= esc($class['nama_mk'] ?? '-') ?></span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Kode:</span>
                        <span class="fw-medium"><?= esc($class['kode_mk'] ?? '-') ?></span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">SKS:</span>
                        <span class="fw-medium"><?= esc($class['sks'] ?? '-') ?></span>
                    </div>
                    <div class="mb-0 d-flex justify-content-between">
                        <span class="text-muted">Kelas:</span>
                        <span class="fw-medium"><?= esc($class['class_name'] ?? '-') ?></span>
                    </div>
                </div>
            </div>

            <!-- Attendance Summary -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold border-bottom-0 pt-3">
                    <i class="bi bi-calendar-check me-1 text-primary"></i>Ringkasan Kehadiran
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="position-relative d-inline-block">
                            <svg width="80" height="80" viewBox="0 0 80 80">
                                <circle cx="40" cy="40" r="35" fill="none" stroke="#e9ecef" stroke-width="8" />
                                <circle cx="40" cy="40" r="35" fill="none" stroke="<?= $attendancePct >= 80 ? '#198754' : ($attendancePct >= 60 ? '#ffc107' : '#dc3545') ?>"
                                    stroke-width="8" stroke-dasharray="<?= 220 * ($attendancePct / 100) ?> 220"
                                    stroke-linecap="round" transform="rotate(-90 40 40)" />
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <div class="h4 fw-bold mb-0 text-<?= $attendanceColor ?>"><?= $attendancePct ?>%</div>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">Kehadiran</small>
                    </div>

                    <div class="row g-2 text-center small">
                        <div class="col-4">
                            <div class="p-2 rounded bg-success-subtle">
                                <div class="fw-bold text-success"><?= $hadir ?></div>
                                <small class="text-muted">Hadir</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-warning-subtle">
                                <div class="fw-bold text-warning"><?= $attendanceSummary['izin'] ?? 0 ?></div>
                                <small class="text-muted">Izin</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-info-subtle">
                                <div class="fw-bold text-info"><?= $attendanceSummary['sakit'] ?? 0 ?></div>
                                <small class="text-muted">Sakit</small>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 text-center small mt-2">
                        <div class="col-6">
                            <div class="p-2 rounded bg-danger-subtle">
                                <div class="fw-bold text-danger"><?= $attendanceSummary['alfa'] ?? 0 ?></div>
                                <small class="text-muted">Alfa</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded bg-secondary-subtle">
                                <div class="fw-bold text-secondary"><?= $attendanceSummary['susulan'] ?? 0 ?></div>
                                <small class="text-muted">Susulan</small>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">Total Pertemuan: <strong><?= $totalSessions ?></strong></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Scores -->
        <div class="col-md-8">
            <!-- Component Scores -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold border-bottom-0 pt-3 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-check me-1 text-primary"></i>Nilai Komponen</span>
                    <button type="button" class="btn btn-sm btn-warning"
                        onclick="openPopup('input', <?= $class['id'] ?? 0 ?>, '<?= esc($student['id'] ?? '') ?>')">
                        <i class="bi bi-pencil-square me-1"></i>Edit Nilai
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Komponen</th>
                                    <th class="text-center">Tipe</th>
                                    <th class="text-center">Bobot</th>
                                    <th class="text-center">Nilai</th>
                                    <th class="text-center">Max</th>
                                    <th class="text-center">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($scores)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                            Belum ada nilai yang diinput
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($scores as $score): ?>
                                        <?php
                                        $scoreVal = $score['score_value'] ?? null;
                                        $maxScore = $score['max_score'] ?? 100;
                                        $pct = ($scoreVal !== null && $maxScore > 0) ? (($scoreVal / $maxScore) * 100) : null;
                                        $pctColor = $pct !== null ? ($pct >= 60 ? 'success' : 'danger') : 'muted';
                                        ?>
                                        <tr>
                                            <td class="fw-medium">
                                                <i class="bi bi-check-circle-fill me-1 text-<?= $pctColor ?>"></i>
                                                <?= esc($score['component_name'] ?? '-') ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border"><?= esc($score['component_type'] ?? '-') ?></span>
                                            </td>
                                            <td class="text-center"><?= $score['weight'] ?? 0 ?>%</td>
                                            <td class="text-center fw-semibold text-<?= $pctColor ?>">
                                                <?= $scoreVal !== null ? number_format($scoreVal, 1) : '-' ?>
                                            </td>
                                            <td class="text-center text-muted"><?= $maxScore ?></td>
                                            <td class="text-center">
                                                <?php if ($pct !== null): ?>
                                                    <span class="badge bg-<?= $pctColor ?>-subtle text-<?= $pctColor ?>-emphasis">
                                                        <?= number_format($pct, 1) ?>%
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Final Score -->
            <?php if ($finalScore): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-semibold border-bottom-0 pt-3">
                        <i class="bi bi-award me-1 text-primary"></i>Nilai Akhir
                    </div>
                    <div class="card-body">
                        <div class="row g-3 text-center">
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-primary-subtle border border-primary-subtle">
                                    <div class="display-6 fw-bold text-primary">
                                        <?= $finalScore['final_score'] !== null ? number_format($finalScore['final_score'], 2) : '-' ?>
                                    </div>
                                    <small class="text-muted">Nilai Akhir</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-success-subtle border border-success-subtle">
                                    <div class="display-6 fw-bold text-success">
                                        <?= esc($finalScore['grade_letter'] ?? '-') ?>
                                    </div>
                                    <small class="text-muted">Huruf Mutu</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-info-subtle border border-info-subtle">
                                    <div class="display-6 fw-bold text-info">
                                        <?= $finalScore['grade_point'] !== null ? number_format($finalScore['grade_point'], 2) : '-' ?>
                                    </div>
                                    <small class="text-muted">Angka Mutu</small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 d-flex align-items-center gap-2 flex-wrap">
                            <span class="text-muted small">Status Nilai:</span>
                            <?php
                            $finalStatus = $finalScore['status'] ?? 'draft';
                            $statusBadge = match ($finalStatus) {
                                'validated' => 'bg-success',
                                'locked' => 'bg-dark',
                                'submitted' => 'bg-warning',
                                'reviewed' => 'bg-info',
                                default => 'bg-secondary'
                            };
                            $statusLabel = match ($finalStatus) {
                                'validated' => 'Tervalidasi',
                                'locked' => 'Terkunci',
                                'submitted' => 'Dikirim',
                                'reviewed' => 'Ditinjau',
                                'draft' => 'Draft',
                                default => ucfirst($finalStatus)
                            };
                            ?>
                            <span class="badge <?= $statusBadge ?>"><?= $statusLabel ?></span>

                            <?php if (!empty($finalScore['validation_status'])): ?>
                                <?php
                                $valStatus = $finalScore['validation_status'];
                                $valBadge = match ($valStatus) {
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-info'
                                };
                                ?>
                                <span class="badge <?= $valBadge ?>">Validasi: <?= ucfirst($valStatus) ?></span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($finalScore['notes'])): ?>
                            <div class="mt-2 p-2 bg-light rounded small">
                                <i class="bi bi-chat-left-text me-1 text-muted"></i>
                                <span class="text-muted"><?= esc($finalScore['notes']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <div>Nilai akhir belum dihitung. Pastikan semua komponen penilaian sudah diinput.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>