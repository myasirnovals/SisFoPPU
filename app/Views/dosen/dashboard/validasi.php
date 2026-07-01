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

    .validation-card {
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .validation-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.1);
    }

    .validation-card.pending {
        border-left-color: #ffc107;
    }

    .validation-card.reviewed {
        border-left-color: #0dcaf0;
    }

    .validation-card.validated {
        border-left-color: #198754;
    }

    .status-timeline {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }

    .filter-btn.active {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card soft-card rounded-5 mb-4">
    <div class="card-body p-4 p-lg-5">
        <!-- Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h1 class="h4 section-title mb-1">Validasi Nilai</h1>
                <p class="text-muted mb-0">Review dan validasi submission nilai dari asisten praktikum.</p>
            </div>
            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-2">
                <?= esc(count($validationRows)) ?> perlu validasi
            </span>
        </div>

        <!-- Filter Buttons -->
        <div class="d-flex gap-2 mb-4 flex-wrap">
            <button class="btn btn-sm btn-outline-primary filter-btn active rounded-pill px-3">Semua</button>
            <button class="btn btn-sm btn-outline-warning filter-btn rounded-pill px-3">Submitted</button>
            <button class="btn btn-sm btn-outline-info filter-btn rounded-pill px-3">Reviewed</button>
            <button class="btn btn-sm btn-outline-success filter-btn rounded-pill px-3">Validated</button>
        </div>

        <?php if (empty($validationRows)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-shield-check fs-1 d-block mb-3"></i>
                <p class="mb-1 fw-medium">Tidak ada nilai yang perlu divalidasi.</p>
                <small>Semua submission sudah tervalidasi atau belum ada input dari asisten.</small>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Mata Kuliah</th>
                            <th>Kelas</th>
                            <th>Komponen Nilai</th>
                            <th>Mahasiswa</th>
                            <th>Nilai</th>
                            <th>Submitted By</th>
                            <th>Tanggal Submit</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($validationRows as $i => $row): ?>
                            <?php
                            $cardClass = match ($row['badge_class'] ?? 'secondary') {
                                'warning' => 'pending',
                                'info' => 'reviewed',
                                'success' => 'validated',
                                default => '',
                            };
                            ?>
                            <tr class="validation-card <?= $cardClass ?>">
                                <td><?= $i + 1 ?></td>
                                <td class="fw-semibold"><?= esc($row['course_name']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= esc($row['class_name']) ?></span></td>
                                <td><?= esc($row['component_name']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                            <i class="bi bi-person small"></i>
                                        </div>
                                        <span class="small">Mahasiswa</span>
                                    </div>
                                </td>
                                <td class="fw-bold text-primary">-</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-person-badge text-muted"></i>
                                        <?= esc($row['submitted_by']) ?>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i><?= esc($row['submitted_at']) ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= esc($row['badge_class']) ?> rounded-pill px-3 py-2">
                                        <?= esc($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?= esc($row['review_url']) ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye me-1"></i>Review
                                        </a>
                                        <button class="btn btn-sm btn-success" onclick="validateScore(this)">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="rejectScore(this)">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Bulk Actions -->
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label" for="selectAll">Pilih Semua</label>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-success" onclick="bulkValidate()">
                        <i class="bi bi-check-all me-1"></i>Validasi Terpilih
                    </button>
                    <button class="btn btn-outline-secondary" onclick="bulkExport()">
                        <i class="bi bi-download me-1"></i>Export
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Info Card -->
<div class="card soft-card rounded-5">
    <div class="card-body p-4">
        <div class="d-flex align-items-start gap-3">
            <div class="rounded-4 bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                <i class="bi bi-info-circle fs-4"></i>
            </div>
            <div>
                <h5 class="fw-semibold mb-1">Panduan Validasi</h5>
                <p class="text-muted mb-2 small">Pastikan nilai yang diinput sudah benar sebelum melakukan validasi.</p>
                <ul class="mb-0 small text-muted">
                    <li><strong>Review</strong> = Lihat detail nilai sebelum memutuskan</li>
                    <li><strong>Validasi</strong> = Setujui nilai (tidak bisa diubah lagi oleh asisten)</li>
                    <li><strong>Tolak</strong> = Kembalikan ke asisten untuk perbaikan</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function validateScore(btn) {
        if (confirm('Validasi nilai ini? Nilai akan dikunci dan tidak bisa diubah oleh asisten.')) {
            // TODO: AJAX call to validate
            btn.closest('tr').classList.add('validated');
            btn.closest('tr').style.opacity = '0.6';
        }
    }

    function rejectScore(btn) {
        const reason = prompt('Alasan penolakan:');
        if (reason) {
            // TODO: AJAX call to reject
            btn.closest('tr').classList.remove('pending', 'reviewed');
            btn.closest('tr').style.background = '#f8d7da';
        }
    }

    function bulkValidate() {
        if (confirm('Validasi semua nilai terpilih?')) {
            // TODO: Bulk validate
        }
    }

    function bulkExport() {
        // TODO: Export to Excel/PDF
        alert('Export functionality coming soon');
    }

    // Select all checkbox
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('tbody .form-check-input').forEach(cb => cb.checked = this.checked);
    });
</script>
<?= $this->endSection() ?>