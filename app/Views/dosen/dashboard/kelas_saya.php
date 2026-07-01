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

    /* Popup styles */
    .popup-modal .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .popup-modal .modal-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 16px 16px 0 0;
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    }

    .popup-modal .modal-title {
        color: #1e293b;
        font-size: 1.1rem;
    }

    .score-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }

    .percentage-cell {
        font-weight: 600;
        font-size: 0.85rem;
    }

    /* Rekap table sticky header */
    .rekap-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    /* Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .popup-content-loaded {
        animation: fadeIn 0.3s ease-out;
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
                            <button type="button" class="btn btn-sm btn-info" onclick="openPopup('rekap', <?= $class['id'] ?>)">
                                <i class="bi bi-table me-1"></i>Rekap
                            </button>
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
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                            onclick="openPopup('detail', <?= $class['id'] ?>, '<?= esc($s['nim']) ?>')">
                                                            <i class="bi bi-eye"></i> Detail
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-warning"
                                                            onclick="openPopup('input', <?= $class['id'] ?>, '<?= esc($s['nim']) ?>')">
                                                            <i class="bi bi-pencil-square me-1"></i> Input Nilai
                                                        </button>
                                                    </div>
                                                </td>
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

<!-- Popup Modal -->
<div class="modal fade popup-modal" id="popupModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="popupTitle">
                    <i class="bi bi-window me-2"></i>Detail
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="popupContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-3">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    /**
     * Open popup modal and load content via AJAX
     */
    function openPopup(type, classId, studentNim = null) {
        const modalEl = document.getElementById('popupModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const contentDiv = document.getElementById('popupContent');
        const titleDiv = document.getElementById('popupTitle');

        // Set title and icon
        const config = {
            'input': {
                title: 'Input Nilai',
                icon: 'bi-pencil-square'
            },
            'rekap': {
                title: 'Rekap Nilai Kelas',
                icon: 'bi-table'
            },
            'detail': {
                title: 'Detail Mahasiswa',
                icon: 'bi-person-vcard'
            }
        };

        const cfg = config[type] || config['detail'];
        titleDiv.innerHTML = `<i class="bi ${cfg.icon} me-2"></i>${cfg.title}`;

        // Show loading
        contentDiv.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-3">Memuat data...</p>
        </div>
    `;

        modal.show();

        // Build URL
        let url = '';
        switch (type) {
            case 'input':
                url = `<?= base_url('dosen/input-nilai') ?>/${classId}/${studentNim}`;
                break;
            case 'rekap':
                url = `<?= base_url('dosen/rekap-nilai') ?>/${classId}`;
                break;
            case 'detail':
                url = `<?= base_url('dosen/detail-mahasiswa') ?>/${classId}/${studentNim}`;
                break;
        }

        // Fetch content
        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}: Gagal memuat data`);
                return response.text();
            })
            .then(html => {
                contentDiv.innerHTML = `<div class="popup-content-loaded">${html}</div>`;

                // Re-initialize tooltips
                const tooltipTriggerList = contentDiv.querySelectorAll('[data-bs-toggle="tooltip"]');
                [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
            })
            .catch(error => {
                contentDiv.innerHTML = `
            <div class="alert alert-danger d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                <div>
                    <strong>Gagal memuat data</strong><br>
                    <span class="small">${error.message}</span>
                </div>
            </div>
        `;
            });
    }

    /**
     * Handle AJAX form submission inside modal
     */
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form.classList.contains('ajax-form')) return;

        e.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalHtml = submitBtn ? submitBtn.innerHTML : '';

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
        }

        const formData = new FormData(form);

        fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Remove old alerts
                form.querySelectorAll('.alert').forEach(el => el.remove());

                const alertClass = data.success ? 'alert-success' : 'alert-danger';
                const icon = data.success ? 'bi-check-circle-fill' : 'bi-x-circle-fill';

                const alertDiv = document.createElement('div');
                alertDiv.className = `alert ${alertClass} alert-dismissible fade show d-flex align-items-center`;
                alertDiv.innerHTML = `
            <i class="bi ${icon} me-2"></i>
            <div>${data.message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
                form.insertBefore(alertDiv, form.firstChild);

                if (data.success) {
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('popupModal')).hide();
                        // Optional: reload page to show updated data
                        location.reload();
                    }, 1500);
                }
            })
            .catch(error => {
                form.querySelectorAll('.alert').forEach(el => el.remove());

                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show d-flex align-items-center';
                alertDiv.innerHTML = `
            <i class="bi bi-x-circle-fill me-2"></i>
            <div>Terjadi kesalahan: ${error.message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
                form.insertBefore(alertDiv, form.firstChild);
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            });
    });

    // Initialize tooltips on page load
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
    });
</script>
<?= $this->endSection() ?>