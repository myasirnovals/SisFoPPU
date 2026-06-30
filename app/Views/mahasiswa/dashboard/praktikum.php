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

    .course-badge {
        font-size: 0.75rem;
    }

    .group-badge {
        background: rgba(99, 102, 241, 0.1);
        color: #4f46e5;
        border: 1px solid rgba(99, 102, 241, 0.2);
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
            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2">
                <?= esc(count($classRows ?? [])) ?> kelas
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Mata Kuliah</th>
                        <th>Kode</th>
                        <th>Kelas</th>
                        <th>Grup</th>
                        <th>Dosen</th>
                        <th>Asisten</th>
                        <th>Semester / Tahun</th>
                        <th>Status</th>
                        <th class="pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($classRows ?? [])): ?>
                        <tr>
                            <td colspan="10">
                                <div class="text-center py-5 empty-state">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    <h6 class="mb-1">Belum ada praktikum yang terdaftar</h6>
                                    <p class="mb-0 small">Anda belum terdaftar di kelas praktikum manapun.<br>Silakan hubungi koordinator praktikum atau admin.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($classRows as $index => $classRow): ?>
                            <tr>
                                <td class="ps-4"><?= esc($index + 1) ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div>
                                            <div class="fw-semibold"><?= esc($classRow['course_name']) ?></div>
                                            <?php if (!empty($classRow['credits'])): ?>
                                                <small class="text-muted"><?= esc($classRow['credits']) ?> SKS</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border course-badge"><?= esc($classRow['course_code']) ?></span>
                                </td>
                                <td><?= esc($classRow['class_name']) ?></td>
                                <td>
                                    <?php if (!empty($classRow['group_name']) && $classRow['group_name'] !== '-'): ?>
                                        <span class="badge group-badge rounded-pill"><?= esc($classRow['group_name']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($classRow['lecturer_name']) ?></td>
                                <td><?= esc($classRow['assistant_name']) ?></td>
                                <td>
                                    <small><?= esc($classRow['semester_label']) ?></small><br>
                                    <small class="text-muted"><?= esc($classRow['academic_year']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= esc($classRow['status_badge']) ?> rounded-pill">
                                        <?= ucfirst(esc($classRow['status'])) ?>
                                    </span>
                                </td>
                                <td class="pe-4">
                                    <a href="<?= esc($classRow['detail_url']) ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Info Card -->
<?php if (!empty($classRows)): ?>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card soft-card rounded-4 h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Informasi</h6>
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Klik "Detail" untuk melihat nilai dan kehadiran per mata kuliah</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Status <strong>Aktif</strong> = kelas sedang berjalan</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Status <strong>Selesai</strong> = nilai sudah final</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card soft-card rounded-4 h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-question-circle me-2 text-warning"></i>Bantuan</h6>
                    <p class="small text-muted mb-0">
                        Jika ada data yang tidak sesuai atau Anda butuh bantuan teknis,
                        silakan hubungi koordinator praktikum atau admin SISFO.
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>