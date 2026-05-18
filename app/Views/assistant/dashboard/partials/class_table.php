<div class="p-4 pb-0">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <h2 class="h4 section-title mb-1">Kelas / Kelompok yang Ditangani</h2>
            <p class="text-muted mb-0">Daftar kelas, progress absensi, progress nilai, dan status kelas.</p>
        </div>
        <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2"><?= esc(count($classRows)) ?> data</span>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Mata Kuliah Praktikum</th>
                <th>Kode</th>
                <th>Kelas</th>
                <th>Kelompok</th>
                <th>Mahasiswa</th>
                <th>Pertemuan</th>
                <th>Progress Absensi</th>
                <th>Progress Nilai</th>
                <th>Status Kelas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($classRows === []): ?>
                <tr>
                    <td colspan="11">
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada kelas praktikum yang ditugaskan.
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($classRows as $index => $row): ?>
                    <tr>
                        <td><?= esc($index + 1) ?></td>
                        <td class="fw-semibold"><?= esc($row['course_name']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= esc($row['course_code']) ?></span></td>
                        <td><?= esc($row['class_name']) ?></td>
                        <td><?= esc($row['group_name'] ?? '-') ?></td>
                        <td><?= esc($row['student_count']) ?></td>
                        <td><?= esc($row['session_count']) ?></td>
                        <td style="min-width: 170px;">
                            <div class="progress" style="height: 18px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: <?= esc($row['attendance_progress']) ?>%;" aria-valuenow="<?= esc($row['attendance_progress']) ?>" aria-valuemin="0" aria-valuemax="100"><?= esc($row['attendance_progress']) ?>%</div>
                            </div>
                            <div class="small text-muted mt-1"><?= esc($row['attendance_status']) ?></div>
                        </td>
                        <td style="min-width: 170px;">
                            <div class="progress" style="height: 18px;">
                                <div class="progress-bar bg-warning text-dark" role="progressbar" style="width: <?= esc($row['score_progress']) ?>%;" aria-valuenow="<?= esc($row['score_progress']) ?>" aria-valuemin="0" aria-valuemax="100"><?= esc($row['score_progress']) ?>%</div>
                            </div>
                            <div class="small text-muted mt-1"><?= esc($row['score_status']) ?></div>
                        </td>
                        <td><span class="badge bg-<?= esc($row['status_badge']) ?>"><?= esc($row['status']) ?></span></td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="<?= esc($row['detail_url']) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                <a href="<?= esc($row['attendance_url']) ?>" class="btn btn-sm btn-outline-info <?= $row['can_input_attendance'] ? '' : 'disabled' ?>">Input Kehadiran</a>
                                <a href="<?= esc($row['score_url']) ?>" class="btn btn-sm btn-outline-warning <?= $row['can_input_score'] ? '' : 'disabled' ?>">Input Nilai</a>
                                <a href="<?= esc($row['recap_url']) ?>" class="btn btn-sm btn-outline-secondary">Rekap</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>