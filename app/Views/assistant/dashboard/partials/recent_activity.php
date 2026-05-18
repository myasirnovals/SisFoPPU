<div class="p-4 pb-0">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <h2 class="h4 section-title mb-1">Aktivitas Terbaru</h2>
            <p class="text-muted mb-0">Riwayat aksi terakhir yang relevan dengan akun Anda.</p>
        </div>
        <span class="badge bg-dark-subtle text-dark-emphasis rounded-pill px-3 py-2"><?= esc(count($activityRows)) ?> log</span>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Waktu</th>
                <th>Aktivitas</th>
                <th>Mata Kuliah/Kelas</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($activityRows === []): ?>
                <tr>
                    <td colspan="4">
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                            Belum ada aktivitas terbaru.
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($activityRows as $row): ?>
                    <tr>
                        <td><?= esc($row['time']) ?></td>
                        <td class="fw-semibold"><?= esc($row['activity']) ?></td>
                        <td><?= esc($row['context']) ?></td>
                        <td><span class="badge bg-secondary"><?= esc($row['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>