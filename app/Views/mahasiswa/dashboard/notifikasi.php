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

    .empty-state {
        color: #64748b;
    }

    /* Notifikasi item styles */
    .notif-item {
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .notif-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    }

    .notif-item.notif-unread {
        border-left-color: #0d6efd;
        background: linear-gradient(90deg, rgba(13, 110, 253, 0.03) 0%, transparent 100%);
    }

    .notif-icon-wrapper {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        flex-shrink: 0;
    }

    .notif-time {
        font-size: 0.7rem;
        white-space: nowrap;
    }

    .notif-category-badge {
        font-size: 0.65rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    /* Badge variants */
    .badge-notif-info {
        background-color: #e7f1ff;
        color: #084298;
    }

    .badge-notif-success {
        background-color: #d1e7dd;
        color: #0f5132;
    }

    .badge-notif-warning {
        background-color: #fff3cd;
        color: #856404;
    }

    .badge-notif-danger {
        background-color: #f8d7da;
        color: #842029;
    }

    .badge-notif-dark {
        background-color: #d3d3d3;
        color: #1a1a1a;
    }

    .badge-notif-secondary {
        background-color: #e2e3e5;
        color: #41464b;
    }

    /* Icon wrapper variants */
    .icon-info {
        background-color: #e7f1ff;
        color: #0d6efd;
    }

    .icon-success {
        background-color: #d1e7dd;
        color: #198754;
    }

    .icon-warning {
        background-color: #fff3cd;
        color: #ffc107;
    }

    .icon-danger {
        background-color: #f8d7da;
        color: #dc3545;
    }

    .icon-dark {
        background-color: #d3d3d3;
        color: #212529;
    }

    .icon-secondary {
        background-color: #e2e3e5;
        color: #6c757d;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card soft-card rounded-5 mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h1 class="h4 section-title mb-1">Aktivitas / Notifikasi Terbaru</h1>
                <p class="text-muted mb-0">Riwayat terbaru terkait nilai, validasi, remedial, atau catatan dosen.</p>
            </div>
            <span class="badge bg-dark-subtle text-dark-emphasis rounded-pill px-3 py-2">
                <?= esc(count($notifications ?? [])) ?> notifikasi
            </span>
        </div>

        <?php if (($notifications ?? []) === []): ?>
            <div class="text-center py-5 empty-state">
                <i class="bi bi-bell-slash fs-1 d-block mb-3"></i>
                <p class="mb-1 fw-medium">Belum ada notifikasi.</p>
                <small class="text-muted">Notifikasi akan muncul ketika ada aktivitas terkait praktikum Anda.</small>
            </div>
        <?php else: ?>
            <!-- Filter tabs -->
            <div class="d-flex gap-2 mb-3 flex-wrap">
                <span class="badge bg-primary rounded-pill px-3 py-2">Semua</span>
                <span class="badge bg-light text-dark border rounded-pill px-3 py-2">Nilai</span>
                <span class="badge bg-light text-dark border rounded-pill px-3 py-2">Validasi</span>
                <span class="badge bg-light text-dark border rounded-pill px-3 py-2">Remedial</span>
                <span class="badge bg-light text-dark border rounded-pill px-3 py-2">Kehadiran</span>
            </div>

            <div class="row g-3">
                <?php foreach ($notifications as $row): ?>
                    <?php
                    $isUnread = !($row['is_read'] ?? true);
                    $badgeClass = match ($row['badge'] ?? 'secondary') {
                        'info'    => 'badge-notif-info',
                        'success' => 'badge-notif-success',
                        'warning' => 'badge-notif-warning',
                        'danger'  => 'badge-notif-danger',
                        'dark'    => 'badge-notif-dark',
                        default   => 'badge-notif-secondary',
                    };
                    $iconClass = match ($row['badge'] ?? 'secondary') {
                        'info'    => 'icon-info',
                        'success' => 'icon-success',
                        'warning' => 'icon-warning',
                        'danger'  => 'icon-danger',
                        'dark'    => 'icon-dark',
                        default   => 'icon-secondary',
                    };
                    ?>
                    <div class="col-12">
                        <div class="notif-item border rounded-4 p-3 h-100 bg-white <?= $isUnread ? 'notif-unread' : '' ?>">
                            <div class="d-flex align-items-start gap-3">
                                <!-- Icon -->
                                <div class="notif-icon-wrapper <?= $iconClass ?>">
                                    <i class="bi <?= esc($row['icon'] ?? 'bi-bell') ?> fs-5"></i>
                                </div>

                                <!-- Content -->
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-1">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="notif-category-badge badge bg-light text-muted border">
                                                <?= esc($row['category'] ?? 'Umum') ?>
                                            </span>
                                            <?php if ($isUnread): ?>
                                                <span class="badge bg-primary rounded-pill" style="font-size: 0.6rem;">Baru</span>
                                            <?php endif; ?>
                                            <?php if (($row['source'] ?? '') === 'activity'): ?>
                                                <span class="badge bg-light text-muted border" style="font-size: 0.6rem;">
                                                    <i class="bi bi-clock-history me-1"></i>Log
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="notif-time text-muted">
                                            <i class="bi bi-clock me-1"></i><?= esc($row['time']) ?>
                                        </span>
                                    </div>

                                    <div class="fw-semibold mb-1 text-truncate" title="<?= esc($row['title']) ?>">
                                        <?= esc($row['title']) ?>
                                    </div>
                                    <div class="text-muted small mb-2">
                                        <?= esc($row['message']) ?>
                                    </div>

                                    <!-- Footer info -->
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge rounded-pill px-2 py-1 <?= $badgeClass ?>">
                                            <?= esc(ucfirst($row['badge'] ?? 'info')) ?>
                                        </span>
                                        <?php if (($row['reference_type'] ?? '') !== ''): ?>
                                            <small class="text-muted">
                                                Ref: <?= esc($row['reference_type']) ?>
                                                <?= ($row['reference_id'] ?? '') !== '' ? '#' . esc($row['reference_id']) : '' ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>