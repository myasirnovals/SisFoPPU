<div class="p-4 pb-0">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <h2 class="h4 section-title mb-1">Mahasiswa Tanpa Nilai / Nilai Kosong</h2>
            <p class="text-muted mb-0">Menampilkan nilai null, nilai 0, dan data tidak valid secara terpisah.</p>
        </div>
        <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-3 py-2"><?= esc(count($missingScoreRows)) ?> baris</span>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Mata Kuliah</th>
                <th>Kelas/Kelompok</th>
                <th>Komponen Nilai</th>
                <th>Sub-Komponen</th>
                <th>Status Nilai</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($missingScoreRows === []): ?>
                <tr>
                    <td colspan="8">
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle fs-1 d-block mb-2"></i>
                            Tidak ada nilai kosong pada kelas yang Anda tangani.
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($missingScoreRows as $row): ?>
                    <tr>
                        <td><?= esc($row['student_number']) ?></td>
                        <td class="fw-semibold"><?= esc($row['student_name']) ?></td>
                        <td><?= esc($row['course_name']) ?></td>
                        <td><?= esc($row['class_label']) ?></td>
                        <td><?= esc($row['component_name']) ?></td>
                        <td><?= esc($row['subcomponent_name']) ?></td>
                        <td><span class="badge bg-<?= esc($row['status_badge']) ?>"><?= esc($row['status']) ?></span></td>
                        <td><a href="<?= esc($row['action_url']) ?>" class="btn btn-sm btn-outline-warning">Input Nilai</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>