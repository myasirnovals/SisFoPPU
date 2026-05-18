<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 text-gray-800">Manajemen Kelas Praktikum</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahKelasModal">
        <i class="bi bi-plus-circle me-1"></i> Buat Kelas Baru
    </button>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nama Kelas</th>
                        <th>Mata Kuliah</th>
                        <th>Dosen Pengampu</th>
                        <th>Jml Mahasiswa</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>IF-2A</strong></td>
                        <td>Praktikum Pemrograman Web</td>
                        <td>Dr. Budi (Dosen) <br> <small class="text-muted">Siti (Asisten)</small></td>
                        <td>40 / 40</td>
                        <td><span class="badge bg-success">Aktif</span></td>
                        <td>
                            <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#kelolaAnggotaModal" title="Kelola Anggota">
                                <i class="bi bi-people-fill"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                            <button class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahKelasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Buat Kelas Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama/Kode Kelas</label>
                    <input type="text" class="form-control" placeholder="Contoh: IF-2A">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mata Kuliah Praktikum</label>
                    <select class="form-select">
                        <option>-- Pilih Mata Kuliah --</option>
                        <option>Praktikum Pemrograman Web</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kapasitas Maksimal</label>
                    <input type="number" class="form-control" value="40">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary">Simpan Kelas</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="kelolaAnggotaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-people-fill me-2"></i>Kelola Anggota: Kelas IF-2A</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="fw-bold border-bottom pb-2">Pengajar</h6>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label small">Dosen Pengampu</label>
                        <select class="form-select form-select-sm">
                            <option>Dr. Budi</option>
                            <option>Ubah Dosen...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">Asisten Praktikum</label>
                        <select class="form-select form-select-sm">
                            <option>Siti (Mahasiswa Akhir)</option>
                            <option>Ubah Asisten...</option>
                        </select>
                    </div>
                </div>

                <h6 class="fw-bold border-bottom pb-2 d-flex justify-content-between">
                    Daftar Mahasiswa
                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-plus"></i> Tambah Mahasiswa</button>
                </h6>
                <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>3411201001</td>
                                <td>Ahmad Fathoni</td>
                                <td><button class="btn btn-sm btn-danger py-0"><i class="bi bi-x"></i> Hapus</button></td>
                            </tr>
                            <tr>
                                <td>3411201002</td>
                                <td>Budi Santoso</td>
                                <td><button class="btn btn-sm btn-danger py-0"><i class="bi bi-x"></i> Hapus</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-info text-white">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>