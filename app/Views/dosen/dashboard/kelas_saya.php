<?= $this->extend('layout/dosen_layout') ?>

<?= $this->section('styles') ?>
<style>
    .soft-card {
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid rgba(148, 163, 184, 0.18);
        backdrop-filter: blur(10px);
        box-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
    }

    .section-title {
        letter-spacing: -0.03em;
        font-weight: 800;
    }

    .class-card {
        transition: all 0.2s;
        border-left: 4px solid transparent;
    }

    .class-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.1);
    }

    .class-card.active {
        border-left-color: #0d6efd;
    }

    .student-row:hover {
        background: rgba(13, 110, 253, 0.03);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card soft-card rounded-5 mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h1 class="h4 section-title mb-1">Kelas Saya</h1>
                <p class="text-muted mb-0">Daftar kelas praktikum yang Anda ampu beserta detail mahasiswa.</p>
            </div>
            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2"><?= esc(count($classRows)) ?> kelas</span>
        </div>

        <?php if (empty($classRows)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-journal-x fs-1 d-block mb-3"></i>
                <p class="mb-1 fw-medium">Belum ada kelas yang diampu.</p>
                <small>Hubungi koordinator praktikum untuk penempatan kelas.</small>
            </div>
        <?php else: ?>
            <?php foreach ($classRows as $class): ?>
                <div class="card class-card rounded-4 mb-3 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <!-- Header Kelas -->
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-primary"><?= esc($class['course_code']) ?></span>
                                    <span class="badge bg-light text-dark border"><?= esc($class['class_name']) ?></span>
                                </div>
                                <h3 class="h5 fw-bold mb-1"><?= esc($class['course_name']) ?></h3>
                                <small class="text-muted"><?= esc($class['academic_year']) ?> • <?= esc($class['semester']) ?></small>
                            </div>
                            <div class="text-end">
                                <div class="h4 fw-bold mb-0"><?= esc($class['student_count']) ?></div>
                                <small class="text-muted">Mahasiswa</small>
                            </div>
                        </div>

                        <!-- Progress -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Progress Nilai</small>
                                <div class="progress" style="height: 24px;">
                                    <div class="progress-bar bg-primary" style="width:<?= esc($class['progress_nilai']) ?>%"><?= esc($class['progress_nilai']) ?>%</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Progress Kehadiran</small>
                                <div class="progress" style="height: 24px;">
                                    <div class="progress-bar bg-info" style="width:<?= esc($class['progress_kehadiran']) ?>%"><?= esc($class['progress_kehadiran']) ?>%</div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <a href="<?= esc($class['input_url']) ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square me-1"></i>Input Nilai</a>
                            <a href="<?= esc($class['rekap_url']) ?>" class="btn btn-sm btn-info"><i class="bi bi-table me-1"></i>Rekap</a>
                            <a href="<?= esc($class['validation_url']) ?>" class="btn btn-sm btn-success"><i class="bi bi-shield-check me-1"></i>Validasi</a>
                            <a href="<?= esc($class['remedial_url']) ?>" class="btn btn-sm btn-danger"><i class="bi bi-arrow-repeat me-1"></i>Remedial</a>
                        </div>

                        <!-- Daftar Mahasiswa -->
                        <?php if (!empty($class['students'])): ?>
                            <hr>
                            <h6 class="fw-semibold mb-2"><i class="bi bi-people me-1"></i>Daftar Mahasiswa</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($class['students'] as $i => $s): ?>
                                            <tr class="student-row">
                                                <td><?= $i + 1 ?></td>
                                                <td><code><?= esc($s['nim']) ?></code></td>
                                                <td class="fw-medium"><?= esc($s['student_name']) ?></td>
                                                <td><span class="badge bg-light text-dark border"><?= esc($s['enrollment_status']) ?></span></td>
                                                <td><a href="#" class="btn btn-sm btn-outline-primary">Detail</a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <!-- Komponen Penilaian -->
                        <?php if (!empty($class['components'])): ?>
                            <hr>
                            <h6 class="fw-semibold mb-2"><i class="bi bi-list-check me-1"></i>Komponen Penilaian</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($class['components'] as $c): ?>
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        <?= esc($c['component_name']) ?>
                                        <span class="text-muted">(<?= esc($c['weight']) ?>%)</span>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>