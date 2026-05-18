<div class="p-4 pb-0">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <h2 class="h4 section-title mb-1">Widget Remedial</h2>
            <p class="text-muted mb-0">Eligible, terdaftar, dijadwalkan, dan status remedial lainnya.</p>
        </div>
        <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-3 py-2"><?= esc(count($remedialRows)) ?> data</span>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Mata Kuliah</th>
                <th>Kelas</th>
                <th>Nilai Akhir Sementara</th>
                <th>Huruf Mutu</th>
                <th>Status Remedial</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($remedialRows === []): ?>
                <tr>
                    <td colspan="8">
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-heart-pulse fs-1 d-block mb-2"></i>
                            Belum ada mahasiswa remedial.
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($remedialRows as $row): ?>
                    <tr>
                        <td><?= esc($row['student_number']) ?></td>
                        <td class="fw-semibold"><?= esc($row['student_name']) ?></td>
                        <td><?= esc($row['course_name']) ?></td>
                        <td><?= esc($row['class_name']) ?></td>
                        <td><?= $row['final_score'] === null ? '-' : esc(number_format((float) $row['final_score'], 2)) ?></td>
                        <td><?= esc($row['grade_letter']) ?></td>
                        <td><span class="badge bg-<?= esc($row['status_badge']) ?>"><?= esc($row['status']) ?></span></td>
                        <td><a href="<?= esc($row['action_url']) ?>" class="btn btn-sm btn-outline-danger">Detail</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>