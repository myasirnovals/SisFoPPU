<div class="p-4 pb-0">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <h2 class="h4 section-title mb-1">Komponen Nilai Belum Lengkap</h2>
            <p class="text-muted mb-0">Komponen atau sub-komponen yang masih memiliki mahasiswa belum dinilai.</p>
        </div>
        <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-2"><?= esc(count($incompleteScoreRows)) ?> item</span>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Mata Kuliah</th>
                <th>Kelas/Kelompok</th>
                <th>Komponen Nilai</th>
                <th>Sub-Komponen</th>
                <th>Mahasiswa Belum Dinilai</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($incompleteScoreRows === []): ?>
                <tr>
                    <td colspan="8">
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-clipboard-check fs-1 d-block mb-2"></i>
                            Semua komponen nilai sudah lengkap.
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($incompleteScoreRows as $row): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($row['course_name']) ?></td>
                        <td><?= esc($row['class_label']) ?></td>
                        <td><?= esc($row['component_name']) ?></td>
                        <td><?= esc($row['subcomponent_name']) ?></td>
                        <td><?= esc($row['students_pending']) ?></td>
                        <td><?= esc($row['deadline']) ?></td>
                        <td><span class="badge bg-<?= esc($row['status_badge']) ?>"><?= esc($row['status']) ?></span></td>
                        <td><a href="<?= esc($row['action_url']) ?>" class="btn btn-sm btn-outline-warning">Input Nilai</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>