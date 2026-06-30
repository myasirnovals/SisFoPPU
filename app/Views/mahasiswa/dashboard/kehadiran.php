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

    .empty-state {
        color: #64748b;
    }

    .progress {
        background-color: #e2e8f0;
        border-radius: 0.5rem;
    }

    .progress-bar {
        border-radius: 0.5rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card soft-card rounded-5 mb-4">
    <div class="card-body p-0">
        <div class="p-4 pb-0 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h1 class="h4 section-title mb-1">Ringkasan Kehadiran</h1>
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
                    <?php if (($attendanceRows ?? []) === []): ?>
                        <tr>
                            <td colspan="9">
                                <div class="text-center py-5 empty-state">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                    Anda belum terdaftar di kelas praktikum manapun.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($attendanceRows as $row): ?>
                            <tr>
                                <td class="fw-semibold"><?= esc($row['course_name'] ?? '-') ?></td>
                                <td><?= esc($row['total_sessions'] ?? 0) ?></td>
                                <td><?= esc($row['hadir'] ?? 0) ?></td>
                                <td><?= esc($row['izin'] ?? 0) ?></td>
                                <td><?= esc($row['sakit'] ?? 0) ?></td>
                                <td><?= esc($row['alfa'] ?? 0) ?></td>
                                <td><?= esc($row['susulan'] ?? 0) ?></td>
                                <td>
                                    <?php if (($row['total_sessions'] ?? 0) > 0): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 14px;">
                                                <div class="progress-bar bg-success"
                                                    role="progressbar"
                                                    style="width: <?= esc($row['attendance_percentage'] ?? 0) ?>%;"
                                                    aria-valuenow="<?= esc($row['attendance_percentage'] ?? 0) ?>"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span class="small fw-semibold" style="min-width: 45px;">
                                                <?= esc($row['attendance_percentage'] ?? 0) ?>%
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= esc($row['status_badge'] ?? 'secondary') ?>">
                                        <?= esc($row['status'] ?? 'Belum Ada Pertemuan') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>