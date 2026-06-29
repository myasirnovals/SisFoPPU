<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 text-gray-800">Konfigurasi Template Penilaian</h2>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Left Column: Form -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>Buat Template Baru</h6>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/template/store') ?>" method="POST" id="formTemplate">
                    <?= csrf_field() ?>

                    <!-- Pilih Mata Kuliah -->
                    <div class="mb-3">
                        <label for="course_id" class="form-label fw-bold">Pilih Mata Kuliah Praktikum <span class="text-danger">*</span></label>
                        <select class="form-select" id="course_id" name="course_id" required>
                            <option value="">-- Pilih Mata Kuliah --</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= $course['id'] ?>" <?= old('course_id') == $course['id'] ? 'selected' : '' ?>>
                                    <?= esc($course['course_code']) ?> - <?= esc($course['course_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($courses)): ?>
                            <div class="form-text text-warning">
                                <i class="bi bi-exclamation-triangle me-1"></i>Semua mata kuliah sudah memiliki template.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Nama Template -->
                    <div class="mb-3">
                        <label for="template_name" class="form-label fw-bold">Nama Template <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="template_name" name="template_name"
                            placeholder="Contoh: Template Penilaian Praktikum Basis Data"
                            value="<?= old('template_name') ?>" required>
                    </div>

                    <!-- Kode Template (Opsional) -->
                    <div class="mb-3">
                        <label for="template_code" class="form-label fw-bold">Kode Template</label>
                        <input type="text" class="form-control" id="template_code" name="template_code"
                            placeholder="Auto-generate jika kosong"
                            value="<?= old('template_code') ?>">
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="2"
                            placeholder="Opsional"><?= old('description') ?></textarea>
                    </div>

                    <hr>

                    <!-- Komponen Penilaian -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="form-label fw-bold mb-0">Komponen Penilaian</label>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="tambahKomponen()">
                            <i class="bi bi-plus-lg me-1"></i>Tambah Komponen
                        </button>
                    </div>

                    <div id="komponenContainer">
                        <!-- Default: UTS -->
                        <div class="row mb-2 komponen-row">
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="nama_komponen[]"
                                    value="UTS" placeholder="Nama komponen" required>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="tipe_komponen[]">
                                    <option value="uts" selected>UTS</option>
                                    <option value="uas">UAS</option>
                                    <option value="tugas">Tugas</option>
                                    <option value="kuis">Kuis</option>
                                    <option value="kehadiran">Kehadiran</option>
                                    <option value="modul">Modul</option>
                                    <option value="laporan">Laporan</option>
                                    <option value="responsi">Responsi</option>
                                    <option value="proyek">Proyek</option>
                                    <option value="presentasi">Presentasi</option>
                                    <option value="sikap">Sikap</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <input type="number" class="form-control input-bobot" name="bobot[]"
                                        value="50" placeholder="0" min="0" max="100" step="0.01"
                                        oninput="hitungTotal()" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger w-100" onclick="hapusKomponen(this)" disabled>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Default: UAS -->
                        <div class="row mb-2 komponen-row">
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="nama_komponen[]"
                                    value="UAS" placeholder="Nama komponen" required>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="tipe_komponen[]">
                                    <option value="uts">UTS</option>
                                    <option value="uas" selected>UAS</option>
                                    <option value="tugas">Tugas</option>
                                    <option value="kuis">Kuis</option>
                                    <option value="kehadiran">Kehadiran</option>
                                    <option value="modul">Modul</option>
                                    <option value="laporan">Laporan</option>
                                    <option value="responsi">Responsi</option>
                                    <option value="proyek">Proyek</option>
                                    <option value="presentasi">Presentasi</option>
                                    <option value="sikap">Sikap</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <input type="number" class="form-control input-bobot" name="bobot[]"
                                        value="50" placeholder="0" min="0" max="100" step="0.01"
                                        oninput="hitungTotal()" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger w-100" onclick="hapusKomponen(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Total Bobot -->
                    <div class="alert mt-3 d-flex justify-content-between align-items-center" id="alertBobot" role="alert">
                        <div>
                            <strong>Total Bobot: <span id="totalBobot">100</span>%</strong>
                            <small class="text-muted d-block" id="pesanBobot">Total bobot harus tepat 100%</small>
                        </div>
                        <span id="statusBobot" class="badge bg-success">Valid (100%)</span>
                    </div>

                    <button type="submit" class="btn btn-primary" id="btnSimpan">
                        <i class="bi bi-save me-1"></i>Simpan Template
                    </button>
                </form>
            </div>
        </div>

        <!-- Daftar Template yang Sudah Ada -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-list me-2"></i>Daftar Template</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Template</th>
                                <th>Mata Kuliah</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($templates)): ?>
                                <?php foreach ($templates as $tpl): ?>
                                    <tr>
                                        <td><code><?= esc($tpl['template_code']) ?></code></td>
                                        <td><?= esc($tpl['template_name']) ?></td>
                                        <td><?= esc($tpl['course_name'] ?? '-') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $tpl['is_active'] ? 'success' : 'secondary' ?>">
                                                <?= $tpl['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info text-white" onclick="lihatDetail(<?= $tpl['id'] ?>)" title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <a href="<?= base_url('admin/template/delete/' . $tpl['id']) ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin ingin menghapus template ini?')"
                                                title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada template penilaian</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Info -->
    <div class="col-lg-4">
        <div class="card bg-light border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-info-circle text-primary me-2"></i>Aturan Pembuatan</h5>
                <ul class="text-muted small ps-3 mb-0">
                    <li class="mb-1">Pilih mata kuliah yang belum memiliki template.</li>
                    <li class="mb-1">Tambahkan komponen nilai sesuai dengan silabus praktikum.</li>
                    <li class="mb-1">Pilih tipe komponen yang sesuai untuk klasifikasi.</li>
                    <li><strong>Total bobot harus tepat 100%.</strong> Tombol simpan tidak akan aktif jika total kurang atau lebih dari 100%.</li>
                </ul>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="m-0 fw-bold">Statistik Template</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Template</span>
                    <span class="fw-bold"><?= count($templates) ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Mata Kuliah Tanpa Template</span>
                    <span class="fw-bold text-warning"><?= count($courses) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Template -->
<div class="modal fade" id="detailTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Detail Template</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Diisi via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ============================================
    // KOMPONEN FUNCTIONS
    // ============================================
    function tambahKomponen() {
        const container = document.getElementById('komponenContainer');
        const newRow = document.createElement('div');
        newRow.className = 'row mb-2 komponen-row';
        newRow.innerHTML = `
            <div class="col-md-5">
                <input type="text" class="form-control" name="nama_komponen[]" placeholder="Nama komponen" required>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="tipe_komponen[]">
                    <option value="uts">UTS</option>
                    <option value="uas">UAS</option>
                    <option value="tugas">Tugas</option>
                    <option value="kuis">Kuis</option>
                    <option value="kehadiran">Kehadiran</option>
                    <option value="modul">Modul</option>
                    <option value="laporan">Laporan</option>
                    <option value="responsi">Responsi</option>
                    <option value="proyek">Proyek</option>
                    <option value="presentasi">Presentasi</option>
                    <option value="sikap">Sikap</option>
                    <option value="custom" selected>Custom</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="input-group">
                    <input type="number" class="form-control input-bobot" name="bobot[]" placeholder="0" min="0" max="100" step="0.01" oninput="hitungTotal()" required>
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
        hitungTotal();
        updateDeleteButtons();
    }

    function hapusKomponen(btn) {
        btn.closest('.komponen-row').remove();
        hitungTotal();
        updateDeleteButtons();
    }

    function updateDeleteButtons() {
        let rows = document.querySelectorAll('.komponen-row');
        rows.forEach((row, index) => {
            let btn = row.querySelector('button[onclick="hapusKomponen(this)"]');
            if (btn) {
                btn.disabled = rows.length === 1;
            }
        });
    }

    function hitungTotal() {
        let total = 0;
        let inputs = document.querySelectorAll('.input-bobot');

        inputs.forEach(input => {
            let val = parseFloat(input.value);
            if (!isNaN(val)) total += val;
        });

        total = Math.round(total * 100) / 100; // Round to 2 decimals

        document.getElementById('totalBobot').innerText = total;

        let alertBobot = document.getElementById('alertBobot');
        let statusBadge = document.getElementById('statusBobot');
        let btnSimpan = document.getElementById('btnSimpan');
        let pesanBobot = document.getElementById('pesanBobot');

        if (total === 100) {
            alertBobot.className = 'alert alert-success mt-3 d-flex justify-content-between align-items-center';
            statusBadge.className = 'badge bg-success';
            statusBadge.innerText = 'Valid (100%)';
            pesanBobot.innerText = 'Total bobot sudah tepat 100%';
            btnSimpan.disabled = false;
        } else {
            alertBobot.className = 'alert alert-warning mt-3 d-flex justify-content-between align-items-center';
            statusBadge.className = 'badge bg-danger';
            statusBadge.innerText = total > 100 ? 'Melebihi 100%' : 'Belum 100%';
            pesanBobot.innerText = total > 100 ? 'Kurangi bobot komponen' : 'Tambah bobot komponen';
            btnSimpan.disabled = true;
        }
    }

    // ============================================
    // DETAIL TEMPLATE (AJAX)
    // ============================================
    async function lihatDetail(templateId) {
        try {
            const response = await fetch(`<?= base_url('admin/template/getTemplateDetail') ?>/${templateId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (!result.success) {
                alert('Error: ' + result.message);
                return;
            }

            const data = result.data;
            let componentsHtml = '';

            if (data.components && data.components.length > 0) {
                componentsHtml = '<table class="table table-sm table-bordered"><thead class="table-light"><tr><th>No</th><th>Komponen</th><th>Tipe</th><th>Bobot</th></tr></thead><tbody>';

                data.components.forEach((comp, index) => {
                    componentsHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${comp.component_name}</td>
                            <td><span class="badge bg-secondary">${comp.component_type}</span></td>
                            <td class="text-end">${comp.weight}%</td>
                        </tr>
                    `;
                });

                componentsHtml += '</tbody></table>';
            } else {
                componentsHtml = '<p class="text-muted">Tidak ada komponen</p>';
            }

            const html = `
                <div class="mb-3">
                    <label class="fw-bold">Kode Template:</label>
                    <p class="mb-1"><code>${data.template_code}</code></p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Nama Template:</label>
                    <p class="mb-1">${data.template_name}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Deskripsi:</label>
                    <p class="mb-1">${data.description || '-'}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Status:</label>
                    <span class="badge bg-${data.is_active ? 'success' : 'secondary'}">${data.is_active ? 'Aktif' : 'Nonaktif'}</span>
                </div>
                <hr>
                <h6 class="fw-bold">Komponen Penilaian (Total: ${data.total_weight}%)</h6>
                ${componentsHtml}
            `;

            document.getElementById('detailContent').innerHTML = html;

            const modal = new bootstrap.Modal(document.getElementById('detailTemplateModal'));
            modal.show();

        } catch (error) {
            console.error('Error:', error);
            alert('Gagal mengambil detail template');
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        hitungTotal();
        updateDeleteButtons();
    });
</script>

<?= $this->endSection() ?>