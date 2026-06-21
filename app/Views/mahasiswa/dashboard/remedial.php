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
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h1 class="h4 section-title mb-1">Status Remedial</h1>
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
<?= $this->endSection() ?>