<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 text-gray-800">Konfigurasi Template Penilaian</h2>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0 fw-bold">Buat Template Baru</h6>
            </div>
            <div class="card-body">
                <form action="#" method="POST" id="formTemplate">
                    <div class="mb-3">
                        <label for="mata_kuliah" class="form-label">Pilih Mata Kuliah Praktikum</label>
                        <select class="form-select" id="mata_kuliah" required>
                            <option value="">-- Pilih Mata Kuliah --</option>
                            <option value="1">IF1234 - Praktikum Pemrograman Web</option>
                            <option value="2">IF1235 - Praktikum Basis Data</option>
                        </select>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold mb-0">Komponen Penilaian</label>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="tambahKomponen()">
                            <i class="bi bi-plus-lg"></i> Tambah Komponen
                        </button>
                    </div>

                    <div id="komponenContainer">
                        <div class="row mb-2 komponen-row">
                            <div class="col-md-7">
                                <input type="text" class="form-control" name="nama_komponen[]" placeholder="Contoh: Kehadiran, Tugas, UTS..." required>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="number" class="form-control input-bobot" name="bobot[]" placeholder="0" min="1" max="100" oninput="hitungTotal()" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger w-100" onclick="hapusKomponen(this)" disabled>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3 d-flex justify-content-between align-items-center" role="alert">
                        <strong>Total Bobot: <span id="totalBobot">0</span>%</strong>
                        <span id="statusBobot" class="badge bg-danger">Belum 100%</span>
                    </div>

                    <button type="submit" class="btn btn-primary" id="btnSimpan" disabled>Simpan Template</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-light border-0">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-info-circle text-primary"></i> Aturan Pembuatan</h5>
                <ul class="text-muted small ps-3">
                    <li>Pilih mata kuliah yang belum memiliki template.</li>
                    <li>Tambahkan komponen nilai sesuai dengan silabus praktikum.</li>
                    <li><strong>Total bobot harus tepat 100%.</strong> Tombol simpan tidak akan aktif jika total kurang atau lebih dari 100%.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function tambahKomponen() {
        const container = document.getElementById('komponenContainer');
        const newRow = document.createElement('div');
        newRow.className = 'row mb-2 komponen-row';
        newRow.innerHTML = `
            <div class="col-md-7">
                <input type="text" class="form-control" name="nama_komponen[]" placeholder="Contoh: Modul 1, Kuis..." required>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <input type="number" class="form-control input-bobot" name="bobot[]" placeholder="0" min="1" max="100" oninput="hitungTotal()" required>
                    <span class="input-group-text">%</span>
                </div>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger w-100" onclick="hapusKomponen(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newRow);

        // Aktifkan tombol hapus di baris pertama jika baris lebih dari 1
        let rows = document.querySelectorAll('.komponen-row');
        if (rows.length > 1) {
            rows[0].querySelector('button').disabled = false;
        }
    }

    function hapusKomponen(btn) {
        btn.closest('.komponen-row').remove();
        hitungTotal(); // Hitung ulang setelah dihapus

        // Nonaktifkan tombol hapus jika sisa 1 baris
        let rows = document.querySelectorAll('.komponen-row');
        if (rows.length === 1) {
            rows[0].querySelector('button').disabled = true;
        }
    }

    function hitungTotal() {
        let total = 0;
        let inputs = document.querySelectorAll('.input-bobot');

        inputs.forEach(input => {
            let val = parseFloat(input.value);
            if (!isNaN(val)) total += val;
        });

        document.getElementById('totalBobot').innerText = total;

        let statusBadge = document.getElementById('statusBobot');
        let btnSimpan = document.getElementById('btnSimpan');

        if (total === 100) {
            statusBadge.className = 'badge bg-success';
            statusBadge.innerText = 'Valid (100%)';
            btnSimpan.disabled = false;
        } else {
            statusBadge.className = 'badge bg-danger';
            statusBadge.innerText = total > 100 ? 'Melebihi 100%' : 'Belum 100%';
            btnSimpan.disabled = true;
        }
    }
</script>

<?= $this->endSection() ?>