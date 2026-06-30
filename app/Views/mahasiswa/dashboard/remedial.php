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

    /* Badge khusus remedial */
    .badge-remedial-eligible {
        background-color: #fff3cd;
        color: #856404;
    }

    .badge-remedial-terdaftar {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .badge-remedial-dijadwalkan {
        background-color: #cce5ff;
        color: #004085;
    }

    .badge-remedial-sudah_dinilai {
        background-color: #d4edda;
        color: #155724;
    }

    .badge-remedial-validated {
        background-color: #c3e6cb;
        color: #155724;
    }

    .badge-remedial-tidak_mengikuti {
        background-color: #e2e3e5;
        color: #383d41;
    }

    .badge-remedial-dibatalkan {
        background-color: #f8d7da;
        color: #721c24;
    }

    .period-badge {
        font-size: 0.65rem;
        letter-spacing: 0.05em;
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
            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-2">
                <?= esc(count($remedialRows ?? [])) ?> remedial
            </span>
        </div>

        <?php if (($remedialRows ?? []) === []): ?>
            <div class="text-center py-5 empty-state">
                <i class="bi bi-clipboard-check fs-1 d-block mb-2"></i>
                <p class="mb-0">Tidak ada remedial aktif saat ini.</p>
                <small class="text-muted">Anda tidak memiliki data remedial untuk semester ini.</small>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Praktikum</th>
                            <th>Periode Remedial</th>
                            <th>Alasan</th>
                            <th>Jenis Remedial</th>
                            <th>Komponen</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                            <th>Nilai Sebelum</th>
                            <th>Nilai Sesudah</th>
                            <th>Hasil</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($remedialRows as $row): ?>
                            <?php
                            // Tentukan badge class berdasarkan status
                            $badgeClass = match ($row['status_badge'] ?? 'secondary') {
                                'warning' => 'badge-remedial-eligible',
                                'info'    => 'badge-remedial-terdaftar',
                                'primary' => 'badge-remedial-dijadwalkan',
                                'success' => $row['status'] === 'Tervalidasi' ? 'badge-remedial-validated' : 'badge-remedial-sudah_dinilai',
                                'secondary' => 'badge-remedial-tidak_mengikuti',
                                default   => 'bg-secondary',
                            };

                            // Format hasil akhir
                            $hasil = '-';
                            if ($row['final_score_after'] !== null) {
                                $hasil = number_format($row['final_score_after'], 2);
                                if ($row['grade_after'] !== '-') {
                                    $hasil .= ' (' . esc($row['grade_after']) . ')';
                                }
                                if ($row['is_passed']) {
                                    $hasil .= ' ✅';
                                }
                            } elseif ($row['score_after'] !== null) {
                                $hasil = number_format($row['score_after'], 2);
                                if ($row['max_after_score'] !== null) {
                                    $hasil .= ' / ' . number_format($row['max_after_score'], 2);
                                }
                            }
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= esc($row['course_name']) ?></div>
                                    <small class="text-muted"><?= esc($row['class_name']) ?> • <?= esc($row['course_code']) ?></small>
                                </td>
                                <td>
                                    <div class="fw-medium"><?= esc($row['period_title']) ?></div>
                                    <?php if (($row['period_status'] ?? '') !== ''): ?>
                                        <span class="badge bg-light text-dark period-badge mt-1">
                                            <?= ucfirst(esc($row['period_status'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($row['reason']) ?></td>
                                <td><?= esc($row['remedial_type']) ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= esc($row['component_label']) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted"><?= esc($row['schedule']) ?></small>
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 <?= $badgeClass ?>">
                                        <?= esc($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['final_score_before'] !== null): ?>
                                        <span class="text-decoration-line-through text-muted">
                                            <?= number_format($row['final_score_before'], 2) ?>
                                        </span>
                                        <?php if ($row['grade_before'] !== '-'): ?>
                                            <small class="text-muted">(<?= esc($row['grade_before']) ?>)</small>
                                        <?php endif; ?>
                                    <?php elseif ($row['score_before'] !== null): ?>
                                        <?= number_format($row['score_before'], 2) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['score_after'] !== null): ?>
                                        <span class="fw-semibold text-success">
                                            <?= number_format($row['score_after'], 2) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-medium"><?= $hasil ?></span>
                                    <?php if ($row['is_passed']): ?>
                                        <span class="badge bg-success-subtle text-success-emphasis ms-1">Lulus</span>
                                    <?php elseif ($row['final_score_after'] !== null && !$row['is_passed']): ?>
                                        <span class="badge bg-danger-subtle text-danger-emphasis ms-1">Tidak Lulus</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Info Card -->
            <div class="alert alert-light border mt-4 mb-0">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-info-circle-fill text-info mt-1"></i>
                    <div>
                        <strong>Keterangan:</strong>
                        <ul class="mb-0 mt-1 small text-muted">
                            <li><strong>Eligible</strong> = Anda memenuhi syarat untuk mengikuti remedial</li>
                            <li><strong>Terdaftar</strong> = Anda sudah mendaftar pada periode remedial</li>
                            <li><strong>Dijadwalkan</strong> = Jadwal remedial sudah ditentukan</li>
                            <li><strong>Sudah Dinilai</strong> = Nilai remedial sudah diinput</li>
                            <li><strong>Tervalidasi</strong> = Nilai remedial sudah divalidasi dan final</li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>