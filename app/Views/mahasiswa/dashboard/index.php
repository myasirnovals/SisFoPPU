<?= $this->extend('layout/mahasiswa_layout') ?>

<?= $this->section('styles') ?>
<style>
    .hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #0f766e 55%, #f59e0b 100%);
        color: #fff;
        border: 0;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
        position: relative;
        overflow: hidden;
    }

    .hero-card::before,
    .hero-card::after {
        content: "";
        position: absolute;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
    }

    .hero-card::before {
        width: 220px;
        height: 220px;
        right: -80px;
        top: -70px;
    }

    .hero-card::after {
        width: 170px;
        height: 170px;
        right: 120px;
        bottom: -70px;
    }

    .soft-card {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(148, 163, 184, 0.18);
        backdrop-filter: blur(10px);
        box-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
    }

    .section-title {
        letter-spacing: -0.03em;
        font-weight: 800;
    }

    .stat-card {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 24px 44px rgba(15, 23, 42, 0.12);
    }

    .mini-pill {
        border: 1px solid rgba(255, 255, 255, 0.16);
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
    }

    .table thead th {
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-size: 0.72rem;
        color: #475569;
        white-space: nowrap;
    }

    .section-anchor {
        scroll-margin-top: 95px;
    }

    .progress-label {
        min-width: 3rem;
    }

    .note-card {
        border-left: 4px solid #f59e0b;
        background: rgba(245, 158, 11, 0.08);
    }

    .empty-state {
        color: #64748b;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="student-dashboard">
    <div class="hero-card rounded-5 mb-4">
        <div class="card-body p-4 p-lg-5 position-relative">
            <div class="row align-items-center g-4 position-relative">
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge mini-pill rounded-pill px-3 py-2">Dashboard Mahasiswa</span>
                        <span class="badge mini-pill rounded-pill px-3 py-2"><?= esc($academicYear ?? '-') ?> - <?= esc($semesterLabel ?? '-') ?></span>
                        <span class="badge mini-pill rounded-pill px-3 py-2">Role: Mahasiswa</span>
                    </div>
                    <h1 class="display-6 fw-bold mb-3">Halo, <?= esc($studentProfile['full_name'] ?? 'Mahasiswa') ?></h1>
                    <p class="lead text-white-75 mb-0">Pantau progres praktikum, kehadiran, nilai, dan status remedial Anda secara real-time.</p>
                </div>
                <div class="col-lg-4">
                    <div class="bg-white bg-opacity-10 rounded-4 p-3 border border-white border-opacity-10">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <div class="text-white-50 small">NIM</div>
                                <div class="fw-semibold fs-5"><?= esc($studentProfile['student_number'] ?? '-') ?></div>
                            </div>
                            <div class="rounded-4 bg-white bg-opacity-15 d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem;">
                                <i class="bi bi-person-circle fs-4"></i>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <div class="d-flex justify-content-between"><span class="text-white-50">Program Studi</span><strong><?= esc($studentProfile['study_program'] ?? '-') ?></strong></div>
                            <div class="d-flex justify-content-between"><span class="text-white-50">Semester Aktif</span><strong><?= esc($studentProfile['semester_active'] ?? $semesterLabel ?? '-') ?></strong></div>
                            <div class="d-flex justify-content-between"><span class="text-white-50">Tahun Akademik</span><strong><?= esc($studentProfile['academic_year_active'] ?? $academicYear ?? '-') ?></strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4 section-anchor" id="ringkasan">
        <?php foreach (($summaryCards ?? []) as $card): ?>
            <div class="col-12 col-sm-6 col-xl-4 col-xxl-2">
                <div class="card stat-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <div class="text-muted small mb-1"><?= esc($card['title']) ?></div>
                                <div class="h2 fw-bold mb-0"><?= esc($card['value']) ?></div>
                            </div>
                            <div class="rounded-4 bg-<?= esc($card['color']) ?> bg-opacity-10 text-<?= esc($card['color']) ?> d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem;">
                                <i class="bi <?= esc($card['icon']) ?> fs-4"></i>
                            </div>
                        </div>
                        <div class="text-muted small"><?= esc($card['description'] ?? '') ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card soft-card rounded-5 mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h2 class="h4 section-title mb-1">Ringkasan Progress Praktikum</h2>
                    <p class="text-muted mb-0">Gambaran cepat progres kehadiran, kelengkapan nilai, dan finalisasi kelas.</p>
                </div>
                <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2"><?= esc($summaryMeta['total_classes'] ?? 0) ?> kelas</span>
            </div>
            <div class="row g-3">
                <div class="col-12 col-lg-4">
                    <div class="p-3 rounded-4 border bg-light-subtle h-100">
                        <div class="text-muted small mb-2">Rata-rata Kehadiran</div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="progress flex-grow-1" style="height: 18px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= esc($summaryMeta['attendance_average'] ?? 0) ?>%;" aria-valuenow="<?= esc($summaryMeta['attendance_average'] ?? 0) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="fw-semibold progress-label"><?= esc($summaryMeta['attendance_average'] ?? 0) ?>%</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="p-3 rounded-4 border bg-light-subtle h-100">
                        <div class="text-muted small mb-2">Kelengkapan Nilai</div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="progress flex-grow-1" style="height: 18px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: <?= esc($summaryMeta['score_average'] ?? 0) ?>%;" aria-valuenow="<?= esc($summaryMeta['score_average'] ?? 0) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="fw-semibold progress-label"><?= esc($summaryMeta['score_average'] ?? 0) ?>%</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="p-3 rounded-4 border bg-light-subtle h-100">
                        <div class="text-muted small mb-2">Kelas Final</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="fw-semibold fs-4"><?= esc($summaryMeta['finalized'] ?? 0) ?></div>
                            <span class="text-muted">dari <?= esc($summaryMeta['total_classes'] ?? 0) ?> kelas</span>
                        </div>
                        <div class="mt-2 text-muted small">Kelas dengan nilai terkunci/tervalidasi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card soft-card rounded-5 mb-4 section-anchor" id="praktikum">
        <div class="card-body p-0">
            <div class="p-4 pb-0 d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="h4 section-title mb-1">Daftar Praktikum Saya</h2>
                    <p class="text-muted mb-0">Mata kuliah praktikum yang sedang atau pernah Anda ikuti.</p>
                </div>
                <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2"><?= esc(count($classRows ?? [])) ?> kelas</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Mata Kuliah</th>
                            <th>Kode</th>
                            <th>Kelas</th>
                            <th>Dosen</th>
                            <th>Asisten</th>
                            <th>Semester / Tahun</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (($classRows ?? []) === []): ?>
                            <tr>
                                <td colspan="9">
                                    <div class="text-center py-5 empty-state">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Belum ada praktikum yang terdaftar untuk akun Anda.
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($classRows as $index => $classRow): ?>
                                <tr>
                                    <td><?= esc($index + 1) ?></td>
                                    <td class="fw-semibold"><?= esc($classRow['course_name']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= esc($classRow['course_code']) ?></span></td>
                                    <td><?= esc($classRow['class_name']) ?></td>
                                    <td><?= esc($classRow['lecturer_name']) ?></td>
                                    <td><?= esc($classRow['assistant_name']) ?></td>
                                    <td><?= esc($classRow['semester_label']) ?> / <?= esc($classRow['academic_year']) ?></td>
                                    <td><span class="badge bg-<?= esc($classRow['status_badge']) ?>"><?= esc($classRow['status']) ?></span></td>
                                    <td><a href="<?= esc($classRow['detail_url']) ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card soft-card rounded-5 mb-4 section-anchor" id="kehadiran">
        <div class="card-body p-0">
            <div class="p-4 pb-0 d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="h4 section-title mb-1">Ringkasan Kehadiran</h2>
                    <p class="text-muted mb-0">Rekap kehadiran Anda di setiap praktikum.</p>
                </div>
                <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3 py-2"><?= esc(count($attendanceRows ?? [])) ?> praktikum</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Praktikum</th>
                            <th>Pertemuan</th>
                            <th>Hadir</th>
                            <th>Izin</th>
                            <th>Sakit</th>
                            <th>Alfa</th>
                            <th>Susulan</th>
                            <th>Persentase</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $hasAttendanceSessions = false;
                        foreach (($attendanceRows ?? []) as $row) {
                            if (($row['total_sessions'] ?? 0) > 0) {
                                $hasAttendanceSessions = true;
                                break;
                            }
                        }
                        ?>
                        <?php if (($attendanceRows ?? []) === [] || ! $hasAttendanceSessions): ?>
                            <tr>
                                <td colspan="9">
                                    <div class="text-center py-5 empty-state">
                                        <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                        Data kehadiran belum tersedia.
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($attendanceRows as $row): ?>
                                <tr>
                                    <td class="fw-semibold"><?= esc($row['course_name']) ?></td>
                                    <td><?= esc($row['total_sessions']) ?></td>
                                    <td><?= esc($row['hadir']) ?></td>
                                    <td><?= esc($row['izin']) ?></td>
                                    <td><?= esc($row['sakit']) ?></td>
                                    <td><?= esc($row['alfa']) ?></td>
                                    <td><?= esc($row['susulan']) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 14px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= esc($row['attendance_percentage']) ?>%;" aria-valuenow="<?= esc($row['attendance_percentage']) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="small fw-semibold"><?= esc($row['attendance_percentage']) ?>%</span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-<?= esc($row['status_badge']) ?>"><?= esc($row['status']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card soft-card rounded-5 mb-4 section-anchor" id="nilai">
        <div class="card-body p-0">
            <div class="p-4 pb-0 d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="h4 section-title mb-1">Ringkasan Nilai Praktikum</h2>
                    <p class="text-muted mb-0">Nilai akhir, huruf mutu, dan status akademik Anda.</p>
                </div>
                <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-3 py-2"><?= esc(count($scoreRows ?? [])) ?> praktikum</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Praktikum</th>
                            <th>Nilai Akhir</th>
                            <th>Huruf Mutu</th>
                            <th>Status Nilai</th>
                            <th>Status Akademik</th>
                            <th>Progress</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (($scoreRows ?? []) === []): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="text-center py-5 empty-state">
                                        <i class="bi bi-clipboard2-x fs-1 d-block mb-2"></i>
                                        Nilai belum tersedia.
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($scoreRows as $row): ?>
                                <tr>
                                    <td class="fw-semibold"><?= esc($row['course_name']) ?></td>
                                    <td>
                                        <?php if ($row['final_score'] === null): ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis">Belum Dinilai</span>
                                        <?php else: ?>
                                            <?= esc(number_format((float) $row['final_score'], 2)) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= esc($row['grade_letter']) ?></span></td>
                                    <td><span class="badge bg-<?= esc($row['score_status_badge']) ?>"><?= esc($row['score_status']) ?></span></td>
                                    <td><span class="badge bg-<?= esc($row['academic_badge']) ?>"><?= esc($row['academic_status']) ?></span></td>
                                    <td style="min-width: 160px;">
                                        <div class="progress" style="height: 16px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: <?= esc($row['score_progress']) ?>%;" aria-valuenow="<?= esc($row['score_progress']) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="small text-muted mt-1"><?= esc($row['score_progress']) ?>% lengkap</div>
                                    </td>
                                    <td><a href="<?= esc($row['detail_url']) ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                <div class="note-card rounded-4 p-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-info-circle text-warning fs-5"></i>
                        <div>
                            <div class="fw-semibold">Nilai per komponen tersedia di detail praktikum.</div>
                            <div class="small text-muted">Klik "Lihat Detail" untuk melihat nilai komponen, sub-komponen, dan catatan dosen/asisten.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card soft-card rounded-5 mb-4 section-anchor" id="remedial">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h2 class="h4 section-title mb-1">Status Remedial</h2>
                    <p class="text-muted mb-0">Informasi remedial jika Anda eligible atau sedang mengikuti remedial.</p>
                </div>
                <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-2"><?= esc(count($remedialRows ?? [])) ?> remedial</span>
            </div>

            <?php if (($remedialRows ?? []) === []): ?>
                <div class="text-center py-4 empty-state">
                    <i class="bi bi-clipboard-check fs-1 d-block mb-2"></i>
                    Tidak ada remedial aktif saat ini.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Praktikum</th>
                                <th>Alasan</th>
                                <th>Jenis Remedial</th>
                                <th>Komponen</th>
                                <th>Jadwal</th>
                                <th>Status</th>
                                <th>Nilai Sebelum</th>
                                <th>Nilai Sesudah</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($remedialRows as $row): ?>
                                <tr>
                                    <td class="fw-semibold"><?= esc($row['course_name']) ?> (<?= esc($row['class_name']) ?>)</td>
                                    <td><?= esc($row['reason']) ?></td>
                                    <td><?= esc($row['remedial_type']) ?></td>
                                    <td><?= esc($row['component_label']) ?></td>
                                    <td><?= esc($row['schedule']) ?></td>
                                    <td><span class="badge bg-<?= esc($row['status_badge']) ?>"><?= esc($row['status']) ?></span></td>
                                    <td><?= $row['score_before'] === null ? '-' : esc(number_format((float) $row['score_before'], 2)) ?></td>
                                    <td><?= $row['score_after'] === null ? '-' : esc(number_format((float) $row['score_after'], 2)) ?></td>
                                    <td><?= esc($row['notes']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card soft-card rounded-5 mb-4 section-anchor" id="notifikasi">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h2 class="h4 section-title mb-1">Aktivitas / Notifikasi Terbaru</h2>
                    <p class="text-muted mb-0">Riwayat terbaru terkait nilai, validasi, remedial, atau catatan dosen.</p>
                </div>
                <span class="badge bg-dark-subtle text-dark-emphasis rounded-pill px-3 py-2"><?= esc(count($notifications ?? [])) ?> notifikasi</span>
            </div>

            <?php if (($notifications ?? []) === []): ?>
                <div class="text-center py-4 empty-state">
                    <i class="bi bi-bell fs-1 d-block mb-2"></i>
                    Belum ada notifikasi.
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($notifications as $row): ?>
                        <div class="col-12 col-lg-6">
                            <div class="border rounded-4 p-3 h-100 bg-white">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                    <div class="fw-semibold"><?= esc($row['title']) ?></div>
                                    <span class="badge bg-<?= esc($row['badge']) ?>"><?= esc($row['time']) ?></span>
                                </div>
                                <div class="text-muted small"><?= esc($row['message']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
