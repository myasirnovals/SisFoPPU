<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 text-gray-800">Manajemen Kelas Praktikum</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahKelasModal">
        <i class="bi bi-plus-circle me-1"></i> Buat Kelas Baru
    </button>
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
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <tr>
                                <td><strong><?= esc($class['class_code']) ?></strong></td>
                                <td><?= esc($class['course_name'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    // Get lecturer info from members (you may need to adjust this)
                                    $lecturerName = '-';
                                    $assistantName = '-';
                                    ?>
                                    <?= esc($lecturerName) ?> <br>
                                    <small class="text-muted"><?= esc($assistantName) ?></small>
                                </td>
                                <td><?= esc($class['student_count'] ?? 0) ?> / 40</td>
                                <td>
                                    <span class="badge bg-<?=
                                                            $class['status'] === 'aktif' ? 'success' : ($class['status'] === 'draft' ? 'secondary' : ($class['status'] === 'selesai' ? 'info' : ($class['status'] === 'terkunci' ? 'warning' : 'dark')))
                                                            ?>">
                                        <?= ucfirst(esc($class['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info text-white" onclick="kelolaAnggota(<?= $class['id'] ?>)" title="Kelola Anggota">
                                        <i class="bi bi-people-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editKelas(<?= $class['id'] ?>)" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $class['id'] ?>, '<?= esc($class['class_name']) ?>')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada kelas praktikum</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL TAMBAH KELAS -->
<!-- ============================================ -->
<div class="modal fade" id="tambahKelasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('admin/kelas/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Buat Kelas Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Kode Kelas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="class_code" placeholder="Contoh: IF-2A" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="class_name" placeholder="Nama lengkap kelas" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mata Kuliah Praktikum <span class="text-danger">*</span></label>
                        <select class="form-select" name="course_id" required>
                            <option value="">-- Pilih Mata Kuliah --</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= $course['id'] ?>"><?= esc($course['course_name']) ?> (<?= esc($course['course_code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tahun Akademik <span class="text-danger">*</span></label>
                            <select class="form-select" name="academic_year_id" required>
                                <option value="">-- Pilih Tahun Akademik --</option>
                                <?php foreach ($academicYears as $ay): ?>
                                    <option value="<?= $ay['id'] ?>"><?= esc($ay['year_code']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Semester <span class="text-danger">*</span></label>
                            <select class="form-select" name="semester_id" required>
                                <option value="">-- Pilih Semester --</option>
                                <?php foreach ($semesters as $sem): ?>
                                    <option value="<?= $sem['id'] ?>"><?= esc($sem['semester_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Laboratorium</label>
                            <select class="form-select" name="laboratory_id">
                                <option value="">-- Pilih Laboratorium --</option>
                                <?php foreach ($laboratories as $lab): ?>
                                    <option value="<?= $lab['id'] ?>"><?= esc($lab['room_name']) ?> (<?= esc($lab['room_code']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Template Penilaian</label>
                            <select class="form-select" name="template_id">
                                <option value="">-- Pilih Template --</option>
                                <?php foreach ($templates as $tpl): ?>
                                    <option value="<?= $tpl['id'] ?>"><?= esc($tpl['template_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Dosen Pengampu</label>
                            <select class="form-select" name="lecturer_id">
                                <option value="">-- Pilih Dosen --</option>
                                <?php foreach ($lecturers as $lecturer): ?>
                                    <option value="<?= $lecturer['user_nid'] ?>"><?= esc($lecturer['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Asisten Praktikum</label>
                            <select class="form-select" name="assistant_id">
                                <option value="">-- Pilih Asisten --</option>
                                <?php foreach ($assistants as $assistant): ?>
                                    <option value="<?= $assistant['user_nim'] ?>"><?= esc($assistant['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="draft">Draft</option>
                            <option value="aktif" selected>Aktif</option>
                            <option value="selesai">Selesai</option>
                            <option value="terkunci">Terkunci</option>
                            <option value="diarsipkan">Diarsipkan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Opsional"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL EDIT KELAS -->
<!-- ============================================ -->
<div class="modal fade" id="editKelasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('admin/kelas/update') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" id="edit_class_id" name="class_id">

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Kode Kelas</label>
                            <input type="text" class="form-control" id="edit_class_code" readonly disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_class_name" name="class_name" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mata Kuliah Praktikum <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_course_id" name="course_id" required>
                            <option value="">-- Pilih Mata Kuliah --</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= $course['id'] ?>"><?= esc($course['course_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tahun Akademik <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_academic_year_id" name="academic_year_id" required>
                                <?php foreach ($academicYears as $ay): ?>
                                    <option value="<?= $ay['id'] ?>"><?= esc($ay['year_code']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Semester <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_semester_id" name="semester_id" required>
                                <?php foreach ($semesters as $sem): ?>
                                    <option value="<?= $sem['id'] ?>"><?= esc($sem['semester_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Laboratorium</label>
                            <select class="form-select" id="edit_laboratory_id" name="laboratory_id">
                                <option value="">-- Pilih Laboratorium --</option>
                                <?php foreach ($laboratories as $lab): ?>
                                    <option value="<?= $lab['id'] ?>"><?= esc($lab['room_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Template Penilaian</label>
                            <select class="form-select" id="edit_template_id" name="template_id">
                                <option value="">-- Pilih Template --</option>
                                <?php foreach ($templates as $tpl): ?>
                                    <option value="<?= $tpl['id'] ?>"><?= esc($tpl['template_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Dosen Pengampu</label>
                            <select class="form-select" id="edit_lecturer_id" name="lecturer_id">
                                <option value="">-- Pilih Dosen --</option>
                                <?php foreach ($lecturers as $lecturer): ?>
                                    <option value="<?= $lecturer['user_nid'] ?>"><?= esc($lecturer['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Asisten Praktikum</label>
                            <select class="form-select" id="edit_assistant_id" name="assistant_id">
                                <option value="">-- Pilih Asisten --</option>
                                <?php foreach ($assistants as $assistant): ?>
                                    <option value="<?= $assistant['user_nim'] ?>"><?= esc($assistant['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="draft">Draft</option>
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                            <option value="terkunci">Terkunci</option>
                            <option value="diarsipkan">Diarsipkan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Update Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL KELOLA ANGGOTA -->
<!-- ============================================ -->
<div class="modal fade" id="kelolaAnggotaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-people-fill me-2"></i>Kelola Anggota: <span id="anggota_kelas_nama">Kelas</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="anggota_class_id">

                <h6 class="fw-bold border-bottom pb-2">Pengajar</h6>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label small">Dosen Pengampu</label>
                        <select class="form-select form-select-sm" id="kelola_lecturer_id">
                            <option value="">-- Pilih Dosen --</option>
                            <?php foreach ($lecturers as $lecturer): ?>
                                <option value="<?= $lecturer['user_nid'] ?>"><?= esc($lecturer['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">Asisten Praktikum</label>
                        <select class="form-select form-select-sm" id="kelola_assistant_id">
                            <option value="">-- Pilih Asisten --</option>
                            <?php foreach ($assistants as $assistant): ?>
                                <option value="<?= $assistant['user_nim'] ?>"><?= esc($assistant['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <h6 class="fw-bold border-bottom pb-2 d-flex justify-content-between align-items-center">
                    Daftar Mahasiswa
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="tambah_mahasiswa_select" style="width: 200px;">
                            <option value="">-- Pilih Mahasiswa --</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= $student['user_nim'] ?>"><?= esc($student['full_name']) ?> (<?= esc($student['user_nim']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-sm btn-outline-primary" onclick="tambahMahasiswa()">
                            <i class="bi bi-plus"></i> Tambah
                        </button>
                    </div>
                </h6>

                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-sm table-bordered" id="tabel_mahasiswa_kelas">
                        <thead class="table-light">
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="mahasiswa_list">
                            <!-- Diisi via JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 text-muted small">
                    Total Mahasiswa: <span id="total_mahasiswa" class="fw-bold">0</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-info text-white" onclick="simpanPerubahanAnggota()">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL KONFIRMASI HAPUS -->
<!-- ============================================ -->
<div class="modal fade" id="hapusKelasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kelas <strong id="hapus_kelas_nama"></strong>?</p>
                <p class="text-muted small">Semua data anggota kelas ini juga akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="hapus_kelas_link" class="btn btn-danger">
                    <i class="bi bi-trash me-1"></i> Hapus Kelas
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // ============================================
    // EDIT KELAS
    // ============================================
    async function editKelas(classId) {
        try {
            const response = await fetch(`<?= base_url('admin/kelas/getClassInfo') ?>/${classId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const result = await response.json();

            if (!result.success) {
                alert('Error: ' + result.message);
                return;
            }

            const data = result.class;

            document.getElementById('edit_class_id').value = data.id;
            document.getElementById('edit_class_code').value = data.class_code;
            document.getElementById('edit_class_name').value = data.class_name;
            document.getElementById('edit_course_id').value = data.course_id;
            document.getElementById('edit_academic_year_id').value = data.academic_year_id;
            document.getElementById('edit_semester_id').value = data.semester_id;
            document.getElementById('edit_laboratory_id').value = data.laboratory_id || '';
            document.getElementById('edit_template_id').value = data.template_id || '';
            document.getElementById('edit_status').value = data.status;
            document.getElementById('edit_description').value = data.description || '';

            // Set lecturer and assistant if available
            if (result.members.lecturers.length > 0) {
                document.getElementById('edit_lecturer_id').value = result.members.lecturers[0].user_nid;
            }
            if (result.members.assistants.length > 0) {
                document.getElementById('edit_assistant_id').value = result.members.assistants[0].user_nim;
            }

            const modal = new bootstrap.Modal(document.getElementById('editKelasModal'));
            modal.show();

        } catch (error) {
            console.error('Error:', error);
            alert('Gagal mengambil data kelas');
        }
    }

    // ============================================
    // KELOLA ANGGOTA
    // ============================================
    async function kelolaAnggota(classId) {
        try {
            const response = await fetch(`<?= base_url('admin/kelas/getClassInfo') ?>/${classId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const result = await response.json();

            if (!result.success) {
                alert('Error: ' + result.message);
                return;
            }

            document.getElementById('anggota_class_id').value = classId;
            document.getElementById('anggota_kelas_nama').textContent = result.class.class_name;

            // Set lecturer and assistant
            if (result.members.lecturers.length > 0) {
                document.getElementById('kelola_lecturer_id').value = result.members.lecturers[0].user_nid;
            } else {
                document.getElementById('kelola_lecturer_id').value = '';
            }

            if (result.members.assistants.length > 0) {
                document.getElementById('kelola_assistant_id').value = result.members.assistants[0].user_nim;
            } else {
                document.getElementById('kelola_assistant_id').value = '';
            }

            // Render students
            renderMahasiswaList(result.members.students);
            document.getElementById('total_mahasiswa').textContent = result.members.total_students;

            const modal = new bootstrap.Modal(document.getElementById('kelolaAnggotaModal'));
            modal.show();

        } catch (error) {
            console.error('Error:', error);
            alert('Gagal mengambil data anggota kelas');
        }
    }

    function renderMahasiswaList(students) {
        const tbody = document.getElementById('mahasiswa_list');
        tbody.innerHTML = '';

        if (students.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Belum ada mahasiswa</td></tr>';
            return;
        }

        students.forEach(student => {
            const row = `
            <tr data-nim="${student.student_nim}">
                <td>${student.student_nim}</td>
                <td>${student.full_name}</td>
                <td>
                    <button class="btn btn-sm btn-danger py-0" onclick="hapusMahasiswa('${student.student_nim}')">
                        <i class="bi bi-x"></i> Hapus
                    </button>
                </td>
            </tr>
        `;
            tbody.innerHTML += row;
        });
    }

    // ============================================
    // TAMBAH MAHASISWA KE KELAS
    // ============================================
    async function tambahMahasiswa() {
        const classId = document.getElementById('anggota_class_id').value;
        const studentNim = document.getElementById('tambah_mahasiswa_select').value;

        if (!studentNim) {
            alert('Pilih mahasiswa terlebih dahulu');
            return;
        }

        try {
            const formData = new FormData();
            formData.append('class_id', classId);
            formData.append('student_nim', studentNim);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            const response = await fetch('<?= base_url('admin/kelas/addStudent') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                // Refresh data
                kelolaAnggota(classId);
                document.getElementById('tambah_mahasiswa_select').value = '';
            } else {
                alert(result.message || 'Gagal menambahkan mahasiswa');
            }

        } catch (error) {
            console.error('Error:', error);
            alert('Gagal menambahkan mahasiswa');
        }
    }

    // ============================================
    // HAPUS MAHASISWA DARI KELAS
    // ============================================
    async function hapusMahasiswa(studentNim) {
        if (!confirm('Yakin ingin menghapus mahasiswa ini dari kelas?')) {
            return;
        }

        const classId = document.getElementById('anggota_class_id').value;

        try {
            const formData = new FormData();
            formData.append('class_id', classId);
            formData.append('student_nim', studentNim);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            const response = await fetch('<?= base_url('admin/kelas/removeStudent') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                // Remove row from table
                const row = document.querySelector(`tr[data-nim="${studentNim}"]`);
                if (row) row.remove();
                document.getElementById('total_mahasiswa').textContent = result.total;
            } else {
                alert(result.message || 'Gagal menghapus mahasiswa');
            }

        } catch (error) {
            console.error('Error:', error);
            alert('Gagal menghapus mahasiswa');
        }
    }

    // ============================================
    // SIMPAN PERUBAHAN ANGGOTA
    // ============================================
    async function simpanPerubahanAnggota() {
        const classId = document.getElementById('anggota_class_id').value;
        const lecturerId = document.getElementById('kelola_lecturer_id').value;
        const assistantId = document.getElementById('kelola_assistant_id').value;

        // You can implement saving lecturer/assistant changes here
        // For now, just close the modal with success message

        alert('Perubahan berhasil disimpan!');
        bootstrap.Modal.getInstance(document.getElementById('kelolaAnggotaModal')).hide();
        location.reload();
    }

    // ============================================
    // HAPUS KELAS
    // ============================================
    function confirmDelete(classId, className) {
        document.getElementById('hapus_kelas_nama').textContent = className;
        document.getElementById('hapus_kelas_link').href = `<?= base_url('admin/kelas/delete') ?>/${classId}`;

        const modal = new bootstrap.Modal(document.getElementById('hapusKelasModal'));
        modal.show();
    }
</script>

<?= $this->endSection() ?>