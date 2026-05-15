<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 text-gray-800">Manajemen Pengguna</h2>
    <div>
        <button type="button" class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#tambahPenggunaModal">
            <i class="bi bi-person-plus me-1"></i> Tambah Manual
        </button>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importExcelModal">
            <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
        </button>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white pt-3 pb-0">
        <ul class="nav nav-tabs border-bottom-0" id="penggunaTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="mahasiswa-tab" data-bs-toggle="tab" data-bs-target="#mahasiswa" type="button" role="tab">Mahasiswa</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="dosen-tab" data-bs-toggle="tab" data-bs-target="#dosen" type="button" role="tab">Dosen & Asisten</button>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="penggunaTabsContent">
            <div class="tab-pane fade show active" id="mahasiswa" role="tabpanel">
                <div class="table-responsive mt-2">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Program Studi</th>
                                <th>Angkatan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>3411201001</td>
                                <td>Ahmad Fathoni</td>
                                <td>Informatika</td>
                                <td>2020</td>
                                <td><span class="badge bg-success">Aktif</span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>3411201002</td>
                                <td>Budi Santoso</td>
                                <td>Informatika</td>
                                <td>2020</td>
                                <td><span class="badge bg-success">Aktif</span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="dosen" role="tabpanel">
                <div class="alert alert-info mt-2">
                    <i class="bi bi-info-circle me-2"></i> Di sini nantinya akan muncul daftar Dosen dan Asisten Praktikum.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importExcelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-file-earmark-excel"></i> Import Data Pengguna</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">1. Unduh Template</label>
                    <p class="small text-muted mb-1">Gunakan template Excel yang sudah disediakan agar format kolom sesuai.</p>
                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-download me-1"></i> Download Template.xlsx</button>
                </div>
                <hr>
                <div class="mb-3">
                    <label for="fileExcel" class="form-label fw-bold">2. Upload File Excel</label>
                    <input class="form-control" type="file" id="fileExcel" accept=".xlsx, .xls">
                </div>
                <div class="alert alert-warning d-none" id="previewArea">
                    Preview data akan muncul di sini sebelum disimpan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success">Import Sekarang</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>