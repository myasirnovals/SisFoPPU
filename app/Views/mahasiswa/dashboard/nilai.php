<?= $this->extend('layout/mahasiswa_layout') ?>

<?= $this->section('styles') ?>
<style>
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

    .table thead th {
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-size: 0.72rem;
        color: #475569;
        white-space: nowrap;
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
<div class="card soft-card rounded-5 mb-4">
    <div class="card-body p-0">
        <div class="p-4 pb-0 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h1 class="h4 section-title mb-1">Ringkasan Nilai Praktikum</h1>
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
<?= $this->endSection() ?>