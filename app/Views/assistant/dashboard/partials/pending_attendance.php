<div class="p-4 pb-0">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <h2 class="h4 section-title mb-1">Pertemuan yang Belum Diabsen</h2>
            <p class="text-muted mb-0">Sesi yang absensinya belum lengkap atau masih sebagian.</p>
        </div>
        <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-2"><?= esc(count($pendingAttendanceRows)) ?> sesi</span>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Mata Kuliah</th>
                <th>Kelas/Kelompok</th>
                <th>Pertemuan Ke</th>
                <th>Tanggal</th>
                <th>Jumlah Mahasiswa</th>
                <th>Absensi Terisi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($pendingAttendanceRows === []): ?>
                <tr>
                    <td colspan="8">
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-check2-circle fs-1 d-block mb-2"></i>
                            Semua pertemuan sudah memiliki absensi lengkap.
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pendingAttendanceRows as $row): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($row['course_name']) ?></td>
                        <td><?= esc($row['class_label']) ?></td>
                        <td><?= esc($row['meeting_no']) ?></td>
                        <td><?= esc($row['session_date']) ?></td>
                        <td><?= esc($row['student_count']) ?></td>
                        <td><?= esc($row['attendance_filled']) ?></td>
                        <td><span class="badge bg-<?= esc($row['status_badge']) ?>"><?= esc($row['status']) ?></span></td>
                        <td><a href="<?= esc($row['action_url']) ?>" class="btn btn-sm btn-outline-primary">Input Absensi</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>