<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 text-gray-800">Manajemen Data Akademik</h2>
</div>

<?php if (session()->getFlashdata('msg')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('msg') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('err')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('err') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white pt-3 pb-0">
        <ul class="nav nav-tabs border-bottom-0" id="akademikTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="prodi-tab" data-bs-toggle="tab" data-bs-target="#prodi" type="button" role="tab">Program Studi</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="tahun-tab" data-bs-toggle="tab" data-bs-target="#tahun" type="button" role="tab">Tahun Akademik</button>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="akademikTabsContent">

            <div class="tab-pane fade show active" id="prodi" role="tabpanel">
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#tambahProdiModal">
                        <i class="bi bi-plus-circle"></i> Tambah Prodi
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Kode Prodi</th>
                                <th>Nama Program Studi</th>
                                <th>Jenjang</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($prodi)) : ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Belum ada data Program Studi.</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($prodi as $p) : ?>
                                    <tr>
                                        <td class="fw-bold"><?= esc($p['program_code']) ?></td>
                                        <td><?= esc($p['program_name']) ?></td>
                                        <td><?= esc($p['degree_level']) ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-warning btn-edit me-1"
                                                data-id="<?= $p['id'] ?>"
                                                data-code="<?= esc($p['program_code']) ?>"
                                                data-name="<?= esc($p['program_name']) ?>"
                                                data-level="<?= esc($p['degree_level']) ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editProdiModal">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <form action="<?= base_url('admin/akademik/prodi/delete/' . $p['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Program Studi ini?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tahun" role="tabpanel">
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#tambahTahunModal">
                        <i class="bi bi-plus-circle"></i> Tambah Tahun Akademik
                    </button>
                </div>
                <div class="alert alert-info py-2">
                    <i class="bi bi-info-circle me-2"></i> Hanya boleh ada <strong>satu</strong> Tahun Akademik yang berstatus Aktif.
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Tahun Akademik & Semester</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 200px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tahun)) : ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">Belum ada data Tahun Akademik.</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($tahun as $t) : ?>
                                    <tr>
                                        <td class="fw-bold"><?= esc($t['year_code']) ?></td>
                                        <td>
                                            <?php if ($t['is_active'] == 1) : ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else : ?>
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($t['is_active'] == 1) : ?>
                                                <button class="btn btn-sm btn-secondary me-1" disabled>Sedang Aktif</button>
                                            <?php else : ?>
                                                <form action="<?= base_url('admin/akademik/tahun/set-active/' . $t['id']) ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-success me-1">Set Aktif</button>
                                                </form>
                                            <?php endif; ?>

                                            <form action="<?= base_url('admin/akademik/tahun/delete/' . $t['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus tahun akademik <?= esc($t['year_code']) ?>?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                            </form>
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
</div>

<div class="modal fade" id="tambahProdiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Program Studi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/akademik/prodi/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kode Prodi <span class="text-danger">*</span></label>
                        <input type="text" name="program_code" class="form-control" placeholder="Misal: IF" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Program Studi <span class="text-danger">*</span></label>
                        <input type="text" name="program_name" class="form-control" placeholder="Misal: Informatika" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Jenjang <span class="text-danger">*</span></label>
                        <select name="degree_level" class="form-select" required>
                            <option value="S1">S1</option>
                            <option value="D3">D3</option>
                            <option value="D4">D4</option>
                            <option value="S2">S2</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editProdiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">Edit Program Studi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/akademik/prodi/update') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kode Prodi <span class="text-danger">*</span></label>
                        <input type="text" name="program_code" id="edit_code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Program Studi <span class="text-danger">*</span></label>
                        <input type="text" name="program_name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Jenjang <span class="text-danger">*</span></label>
                        <select name="degree_level" id="edit_level" class="form-select" required>
                            <option value="S1">S1</option>
                            <option value="D3">D3</option>
                            <option value="D4">D4</option>
                            <option value="S2">S2</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahTahunModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Tahun Akademik</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/akademik/tahun/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tahun Akademik <span class="text-danger">*</span></label>
                        <input type="text" name="tahun" class="form-control" placeholder="Contoh: 2024/2025" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Semester <span class="text-danger">*</span></label>
                        <select name="semester" class="form-select" required>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_id').value = this.getAttribute('data-id');
                document.getElementById('edit_code').value = this.getAttribute('data-code');
                document.getElementById('edit_name').value = this.getAttribute('data-name');
                document.getElementById('edit_level').value = this.getAttribute('data-level');
            });
        });
    });
</script>

<?= $this->endSection() ?>