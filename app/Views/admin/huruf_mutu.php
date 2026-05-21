<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 text-gray-800">Pengaturan Huruf Mutu</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahHurufModal">
        <i class="bi bi-plus-circle me-1"></i> Tambah Rentang Nilai
    </button>
</div>

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
                                <th>Status Kelulusan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-success fs-6">A</span></td>
                                <td>80</td>
                                <td>100</td>
                                <td><span class="text-success fw-bold"><i class="bi bi-check-circle"></i> Lulus</span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-primary fs-6">B</span></td>
                                <td>70</td>
                                <td>79.99</td>
                                <td><span class="text-success fw-bold"><i class="bi bi-check-circle"></i> Lulus</span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-danger fs-6">E</span></td>
                                <td>0</td>
                                <td>49.99</td>
                                <td><span class="text-danger fw-bold"><i class="bi bi-x-circle"></i> Tidak Lulus</span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
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

<div class="modal fade" id="tambahHurufModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Rentang Huruf Mutu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Huruf Mutu (Indeks)</label>
                    <input type="text" class="form-control" placeholder="Contoh: A, A-, B+, dst" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Batas Bawah</label>
                        <input type="number" step="0.01" class="form-control" placeholder="0" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Batas Atas</label>
                        <input type="number" step="0.01" class="form-control" placeholder="100" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status Kelulusan</label>
                    <select class="form-select">
                        <option value="1">Lulus</option>
                        <option value="0">Tidak Lulus (Wajib Mengulang)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>