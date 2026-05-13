<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 text-gray-800">Manajemen Mata Kuliah Praktikum</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahDataModal">
        <i class="bi bi-plus-circle me-1"></i> Tambah Mata Kuliah
    </button>
</div>

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
                    <tr>
                        <td>1</td>
                        <td>IF1234</td>
                        <td>Praktikum Pemrograman Web</td>
                        <td>1</td>
                        <td>Ganjil</td>
                        <td>
                            <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                            <button class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>IF1235</td>
                        <td>Praktikum Basis Data</td>
                        <td>1</td>
                        <td>Genap</td>
                        <td>
                            <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                            <button class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahDataModal" tabindex="-1" aria-labelledby="tambahDataModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tambahDataModalLabel">Tambah Mata Kuliah</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="#" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kode_mk" class="form-label">Kode Mata Kuliah</label>
                        <input type="text" class="form-control" id="kode_mk" placeholder="Contoh: IF1234" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_mk" class="form-label">Nama Mata Kuliah</label>
                        <input type="text" class="form-control" id="nama_mk" placeholder="Contoh: Praktikum Pemrograman Web" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sks" class="form-label">Bobot SKS</label>
                            <input type="number" class="form-control" id="sks" min="1" max="4" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="semester" class="form-label">Semester</label>
                            <select class="form-select" id="semester" required>
                                <option value="">Pilih Semester...</option>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
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

<?= $this->endSection() ?>