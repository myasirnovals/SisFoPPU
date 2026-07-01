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

    .remedial-card {
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .remedial-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.1);
    }

    .remedial-card.eligible {
        border-left-color: #ffc107;
    }

    .remedial-card.terdaftar {
        border-left-color: #0dcaf0;
    }

    .remedial-card.dijadwalkan {
        border-left-color: #0d6efd;
    }

    .remedial-card.selesai {
        border-left-color: #198754;
    }

    .score-before {
        text-decoration: line-through;
        color: #dc3545;
    }

    .score-after {
        font-weight: bold;
        color: #198754;
    }

    .period-badge {
        font-size: 0.7rem;
        letter-spacing: 0.05em;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card soft-card rounded-5 mb-4">
    <div class="card-body p-4 p-lg-5">
        <!-- Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h1 class="h4 section-title mb-1">Mahasiswa Remedial</h1>
                <p class="text-muted mb-0">Pantau dan kelola peserta remedial dari kelas yang Anda ampu.</p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-2">
                    <?= esc(count(array_filter($remedialRows, fn($r) => ($r['status'] ?? '') === 'Eligible'))) ?> eligible
                </span>
                <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-3 py-2">
                    <?= esc(count(array_filter($remedialRows, fn($r) => in_array($r['status'] ?? '', ['Terdaftar', 'Dijadwalkan'])))) ?> aktif
                </span>
            </div>
        </div>

        <!-- Filter -->
        <div class="d-flex gap-2 mb-4 flex-wrap">
            <button class="btn btn-sm btn-outline-primary filter-btn active rounded-pill px-3">Semua</button>
            <button class="btn btn-sm btn-outline-warning filter-btn rounded-pill px-3">Eligible</button>
            <button class="btn btn-sm btn-outline-info filter-btn rounded-pill px-3">Terdaftar</button>
            <button class="btn btn-sm btn-outline-success filter-btn rounded-pill px-3">Selesai</button>
        </div>

        <?php if (empty($remedialRows)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-clipboard-check fs-1 d-block mb-3"></i>
                <p class="mb-1 fw-medium">Belum ada mahasiswa remedial.</p>
                <small>Mahasiswa akan muncul di sini jika memenuhi kriteria remedial.</small>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Mata Kuliah</th>
                            <th>Kelas</th>
                            <th>Nilai Awal</th>
                            <th>Nilai Remedial</th>
                            <th>Grade</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($remedialRows as $i => $row): ?>
                            <?php
                            $statusKey = match ($row['status'] ?? '') {
                                'Eligible' => 'eligible',
                                'Terdaftar' => 'terdaftar',
                                'Dijadwalkan' => 'dijadwalkan',
                                'Selesai' => 'selesai',
                                default => '',
                            };
                            ?>
                            <tr class="remedial-card <?= $statusKey ?>">
                                <td><?= $i + 1 ?></td>
                                <td><code><?= esc($row['nim']) ?></code></td>
                                <td class="fw-semibold"><?= esc($row['student_name']) ?></td>
                                <td><?= esc($row['course_name']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= esc($row['class_name']) ?></span></td>
                                <td>
                                    <?php if ($row['score'] !== '-'): ?>
                                        <span class="score-before"><?= esc($row['score']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (($row['score_after'] ?? null) !== null): ?>
                                        <span class="score-after"><?= number_format((float)$row['score_after'], 1) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Belum dinilai</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= esc($row['grade']) ?></span>
                                </td>
                                <td style="max-width: 200px;">
                                    <small class="text-muted"><?= esc($row['reason']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= esc($row['badge_class']) ?> rounded-pill px-3 py-2">
                                        <?= esc($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?= esc($row['detail_url']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>Detail
                                        </a>
                                        <a href="<?= esc($row['manage_url']) ?>" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pencil-square me-1"></i>Kelola
                                        </a>
                                        <?php if (($row['status'] ?? '') === 'Eligible'): ?>
                                            <button class="btn btn-sm btn-success" onclick="approveRemedial(this)">
                                                <i class="bi bi-check-lg"></i>Approve
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Info Cards -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="card soft-card rounded-5 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-4 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                        <i class="bi bi-exclamation-triangle fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-semibold mb-1">Kriteria Remedial</h5>
                        <ul class="mb-0 small text-muted">
                            <li>Nilai akhir &lt; 60 (tidak lulus)</li>
                            <li>Kehadiran &lt; 75%</li>
                            <li>Komponen nilai tidak lengkap</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card soft-card rounded-5 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-4 bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                        <i class="bi bi-calendar-event fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-semibold mb-1">Periode Remedial</h5>
                        <p class="mb-0 small text-muted">
                            Pastikan jadwal remedial sudah ditentukan sebelum approve peserta.
                            Koordinator akan mengatur jadwal dan ruangan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function approveRemedial(btn) {
        if (confirm('Approve mahasiswa ini untuk mengikuti remedial?')) {
            // TODO: AJAX call
            btn.closest('tr').classList.remove('eligible');
            btn.closest('tr').classList.add('terdaftar');
            btn.remove();
        }
    }
</script>
<?= $this->endSection() ?>