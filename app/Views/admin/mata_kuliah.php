<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-x-circle me-1"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 text-gray-800">Manajemen Mata Kuliah Praktikum</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahDataModal">
        <i class="bi bi-plus-circle me-1"></i> Tambah Mata Kuliah
    </button>
</div>

<!-- Tabel -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Kode MK</th>
                        <th>Nama Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Semester</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($matakuliah)): ?>
                        <?php foreach ($matakuliah as $i => $mk): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($mk['kode_mk']) ?></td>
                                <td><?= esc($mk['nama_mk']) ?></td>
                                <td><?= esc($mk['sks']) ?></td>
                                <td>
                                    <span class="badge <?= $mk['semester'] === 'Ganjil' ? 'bg-primary' : 'bg-success' ?>">
                                        <?= esc($mk['semester']) ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Tombol Edit -->
                                    <button class="btn btn-sm btn-warning btn-edit"
                                        title="Edit"
                                        data-id="<?= $mk['id'] ?>"
                                        data-kode="<?= esc($mk['kode_mk']) ?>"
                                        data-nama="<?= esc($mk['nama_mk']) ?>"
                                        data-sks="<?= esc($mk['sks']) ?>"
                                        data-semester="<?= esc($mk['semester']) ?>">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <button class="btn btn-sm btn-danger btn-hapus"
                                        title="Hapus"
                                        data-id="<?= $mk['id'] ?>"
                                        data-nama="<?= esc($mk['nama_mk']) ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada data mata kuliah.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════════════
     MODAL: TAMBAH
════════════════════════════════════════════ -->
<div class="modal fade" id="tambahDataModal" tabindex="-1" aria-labelledby="tambahDataModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tambahDataModalLabel">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Mata Kuliah
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/matakuliah/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kode_mk" class="form-label">Kode Mata Kuliah</label>
                        <input type="text" class="form-control" id="kode_mk" name="kode_mk"
                            placeholder="Contoh: IF1234"
                            value="<?= old('kode_mk') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_mk" class="form-label">Nama Mata Kuliah</label>
                        <input type="text" class="form-control" id="nama_mk" name="nama_mk"
                            placeholder="Contoh: Praktikum Pemrograman Web"
                            value="<?= old('nama_mk') ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sks" class="form-label">Bobot SKS</label>
                            <input type="number" class="form-control" id="sks" name="sks"
                                min="1" max="4"
                                value="<?= old('sks') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="semester" class="form-label">Semester</label>
                            <select class="form-select" id="semester" name="semester" required>
                                <option value="">Pilih Semester...</option>
                                <option value="Ganjil" <?= old('semester') === 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                                <option value="Genap" <?= old('semester') === 'Genap'  ? 'selected' : '' ?>>Genap</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════════════
     MODAL: EDIT
════════════════════════════════════════════ -->
<div class="modal fade" id="editDataModal" tabindex="-1" aria-labelledby="editDataModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editDataModalLabel">
                    <i class="bi bi-pencil-square me-1"></i> Edit Mata Kuliah
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" action="" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_kode_mk" class="form-label">Kode Mata Kuliah</label>
                        <input type="text" class="form-control" id="edit_kode_mk" name="kode_mk"
                            placeholder="Contoh: IF1234" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama_mk" class="form-label">Nama Mata Kuliah</label>
                        <input type="text" class="form-control" id="edit_nama_mk" name="nama_mk"
                            placeholder="Contoh: Praktikum Pemrograman Web" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_sks" class="form-label">Bobot SKS</label>
                            <input type="number" class="form-control" id="edit_sks" name="sks"
                                min="1" max="4" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_semester" class="form-label">Semester</label>
                            <select class="form-select" id="edit_semester" name="semester" required>
                                <option value="">Pilih Semester...</option>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark">
                        <i class="bi bi-save me-1"></i> Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════════════
     MODAL: KONFIRMASI HAPUS
════════════════════════════════════════════ -->
<div class="modal fade" id="hapusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-1"></i> Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus mata kuliah <strong id="hapus_nama"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="hapus_link" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash me-1"></i> Hapus
                </a>
            </div>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════════════
     JAVASCRIPT
════════════════════════════════════════════ -->
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ── Buka modal EDIT dan isi data ──────────────────────
        document.querySelectorAll('.btn-edit').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const kode = this.dataset.kode;
                const nama = this.dataset.nama;
                const sks = this.dataset.sks;
                const semester = this.dataset.semester;

                document.getElementById('edit_kode_mk').value = kode;
                document.getElementById('edit_nama_mk').value = nama;
                document.getElementById('edit_sks').value = sks;

                // Set semester dropdown
                const semesterEl = document.getElementById('edit_semester');
                for (let opt of semesterEl.options) {
                    opt.selected = (opt.value === semester);
                }

                // Set action form
                document.getElementById('formEdit').action =
                    `<?= base_url('admin/matakuliah/update') ?>/${id}`;

                // Tampilkan modal
                new bootstrap.Modal(document.getElementById('editDataModal')).show();
            });
        });

        // ── Buka modal HAPUS ─────────────────────────────────
        document.querySelectorAll('.btn-hapus').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const nama = this.dataset.nama;

                document.getElementById('hapus_nama').textContent = nama;
                document.getElementById('hapus_link').href =
                    `<?= base_url('admin/matakuliah/delete') ?>/${id}`;

                new bootstrap.Modal(document.getElementById('hapusModal')).show();
            });
        });

        // ── Auto-buka modal jika ada validasi error (redirect back) ──
        <?php if (session()->getFlashdata('open_modal') === 'tambah'): ?>
            new bootstrap.Modal(document.getElementById('tambahDataModal')).show();
        <?php endif; ?>

        <?php if (session()->getFlashdata('open_modal') === 'edit'): ?>
            // Untuk kasus edit error — bisa dikembangkan lebih lanjut
        <?php endif; ?>

    });
</script>

<?= $this->endSection() ?>