<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 text-gray-800">Pengaturan Huruf Mutu</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahHurufModal">
        <i class="bi bi-plus-circle me-1"></i> Tambah Rentang Nilai
    </button>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary">Daftar Rentang Huruf Mutu (Default)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Huruf Mutu</th>
                                <th>Batas Bawah (≥)</th>
                                <th>Batas Atas (≤)</th>
                                <th>Angka Mutu</th>
                                <th>Status Kelulusan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($gradeScales)): ?>
                                <tr>
                                    <td colspan="6" class="text-muted">Tidak ada data.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($gradeScales as $gs):
                                    $badgeClass = match (true) {
                                        $gs['grade_letter'] === 'A' => 'bg-success',
                                        $gs['grade_letter'] === 'B' => 'bg-primary',
                                        $gs['grade_letter'] === 'C' => 'bg-info',
                                        $gs['grade_letter'] === 'D' => 'bg-warning',
                                        default => 'bg-danger'
                                    };
                                ?>
                                    <tr>
                                        <td><span class="badge <?= $badgeClass ?> fs-6"><?= esc($gs['grade_letter']) ?></span></td>
                                        <td><?= $gs['min_score'] ?></td>
                                        <td><?= $gs['max_score'] ?></td>
                                        <td><?= $gs['grade_point'] ?></td>
                                        <td>
                                            <?php if ($gs['is_passing']): ?>
                                                <span class="text-success fw-bold"><i class="bi bi-check-circle"></i> Lulus</span>
                                            <?php else: ?>
                                                <span class="text-danger fw-bold"><i class="bi bi-x-circle"></i> Tidak Lulus</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning btn-edit"
                                                data-id="<?= $gs['id'] ?>"
                                                data-grade_letter="<?= esc($gs['grade_letter']) ?>"
                                                data-min_score="<?= $gs['min_score'] ?>"
                                                data-max_score="<?= $gs['max_score'] ?>"
                                                data-grade_point="<?= $gs['grade_point'] ?>"
                                                data-is_passing="<?= $gs['is_passing'] ?>"
                                                data-predicate="<?= esc($gs['predicate'] ?? '') ?>"
                                                data-bs-toggle="modal" data-bs-target="#editHurufModal">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                data-id="<?= $gs['id'] ?>"
                                                data-grade_letter="<?= esc($gs['grade_letter']) ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-light border-0">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-exclamation-triangle text-warning"></i> Perhatian</h5>
                <p class="small text-muted mb-2">Pastikan rentang nilai tidak bertabrakan (overlap) satu sama lain.</p>
                <p class="small text-muted mb-0">Aturan ini akan digunakan sebagai konversi otomatis saat Dosen dan Asisten selesai menginput nilai angka mahasiswa.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahHurufModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/hurufmutu/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah Rentang Huruf Mutu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Huruf Mutu (Indeks)</label>
                        <input type="text" name="grade_letter" class="form-control" placeholder="Contoh: A, A-, B+, dst" value="<?= old('grade_letter') ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Batas Bawah</label>
                            <input type="number" step="0.01" name="min_score" class="form-control" placeholder="0" value="<?= old('min_score') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Batas Atas</label>
                            <input type="number" step="0.01" name="max_score" class="form-control" placeholder="100" value="<?= old('max_score') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Angka Mutu</label>
                        <input type="number" step="0.01" name="grade_point" class="form-control" placeholder="Contoh: 4.00" value="<?= old('grade_point') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Predikat</label>
                        <input type="text" name="predicate" class="form-control" placeholder="Contoh: Sangat Baik" value="<?= old('predicate') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status Kelulusan</label>
                        <select class="form-select" name="is_passing" required>
                            <option value="1" <?= old('is_passing') === '1' ? 'selected' : '' ?>>Lulus</option>
                            <option value="0" <?= old('is_passing') === '0' ? 'selected' : '' ?>>Tidak Lulus (Wajib Mengulang)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editHurufModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" method="post" id="formEdit">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Edit Rentang Huruf Mutu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Huruf Mutu (Indeks)</label>
                        <input type="text" name="grade_letter" id="edit_grade_letter" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Batas Bawah</label>
                            <input type="number" step="0.01" name="min_score" id="edit_min_score" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Batas Atas</label>
                            <input type="number" step="0.01" name="max_score" id="edit_max_score" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Angka Mutu</label>
                        <input type="number" step="0.01" name="grade_point" id="edit_grade_point" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Predikat</label>
                        <input type="text" name="predicate" id="edit_predicate" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status Kelulusan</label>
                        <select class="form-select" name="is_passing" id="edit_is_passing" required>
                            <option value="1">Lulus</option>
                            <option value="0">Tidak Lulus (Wajib Mengulang)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Form Delete (hidden) -->
<form action="" method="post" id="formDelete" class="d-none">
    <?= csrf_field() ?>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tombol Edit
        document.querySelectorAll('.btn-edit').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('formEdit').action = '<?= base_url('admin/hurufmutu/update/') ?>' + id;
                document.getElementById('edit_grade_letter').value = this.getAttribute('data-grade_letter');
                document.getElementById('edit_min_score').value = this.getAttribute('data-min_score');
                document.getElementById('edit_max_score').value = this.getAttribute('data-max_score');
                document.getElementById('edit_grade_point').value = this.getAttribute('data-grade_point');
                document.getElementById('edit_predicate').value = this.getAttribute('data-predicate');
                document.getElementById('edit_is_passing').value = this.getAttribute('data-is_passing');
            });
        });

        // Tombol Delete
        document.querySelectorAll('.btn-delete').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const letter = this.getAttribute('data-grade_letter');
                if (confirm('Apakah Anda yakin ingin menghapus huruf mutu "' + letter + '"?')) {
                    const form = document.getElementById('formDelete');
                    form.action = '<?= base_url('admin/hurufmutu/delete/') ?>' + id;
                    form.submit();
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>