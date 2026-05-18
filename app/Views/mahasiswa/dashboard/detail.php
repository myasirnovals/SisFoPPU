<?= $this->extend('layout/mahasiswa_layout') ?>

<?= $this->section('styles') ?>
<style>
    .detail-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #0f766e 100%);
        color: #fff;
        border: 0;
        box-shadow: 0 22px 48px rgba(15, 23, 42, 0.18);
    }

    .soft-card {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(148, 163, 184, 0.18);
        backdrop-filter: blur(10px);
        box-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
    }

    .section-title {
        letter-spacing: -0.03em;
        font-weight: 800;
    }

    .table thead th {
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-size: 0.72rem;
        color: #475569;
        white-space: nowrap;
    }

    .empty-state {
        color: #64748b;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card detail-hero rounded-5 mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <div class="text-white-50 small">Detail Praktikum</div>
                <h1 class="display-6 fw-bold mb-2"><?= esc($classInfo['course_name'] ?? '-') ?></h1>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-dark rounded-pill px-3 py-2">Kode: <?= esc($classInfo['course_code'] ?? '-') ?></span>
                    <span class="badge bg-light text-dark rounded-pill px-3 py-2">Kelas: <?= esc($classInfo['class_name'] ?? '-') ?></span>
                    <span class="badge bg-light text-dark rounded-pill px-3 py-2"><?= esc($classInfo['semester_label'] ?? '-') ?> / <?= esc($classInfo['academic_year'] ?? '-') ?></span>
                </div>
            </div>
            <a href="<?= esc($backUrl ?? site_url('mahasiswa/dashboard')) ?>" class="btn btn-outline-light">Kembali ke Dashboard</a>
        </div>
        <div class="row g-3">
            <div class="col-12 col-lg-4">
                <div class="bg-white bg-opacity-10 rounded-4 p-3 border border-white border-opacity-10 h-100">
                    <div class="text-white-50 small">Dosen Pengampu</div>
                    <div class="fw-semibold fs-5"><?= esc($classInfo['lecturer_name'] ?? '-') ?></div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="bg-white bg-opacity-10 rounded-4 p-3 border border-white border-opacity-10 h-100">
                    <div class="text-white-50 small">Asisten Praktikum</div>
                    <div class="fw-semibold fs-5"><?= esc($classInfo['assistant_name'] ?? '-') ?></div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="bg-white bg-opacity-10 rounded-4 p-3 border border-white border-opacity-10 h-100">
                    <div class="text-white-50 small">Status Kelas</div>
                    <div class="fw-semibold fs-5"><span class="badge bg-<?= esc($classInfo['status_badge'] ?? 'secondary') ?>"><?= esc($classInfo['status'] ?? '-') ?></span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-lg-4">
        <div class="card soft-card rounded-5 h-100">
            <div class="card-body p-4">
                <h2 class="h5 section-title mb-3">Nilai Akhir</h2>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Nilai Akhir</span>
                    <strong>
                        <?php if (($finalScore['final_score'] ?? null) === null): ?>
                            <span class="badge bg-warning-subtle text-warning-emphasis">Belum Dinilai</span>
                        <?php else: ?>
                            <?= esc(number_format((float) $finalScore['final_score'], 2)) ?>
                        <?php endif; ?>
                    </strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Huruf Mutu</span>
                    <span class="badge bg-light text-dark border"><?= esc($finalScore['grade_letter'] ?? '-') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Status Nilai</span>
                    <span class="badge bg-<?= esc($finalScore['status_badge'] ?? 'secondary') ?>"><?= esc($finalScore['status'] ?? '-') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Status Akademik</span>
                    <span class="badge bg-<?= esc($finalScore['academic_badge'] ?? 'secondary') ?>"><?= esc($finalScore['academic_status'] ?? '-') ?></span>
                </div>
                <div class="text-muted small">Catatan: <?= esc($finalScore['notes'] ?? '-') ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8">
        <div class="card soft-card rounded-5 h-100">
            <div class="card-body p-4">
                <h2 class="h5 section-title mb-3">Catatan Remedial (Jika Ada)</h2>
                <?php if (($remedialRows ?? []) === []): ?>
                    <div class="text-center py-4 empty-state">
                        <i class="bi bi-shield-check fs-1 d-block mb-2"></i>
                        Tidak ada remedial aktif untuk praktikum ini.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Alasan</th>
                                    <th>Jadwal</th>
                                    <th>Status</th>
                                    <th>Nilai Sebelum</th>
                                    <th>Nilai Sesudah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($remedialRows as $row): ?>
                                    <tr>
                                        <td><?= esc($row['reason']) ?></td>
                                        <td><?= esc($row['schedule']) ?></td>
                                        <td><span class="badge bg-<?= esc($row['status_badge']) ?>"><?= esc($row['status']) ?></span></td>
                                        <td><?= $row['score_before'] === null ? '-' : esc(number_format((float) $row['score_before'], 2)) ?></td>
                                        <td><?= $row['score_after'] === null ? '-' : esc(number_format((float) $row['score_after'], 2)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card soft-card rounded-5 mb-4">
    <div class="card-body p-0">
        <div class="p-4 pb-0">
            <h2 class="h5 section-title mb-1">Komponen Nilai</h2>
            <p class="text-muted mb-0">Detail komponen, sub-komponen, bobot, dan nilai.</p>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Komponen</th>
                        <th>Sub-Komponen</th>
                        <th>Bobot</th>
                        <th>Nilai</th>
                        <th>Nilai Pembobotan</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (($componentRows ?? []) === []): ?>
                        <tr>
                            <td colspan="6">
                                <div class="text-center py-5 empty-state">
                                    <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i>
                                    Nilai belum tersedia.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($componentRows as $row): ?>
                            <tr>
                                <td class="fw-semibold"><?= esc($row['component_name']) ?></td>
                                <td><?= esc($row['subcomponent_name']) ?></td>
                                <td><?= $row['weight'] === null ? '-' : esc(number_format((float) $row['weight'], 2)) ?>%</td>
                                <td>
                                    <?php if ($row['score_value'] === null): ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis">Belum Dinilai</span>
                                    <?php else: ?>
                                        <?= esc(number_format((float) $row['score_value'], 2)) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= $row['weighted_score'] === null ? '-' : esc(number_format((float) $row['weighted_score'], 2)) ?></td>
                                <td><?= esc($row['notes']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card soft-card rounded-5">
    <div class="card-body p-0">
        <div class="p-4 pb-0">
            <h2 class="h5 section-title mb-1">Kehadiran Per Pertemuan</h2>
            <p class="text-muted mb-0">Detail kehadiran Anda di setiap pertemuan praktikum.</p>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Pertemuan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (($attendanceRows ?? []) === []): ?>
                        <tr>
                            <td colspan="4">
                                <div class="text-center py-5 empty-state">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                    Data kehadiran belum tersedia.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($attendanceRows as $row): ?>
                            <tr>
                                <td><?= esc($row['meeting_no']) ?></td>
                                <td><?= esc($row['session_date']) ?></td>
                                <td><?= esc($row['status']) ?></td>
                                <td><?= esc($row['notes']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
