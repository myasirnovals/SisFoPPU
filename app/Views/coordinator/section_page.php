<?= $this->extend('layout/coordinator_layout') ?>

<?= $this->section('styles') ?>
<style>
    .section-shell {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(148, 163, 184, 0.18);
        backdrop-filter: blur(8px);
    }

    .section-title {
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .table thead th {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #475569;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card section-shell rounded-4 mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-primary-subtle text-primary-emphasis">Koordinator Praktikum</span>
                    <span class="badge bg-light text-dark border"><?= esc($filters['academic_year'] ?: '2025/2026') ?></span>
                </div>
                <h1 class="h3 section-title mb-2"><?= esc($sectionTitle ?? $title) ?></h1>
                <p class="text-muted mb-0"><?= esc($sectionDescription ?? '') ?></p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= esc($backUrl) ?>" class="btn btn-outline-secondary">Kembali ke Ringkasan</a>
            </div>
        </div>
    </div>
</div>

<div class="card section-shell rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h2 class="h5 section-title mb-1">Filter Global</h2>
                <p class="text-muted mb-0">Filter yang sama dipakai lintas halaman agar konteks data tetap konsisten.</p>
            </div>
        </div>

        <form method="get" action="<?= current_url() ?>" class="row g-3">
            <div class="col-md-6 col-xl-2">
                <select name="academic_year" class="form-select">
                    <option value="">Tahun Akademik</option>
                    <?php foreach ($filterOptions['academic_years'] as $item): ?>
                        <option value="<?= esc($item) ?>" <?= $filters['academic_year'] === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-xl-2">
                <select name="semester" class="form-select">
                    <option value="">Semester</option>
                    <?php foreach ($filterOptions['semesters'] as $item): ?>
                        <option value="<?= esc($item) ?>" <?= $filters['semester'] === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-xl-2">
                <select name="study_program" class="form-select">
                    <option value="">Program Studi</option>
                    <?php foreach ($filterOptions['study_programs'] as $item): ?>
                        <option value="<?= esc($item) ?>" <?= $filters['study_program'] === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-xl-2">
                <select name="course_id" class="form-select">
                    <option value="">Mata Kuliah</option>
                    <?php foreach ($filterOptions['courses'] as $item): ?>
                        <option value="<?= esc($item['id']) ?>" <?= (string) $filters['course_id'] === (string) $item['id'] ? 'selected' : '' ?>><?= esc($item['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-xl-2">
                <select name="class_id" class="form-select">
                    <option value="">Kelas Praktikum</option>
                    <?php foreach ($filterOptions['classes'] as $item): ?>
                        <option value="<?= esc($item['id']) ?>" <?= (string) $filters['class_id'] === (string) $item['id'] ? 'selected' : '' ?>><?= esc($item['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-xl-2">
                <select name="lecturer" class="form-select">
                    <option value="">Dosen Pengampu</option>
                    <?php foreach ($filterOptions['lecturers'] as $item): ?>
                        <option value="<?= esc($item) ?>" <?= $filters['lecturer'] === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-xl-2">
                <select name="score_status" class="form-select">
                    <option value="">Status Nilai</option>
                    <?php foreach ($filterOptions['score_statuses'] as $item): ?>
                        <option value="<?= esc($item) ?>" <?= $filters['score_status'] === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                <a href="<?= esc($backUrl) ?>" class="btn btn-outline-secondary">Reset Filter</a>
            </div>
        </form>
    </div>
</div>

<?php if (isset($stats)): ?>
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3"><div class="card section-shell rounded-4 h-100"><div class="card-body p-4"><div class="text-muted small mb-1">Eligible</div><div class="h2 mb-0"><?= esc($stats['eligible']) ?></div></div></div></div>
    <div class="col-12 col-sm-6 col-xl-3"><div class="card section-shell rounded-4 h-100"><div class="card-body p-4"><div class="text-muted small mb-1">Terdaftar</div><div class="h2 mb-0"><?= esc($stats['registered']) ?></div></div></div></div>
    <div class="col-12 col-sm-6 col-xl-3"><div class="card section-shell rounded-4 h-100"><div class="card-body p-4"><div class="text-muted small mb-1">Sudah Dinilai</div><div class="h2 mb-0"><?= esc($stats['graded']) ?></div></div></div></div>
    <div class="col-12 col-sm-6 col-xl-3"><div class="card section-shell rounded-4 h-100"><div class="card-body p-4"><div class="text-muted small mb-1">Tidak Mengikuti</div><div class="h2 mb-0"><?= esc($stats['not_attended']) ?></div></div></div></div>
</div>
<?php endif; ?>

<div class="card section-shell rounded-4">
    <div class="card-body p-0">
        <div class="p-4 pb-0 d-flex justify-content-between align-items-start gap-3">
            <div>
                <h2 class="h5 section-title mb-1"><?= esc($sectionTitle ?? $title) ?></h2>
                <p class="text-muted mb-0"><?= esc($sectionDescription ?? '') ?></p>
            </div>
            <span class="badge bg-secondary rounded-pill px-3 py-2"><?= esc($totalRows) ?> data</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <?php if ($sectionType === 'classes'): ?>
                        <tr>
                            <th>Mata Kuliah</th>
                            <th>Kelas</th>
                            <th>Dosen Pengampu</th>
                            <th>Jumlah Mahasiswa</th>
                            <th>Komponen Lengkap</th>
                            <th>Komponen Belum Lengkap</th>
                            <th>Progress</th>
                            <th>Status Nilai</th>
                            <th>Aksi</th>
                        </tr>
                    <?php elseif ($sectionType === 'attention'): ?>
                        <tr>
                            <th>Mata Kuliah</th>
                            <th>Kelas</th>
                            <th>Masalah</th>
                            <th>Prioritas</th>
                            <th>Deadline</th>
                            <th>Aksi</th>
                        </tr>
                    <?php elseif ($sectionType === 'remedial'): ?>
                        <tr>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Mata Kuliah</th>
                            <th>Kelas</th>
                            <th>Nilai Akhir</th>
                            <th>Huruf Mutu</th>
                            <th>Alasan Remedial</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    <?php elseif ($sectionType === 'validation'): ?>
                        <tr>
                            <th>Mata Kuliah</th>
                            <th>Kelas</th>
                            <th>Dosen Pengampu</th>
                            <th>Status Input</th>
                            <th>Status Validasi</th>
                            <th>Submit</th>
                            <th>Validasi</th>
                            <th>Revisi</th>
                            <th>Aksi</th>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Aktivitas</th>
                            <th>Modul</th>
                            <th>Keterangan</th>
                        </tr>
                    <?php endif; ?>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="<?= $sectionType === 'classes' ? 9 : ($sectionType === 'attention' ? 6 : ($sectionType === 'remedial' ? 9 : ($sectionType === 'validation' ? 9 : 6))) ?>">
                                <div class="text-center py-5 text-muted">Tidak ada data untuk filter yang dipilih.</div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                            <?php if ($sectionType === 'classes'): ?>
                                <tr>
                                    <td class="fw-semibold"><?= esc($row['course_name']) ?></td>
                                    <td><span class="badge bg-light text-dark border">Kelas <?= esc($row['class_name']) ?></span></td>
                                    <td><?= esc($row['lecturer_name']) ?></td>
                                    <td><?= esc($row['student_count']) ?></td>
                                    <td><?= esc($row['complete_components']) ?></td>
                                    <td><?= esc($row['incomplete_components']) ?></td>
                                    <td style="min-width: 180px;">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar <?= $row['progress_percent'] === 100 ? 'bg-success' : 'bg-primary' ?>" style="width: <?= esc($row['progress_percent']) ?>%;">
                                                <?= esc($row['progress_percent']) ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge <?= esc($row['badge_class']) ?>"><?= esc($row['status']) ?></span></td>
                                    <td><a href="<?= esc($row['detail_url']) ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a></td>
                                </tr>
                            <?php elseif ($sectionType === 'attention'): ?>
                                <tr>
                                    <td><?= esc($row['course_name']) ?></td>
                                    <td><span class="badge bg-light text-dark border">Kelas <?= esc($row['class_name']) ?></span></td>
                                    <td style="max-width: 320px;"><span class="text-muted small"><?= esc($row['problem']) ?></span></td>
                                    <td><span class="badge <?= esc($row['priority_badge_class']) ?>"><?= esc($row['priority']) ?></span></td>
                                    <td><?= esc($row['deadline']) ?></td>
                                    <td><a href="<?= esc($row['detail_url']) ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a></td>
                                </tr>
                            <?php elseif ($sectionType === 'remedial'): ?>
                                <tr>
                                    <td><?= esc($row['nim']) ?></td>
                                    <td class="fw-semibold"><?= esc($row['student_name']) ?></td>
                                    <td><?= esc($row['course_name']) ?></td>
                                    <td><?= esc($row['class_name']) ?></td>
                                    <td><?= esc($row['final_score']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= esc($row['grade']) ?></span></td>
                                    <td style="max-width: 260px;"><span class="text-muted small"><?= esc($row['reason']) ?></span></td>
                                    <td><span class="badge <?= esc($row['badge_class']) ?>"><?= esc($row['status']) ?></span></td>
                                    <td><a href="<?= esc($row['detail_url']) ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a></td>
                                </tr>
                            <?php elseif ($sectionType === 'validation'): ?>
                                <tr>
                                    <td><?= esc($row['course_name']) ?></td>
                                    <td><?= esc($row['class_name']) ?></td>
                                    <td><?= esc($row['lecturer_name']) ?></td>
                                    <td><span class="badge <?= esc($row['badge_class']) ?>"><?= esc($row['score_status']) ?></span></td>
                                    <td><span class="badge <?= esc($row['badge_class']) ?>"><?= esc($row['validation_status']) ?></span></td>
                                    <td><?= esc($row['submit_date']) ?></td>
                                    <td><?= esc($row['validation_date']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= esc($row['revision_count']) ?></span></td>
                                    <td><a href="<?= esc($row['detail_url']) ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td class="text-nowrap"><?= esc($row['time']) ?></td>
                                    <td><?= esc($row['user']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= esc($row['role']) ?></span></td>
                                    <td><?= esc($row['activity']) ?></td>
                                    <td><span class="badge bg-primary-subtle text-primary-emphasis"><?= esc($row['module']) ?></span></td>
                                    <td style="max-width: 220px;"><span class="text-muted small"><?= esc($row['detail']) ?></span></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="p-4 pt-3 border-top bg-white rounded-bottom-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted">Menampilkan <?= esc(count($rows)) ?> dari <?= esc($pagination['total']) ?> data</small>
                <div class="btn-group">
                    <?php $queryParams = $filters + ['page' => max(1, $pagination['page'] - 1)]; ?>
                    <a class="btn btn-outline-secondary btn-sm <?= $pagination['page'] <= 1 ? 'disabled' : '' ?>" href="<?= esc(current_url() . '?' . http_build_query($queryParams)) ?>">Sebelumnya</a>
                    <span class="btn btn-outline-secondary btn-sm disabled">Halaman <?= esc($pagination['page']) ?> / <?= esc($pagination['totalPages']) ?></span>
                    <?php $queryParamsNext = $filters + ['page' => min($pagination['totalPages'], $pagination['page'] + 1)]; ?>
                    <a class="btn btn-outline-secondary btn-sm <?= $pagination['page'] >= $pagination['totalPages'] ? 'disabled' : '' ?>" href="<?= esc(current_url() . '?' . http_build_query($queryParamsNext)) ?>">Berikutnya</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
