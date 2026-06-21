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
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card soft-card rounded-5 mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h1 class="h4 section-title mb-1">Aktivitas / Notifikasi Terbaru</h1>
                <p class="text-muted mb-0">Riwayat terbaru terkait nilai, validasi, remedial, atau catatan dosen.</p>
            </div>
            <span class="badge bg-dark-subtle text-dark-emphasis rounded-pill px-3 py-2"><?= esc(count($notifications ?? [])) ?> notifikasi</span>
        </div>
        <?php if (($notifications ?? []) === []): ?>
            <div class="text-center py-4 empty-state">
                <i class="bi bi-bell fs-1 d-block mb-2"></i>
                Belum ada notifikasi.
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($notifications as $row): ?>
                    <div class="col-12 col-lg-6">
                        <div class="border rounded-4 p-3 h-100 bg-white">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                <div class="fw-semibold"><?= esc($row['title']) ?></div>
                                <span class="badge bg-<?= esc($row['badge']) ?>"><?= esc($row['time']) ?></span>
                            </div>
                            <div class="text-muted small"><?= esc($row['message']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>