<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 text-gray-800">Manajemen Data Akademik</h2>
</div>

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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>IF</td>
                                <td>Informatika</td>
                                <td>S1</td>
                                <td>
                                    <button class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
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
                                <th>Tahun Akademik</th>
                                <th>Semester</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2023/2024</td>
                                <td>Ganjil</td>
                                <td><span class="badge bg-success">Aktif</span></td>
                                <td>
                                    <button class="btn btn-sm btn-secondary" disabled>Set Aktif</button>
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>2022/2023</td>
                                <td>Genap</td>
                                <td><span class="badge bg-secondary">Tidak Aktif</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-success">Set Aktif</button>
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
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
            <div class="modal-body">
                <input type="text" class="form-control mb-2" placeholder="Kode (Misal: IF)">
                <input type="text" class="form-control mb-2" placeholder="Nama Prodi (Misal: Informatika)">
                <select class="form-select">
                    <option>S1</option>
                    <option>D3</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Simpan</button>
            </div>
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
            <div class="modal-body">
                <input type="text" class="form-control mb-2" placeholder="Contoh: 2023/2024">
                <select class="form-select mb-2">
                    <option>Ganjil</option>
                    <option>Genap</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>