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
<?= $this->endSection() ?>