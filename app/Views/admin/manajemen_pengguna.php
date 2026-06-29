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

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

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
            <!-- Tab Mahasiswa -->
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
                            <?php if (!empty($students)): ?>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?= esc($student['user_nim']) ?></td>
                                        <td><?= esc($student['full_name']) ?></td>
                                        <td><?= esc($student['program_name'] ?? '-') ?></td>
                                        <td><?= esc($student['class_year']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $student['student_status'] === 'aktif' ? 'success' : 'secondary' ?>">
                                                <?= ucfirst(esc($student['student_status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick="editUser('<?= $student['user_nim'] ?>', 'mahasiswa')">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada data mahasiswa</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Dosen & Asisten -->
            <div class="tab-pane fade" id="dosen" role="tabpanel">
                <div class="table-responsive mt-2">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>NID/NIM</th>
                                <th>Nama</th>
                                <th>Program Studi</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($lecturers)): ?>
                                <?php foreach ($lecturers as $lecturer): ?>
                                    <tr>
                                        <td><?= esc($lecturer['user_nid']) ?></td>
                                        <td><?= esc($lecturer['full_name']) ?></td>
                                        <td><?= esc($lecturer['program_name'] ?? '-') ?></td>
                                        <td><span class="badge bg-primary">Dosen</span></td>
                                        <td>
                                            <span class="badge bg-<?= $lecturer['lecturer_status'] === 'aktif' ? 'success' : 'secondary' ?>">
                                                <?= ucfirst(esc($lecturer['lecturer_status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick="editUser('<?= $lecturer['user_nid'] ?>', 'dosen')">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if (!empty($assistants)): ?>
                                <?php foreach ($assistants as $assistant): ?>
                                    <tr>
                                        <td><?= esc($assistant['user_nim']) ?></td>
                                        <td><?= esc($assistant['full_name']) ?></td>
                                        <td><?= esc($assistant['program_name'] ?? '-') ?></td>
                                        <td><span class="badge bg-info">Asisten</span></td>
                                        <td>
                                            <span class="badge bg-<?= $assistant['assistant_status'] === 'aktif' ? 'success' : 'secondary' ?>">
                                                <?= ucfirst(esc($assistant['assistant_status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick="editUser('<?= $assistant['user_nim'] ?>', 'asisten')">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if (empty($lecturers) && empty($assistants)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada data dosen dan asisten</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL TAMBAH PENGGUNA -->
<!-- ============================================ -->
<div class="modal fade" id="tambahPenggunaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('admin/pengguna/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Tambah Pengguna Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipe Pengguna <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="user_type" id="type_mahasiswa" value="mahasiswa" checked onchange="toggleFormFields('tambah')">
                            <label class="btn btn-outline-primary" for="type_mahasiswa"><i class="bi bi-mortarboard me-1"></i> Mahasiswa</label>

                            <input type="radio" class="btn-check" name="user_type" id="type_dosen" value="dosen" onchange="toggleFormFields('tambah')">
                            <label class="btn btn-outline-primary" for="type_dosen"><i class="bi bi-person-workspace me-1"></i> Dosen</label>

                            <input type="radio" class="btn-check" name="user_type" id="type_asisten" value="asisten" onchange="toggleFormFields('tambah')">
                            <label class="btn btn-outline-primary" for="type_asisten"><i class="bi bi-person-badge me-1"></i> Asisten</label>
                        </div>
                    </div>

                    <div class="mb-3" id="tambah_field_nim">
                        <label for="tambah_nim" class="form-label fw-bold">NIM <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tambah_nim" name="nim" maxlength="10" placeholder="Masukkan 10 digit NIM" value="<?= old('nim') ?>">
                    </div>

                    <div class="mb-3 d-none" id="tambah_field_nid">
                        <label for="tambah_nid" class="form-label fw-bold">NID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tambah_nid" name="nid" maxlength="10" placeholder="Masukkan 10 digit NID" value="<?= old('nid') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="tambah_full_name" class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tambah_full_name" name="full_name" placeholder="Masukkan nama lengkap" value="<?= old('full_name') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="tambah_email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="tambah_email" name="email" placeholder="email@example.edu" value="<?= old('email') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="tambah_phone" class="form-label fw-bold">No. Telepon</label>
                        <input type="text" class="form-control" id="tambah_phone" name="phone" placeholder="Opsional" value="<?= old('phone') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="tambah_study_program_id" class="form-label fw-bold">Program Studi <span class="text-danger">*</span></label>
                        <select class="form-select" id="tambah_study_program_id" name="study_program_id" required>
                            <option value="">-- Pilih Program Studi --</option>
                            <?php foreach ($studyPrograms as $program): ?>
                                <option value="<?= $program['id'] ?>" <?= old('study_program_id') == $program['id'] ? 'selected' : '' ?>>
                                    <?= esc($program['program_name']) ?> (<?= esc($program['program_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3" id="tambah_field_class_year">
                        <label for="tambah_class_year" class="form-label fw-bold">Angkatan <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="tambah_class_year" name="class_year" placeholder="Contoh: 2024" min="2000" max="2100" value="<?= old('class_year') ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL EDIT PENGGUNA -->
<!-- ============================================ -->
<div class="modal fade" id="editPenggunaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('admin/pengguna/update') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" id="edit_user_id" name="user_id">
                <input type="hidden" id="edit_user_type" name="user_type">

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipe Pengguna</label>
                        <input type="text" class="form-control" id="edit_type_display" readonly disabled>
                    </div>

                    <div class="mb-3">
                        <label for="edit_identifier" class="form-label fw-bold">ID Pengguna</label>
                        <input type="text" class="form-control" id="edit_identifier" readonly disabled>
                    </div>

                    <div class="mb-3">
                        <label for="edit_full_name" class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_phone" class="form-label fw-bold">No. Telepon</label>
                        <input type="text" class="form-control" id="edit_phone" name="phone" placeholder="Opsional">
                    </div>

                    <div class="mb-3">
                        <label for="edit_study_program_id" class="form-label fw-bold">Program Studi <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_study_program_id" name="study_program_id" required>
                            <option value="">-- Pilih Program Studi --</option>
                            <?php foreach ($studyPrograms as $program): ?>
                                <option value="<?= $program['id'] ?>">
                                    <?= esc($program['program_name']) ?> (<?= esc($program['program_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3" id="edit_field_class_year">
                        <label for="edit_class_year" class="form-label fw-bold">Angkatan <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_class_year" name="class_year" placeholder="Contoh: 2024" min="2000" max="2100">
                    </div>

                    <div class="mb-3">
                        <label for="edit_status" class="form-label fw-bold">Status Akun <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="aktif">Aktif</option>
                            <option value="tidak_aktif">Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_is_active" class="form-label fw-bold">Status Login <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_is_active" name="is_active" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
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

<script>
    // ============================================
    // TAMBAH FORM FUNCTIONS
    // ============================================
    function toggleFormFields(prefix) {
        const type = document.querySelector(`#${prefix === 'tambah' ? 'tambahPenggunaModal' : 'editPenggunaModal'} input[name="user_type"]:checked`);
        const selectedType = type ? type.value : 'mahasiswa';

        const fieldNim = document.getElementById(`${prefix}_field_nim`);
        const fieldNid = document.getElementById(`${prefix}_field_nid`);
        const fieldClassYear = document.getElementById(`${prefix}_field_class_year`);

        const nimInput = document.getElementById(`${prefix}_nim`);
        const nidInput = document.getElementById(`${prefix}_nid`);
        const classYearInput = document.getElementById(`${prefix}_class_year`);

        if (!fieldNim || !fieldNid || !fieldClassYear) return;

        if (selectedType === 'mahasiswa') {
            fieldNim.classList.remove('d-none');
            fieldNid.classList.add('d-none');
            fieldClassYear.classList.remove('d-none');
            if (nimInput) nimInput.required = true;
            if (nidInput) nidInput.required = false;
            if (classYearInput) classYearInput.required = true;
        } else if (selectedType === 'dosen') {
            fieldNim.classList.add('d-none');
            fieldNid.classList.remove('d-none');
            fieldClassYear.classList.add('d-none');
            if (nimInput) nimInput.required = false;
            if (nidInput) nidInput.required = true;
            if (classYearInput) classYearInput.required = false;
        } else if (selectedType === 'asisten') {
            fieldNim.classList.remove('d-none');
            fieldNid.classList.add('d-none');
            fieldClassYear.classList.add('d-none');
            if (nimInput) nimInput.required = true;
            if (nidInput) nidInput.required = false;
            if (classYearInput) classYearInput.required = false;
        }
    }

    // ============================================
    // EDIT FUNCTIONS
    // ============================================
    async function editUser(userId, type) {
        try {
            const response = await fetch(`<?= base_url('admin/pengguna/getUserData') ?>/${userId}/${type}`, {
                method: 'GET',
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

            // Set hidden fields
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_user_type').value = type;

            // Set display fields
            const typeDisplay = {
                'mahasiswa': 'Mahasiswa',
                'dosen': 'Dosen',
                'asisten': 'Asisten Praktikum'
            };
            document.getElementById('edit_type_display').value = typeDisplay[type] || type;
            document.getElementById('edit_identifier').value = data.login_identifier;

            // Set form fields
            document.getElementById('edit_full_name').value = data.full_name || '';
            document.getElementById('edit_email').value = data.email || '';
            document.getElementById('edit_phone').value = data.phone || '';
            document.getElementById('edit_study_program_id').value = data.study_program_id || '';
            document.getElementById('edit_status').value = data.status || 'aktif';
            document.getElementById('edit_is_active').value = data.is_active !== undefined ? data.is_active.toString() : '1';

            // Handle class_year (only for mahasiswa)
            const classYearField = document.getElementById('edit_field_class_year');
            const classYearInput = document.getElementById('edit_class_year');

            if (type === 'mahasiswa') {
                classYearField.classList.remove('d-none');
                classYearInput.required = true;
                classYearInput.value = data.class_year || '';
            } else {
                classYearField.classList.add('d-none');
                classYearInput.required = false;
                classYearInput.value = '';
            }

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editPenggunaModal'));
            modal.show();

        } catch (error) {
            console.error('Error fetching user data:', error);
            alert('Gagal mengambil data pengguna. Silakan coba lagi.');
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleFormFields('tambah');
    });
</script>

<?= $this->endSection() ?>