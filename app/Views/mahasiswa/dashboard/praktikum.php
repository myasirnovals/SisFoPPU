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
                <h1 class="h4 section-title mb-1">Daftar Praktikum Saya</h1>
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
<?= $this->endSection() ?>