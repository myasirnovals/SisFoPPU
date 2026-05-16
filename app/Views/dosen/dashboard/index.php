<?= $this->extend('layout/dosen_layout') ?>

<?= $this->section('styles') ?>
<style>
    .hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 55%, #14b8a6 100%);
        color: #fff;
        border: 0;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
        overflow: hidden;
        position: relative;
    }

    .hero-card::before,
    .hero-card::after {
        content: "";
        position: absolute;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
    }

    .hero-card::before {
        width: 240px;
        height: 240px;
        right: -80px;
        top: -80px;
    }

    .hero-card::after {
        width: 160px;
        height: 160px;
        right: 120px;
        bottom: -50px;
    }

    .soft-card {
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid rgba(148, 163, 184, 0.18);
        backdrop-filter: blur(10px);
        box-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
    }

    .section-title {
        letter-spacing: -0.03em;
        font-weight: 800;
    }

    .stat-card {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 24px 44px rgba(15, 23, 42, 0.12);
    }

    .mini-pill {
        border: 1px solid rgba(255, 255, 255, 0.16);
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
    }

    .table thead th {
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-size: 0.74rem;
        color: #475569;
    }

    .section-anchor {
        scroll-margin-top: 95px;
    }

    .metric-badge {
        min-width: 3rem;
        text-align: center;
    }

    .shortcut-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .shortcut-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.12);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="hero-card rounded-5 mb-4">
    <div class="card-body p-4 p-lg-5 position-relative">
        <div class="row align-items-center g-4 position-relative">
            <div class="col-lg-8">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge mini-pill rounded-pill px-3 py-2">Dashboard Dosen</span>
                    <span class="badge mini-pill rounded-pill px-3 py-2"><?= esc($academicYear) ?> - <?= esc($semesterLabel) ?></span>
                    <span class="badge mini-pill rounded-pill px-3 py-2"><?= esc($todayLabel) ?></span>
                </div>
                <h1 class="display-6 fw-bold mb-3">Selamat datang, <?= esc($userName) ?></h1>
                <p class="lead text-white-75 mb-0">
                    Pantau kelas praktikum yang Anda ampu, cek progres input nilai, lihat mahasiswa berisiko, dan selesaikan validasi dalam satu halaman.
                </p>
            </div>
            <div class="col-lg-4">
                <div class="bg-white bg-opacity-10 rounded-4 p-3 border border-white border-opacity-10">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="text-white-50 small">Dosen Login</div>
                            <div class="fw-semibold fs-5"><?= esc($lecturerName) ?></div>
                        </div>
                        <div class="rounded-4 bg-white bg-opacity-15 d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem;">
                            <i class="bi bi-person-badge fs-4"></i>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <div class="d-flex justify-content-between"><span class="text-white-50">Tahun Akademik</span><strong><?= esc($academicYear) ?></strong></div>
                        <div class="d-flex justify-content-between"><span class="text-white-50">Semester</span><strong><?= esc($semesterLabel) ?></strong></div>
                        <div class="d-flex justify-content-between"><span class="text-white-50">Kelas Diampu</span><strong><?= esc($classTotal) ?></strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($summaryCards as $card): ?>
        <div class="col-12 col-sm-6 col-xl-4 col-xxl-2">
            <div class="card stat-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <div class="text-muted small mb-1"><?= esc($card['title']) ?></div>
                            <div class="h2 fw-bold mb-0"><?= esc($card['value']) ?></div>
                        </div>
                        <div class="rounded-4 bg-<?= esc($card['color']) ?> bg-opacity-10 text-<?= esc($card['color']) ?> d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem;">
                            <i class="bi <?= esc($card['icon']) ?> fs-4"></i>
                        </div>
                    </div>
                    <div class="text-muted small"><?= esc($card['description']) ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card soft-card rounded-5 mb-4 section-anchor" id="shortcut-menu">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
            <div>
                <h2 class="h4 section-title mb-1">Shortcut Menu</h2>
                <p class="text-muted mb-0">Akses cepat ke modul inti dosen.</p>
            </div>
        </div>
        <div class="row g-3">
            <?php foreach ($quickActions as $action): ?>
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="<?= esc($action['url']) ?>" class="text-decoration-none">
                        <div class="card shortcut-card border-0 rounded-4 h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="rounded-4 bg-<?= esc($action['color']) ?> bg-opacity-10 text-<?= esc($action['color']) ?> d-inline-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem;">
                                    <i class="bi <?= esc($action['icon']) ?> fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark"><?= esc($action['label']) ?></div>
                                    <small class="text-muted">Buka modul terkait</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card soft-card rounded-5 mb-4 section-anchor" id="kelas-saya">
    <div class="card-body p-0">
        <div class="p-4 pb-0 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h2 class="h4 section-title mb-1">Kelas Praktikum Saya</h2>
                <p class="text-muted mb-0">Ringkasan kelas, progres nilai, progres kehadiran, dan status finalisasi.</p>
            </div>
            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2"><?= esc($classTotal) ?> kelas aktif</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tahun Akademik</th>
                        <th>Semester</th>
                        <th>Mata Kuliah</th>
                        <th>Kode Mata Kuliah</th>
                        <th>Kelas</th>
                        <th>Jumlah Mahasiswa</th>
                        <th>Progress Nilai</th>
                        <th>Progress Kehadiran</th>
                        <th>Rata-rata Nilai</th>
                        <th>Status Nilai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($classRows === []): ?>
                        <tr>
                            <td colspan="12">
                                <div class="text-center py-5 text-muted">Belum ada kelas praktikum yang diampu.</div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($classRows as $index => $classRow): ?>
                            <tr>
                                <td><?= esc($index + 1) ?></td>
                                <td><?= esc($classRow['academic_year']) ?></td>
                                <td><?= esc($classRow['semester']) ?></td>
                                <td class="fw-semibold"><?= esc($classRow['course_name']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= esc($classRow['course_code']) ?></span></td>
                                <td><?= esc($classRow['class_name']) ?></td>
                                <td><?= esc($classRow['student_count']) ?></td>
                                <td style="min-width: 160px;">
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?= esc($classRow['progress_nilai']) ?>%;" aria-valuenow="<?= esc($classRow['progress_nilai']) ?>" aria-valuemin="0" aria-valuemax="100"><?= esc($classRow['progress_nilai']) ?>%</div>
                                    </div>
                                </td>
                                <td style="min-width: 160px;">
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?= esc($classRow['progress_kehadiran']) ?>%;" aria-valuenow="<?= esc($classRow['progress_kehadiran']) ?>" aria-valuemin="0" aria-valuemax="100"><?= esc($classRow['progress_kehadiran']) ?>%</div>
                                    </div>
                                </td>
                                <td><?= esc(number_format((float) $classRow['average_score'], 1)) ?></td>
                                <td><span class="badge bg-<?= esc($classRow['status_badge'] ?? 'secondary') ?>"><?= esc($classRow['status']) ?></span></td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="<?= esc($classRow['detail_url']) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                        <a href="<?= esc($classRow['input_url']) ?>" class="btn btn-sm btn-outline-warning">Input Nilai</a>
                                        <a href="<?= esc($classRow['rekap_url']) ?>" class="btn btn-sm btn-outline-info">Rekap Nilai</a>
                                        <a href="<?= esc($classRow['validation_url']) ?>" class="btn btn-sm btn-outline-success">Validasi</a>
                                        <a href="<?= esc($classRow['remedial_url']) ?>" class="btn btn-sm btn-outline-danger">Remedial</a>
                                        <a href="<?= esc($classRow['report_url']) ?>" class="btn btn-sm btn-outline-secondary">Laporan</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row g-4 mb-4 section-anchor" id="validasi-nilai">
    <div class="col-xl-7">
        <div class="card soft-card rounded-5 h-100">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h2 class="h4 section-title mb-1">Nilai Perlu Validasi</h2>
                        <p class="text-muted mb-0">Submission dari asisten yang masih menunggu review atau validasi.</p>
                    </div>
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><?= esc(count($validationRows)) ?> data</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mata Kuliah</th>
                                <th>Kelas</th>
                                <th>Komponen Nilai</th>
                                <th>Submitted By</th>
                                <th>Tanggal Submit</th>
                                <th>Status</th>
                                <th>Aksi Review</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($validationRows === []): ?>
                                <tr>
                                    <td colspan="7">
                                        <div class="text-center py-5 text-muted">Tidak ada nilai yang perlu divalidasi saat ini.</div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($validationRows as $validationRow): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= esc($validationRow['course_name']) ?></td>
                                        <td><?= esc($validationRow['class_name']) ?></td>
                                        <td><?= esc($validationRow['component_name']) ?></td>
                                        <td><?= esc($validationRow['submitted_by']) ?></td>
                                        <td><?= esc($validationRow['submitted_at']) ?></td>
                                        <td><span class="badge bg-<?= esc($validationRow['badge_class']) ?>"><?= esc($validationRow['status']) ?></span></td>
                                        <td><a href="<?= esc($validationRow['review_url']) ?>" class="btn btn-sm btn-outline-primary">Review</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card soft-card rounded-5 h-100">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h2 class="h4 section-title mb-1">Grafik Progress</h2>
                        <p class="text-muted mb-0">Distribusi progress input nilai per kelas.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2">Chart.js</span>
                </div>
                <div class="ratio ratio-16x9">
                    <canvas id="progressChart"></canvas>
                </div>
                <div class="row g-2 mt-3">
                    <?php foreach ($statusLegend as $label => $count): ?>
                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-between rounded-4 border bg-white px-3 py-2">
                                <span class="small text-muted"><?= esc($label) ?></span>
                                <span class="badge bg-light text-dark border metric-badge"><?= esc($count) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4 section-anchor" id="risiko-mahasiswa">
    <div class="col-xl-7">
        <div class="card soft-card rounded-5 h-100">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h2 class="h4 section-title mb-1">Mahasiswa Berisiko / Nilai Rendah</h2>
                        <p class="text-muted mb-0">Mahasiswa yang perlu perhatian dosen karena nilai atau kehadiran.</p>
                    </div>
                    <span class="badge bg-danger rounded-pill px-3 py-2"><?= esc(count($riskRows)) ?> data</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Mata Kuliah</th>
                                <th>Kelas</th>
                                <th>Nilai Sementara</th>
                                <th>Kehadiran</th>
                                <th>Status Risiko</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($riskRows === []): ?>
                                <tr>
                                    <td colspan="8">
                                        <div class="text-center py-5 text-muted">Belum ada mahasiswa dengan risiko yang terdeteksi.</div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($riskRows as $riskRow): ?>
                                    <tr>
                                        <td><?= esc($riskRow['nim']) ?></td>
                                        <td class="fw-semibold"><?= esc($riskRow['student_name']) ?></td>
                                        <td><?= esc($riskRow['course_name']) ?></td>
                                        <td><?= esc($riskRow['class_name']) ?></td>
                                        <td><?= esc($riskRow['temporary_score']) ?></td>
                                        <td><?= esc($riskRow['attendance']) ?>%</td>
                                        <td><span class="badge bg-<?= esc($riskRow['badge_class']) ?>"><?= esc($riskRow['risk_status']) ?></span></td>
                                        <td><a href="<?= esc($riskRow['detail_url']) ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-5 section-anchor" id="remedial">
        <div class="card soft-card rounded-5 h-100">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h2 class="h4 section-title mb-1">Mahasiswa Eligible Remedial</h2>
                        <p class="text-muted mb-0">Ringkasan peserta remedial yang perlu dipantau dosen.</p>
                    </div>
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><?= esc(count($remedialRows)) ?> data</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Mata Kuliah</th>
                                <th>Kelas</th>
                                <th>Nilai</th>
                                <th>Huruf Mutu</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($remedialRows === []): ?>
                                <tr>
                                    <td colspan="9">
                                        <div class="text-center py-5 text-muted">Belum ada mahasiswa eligible remedial.</div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($remedialRows as $remedialRow): ?>
                                    <tr>
                                        <td><?= esc($remedialRow['nim']) ?></td>
                                        <td class="fw-semibold"><?= esc($remedialRow['student_name']) ?></td>
                                        <td><?= esc($remedialRow['course_name']) ?></td>
                                        <td><?= esc($remedialRow['class_name']) ?></td>
                                        <td><?= esc($remedialRow['score']) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= esc($remedialRow['grade']) ?></span></td>
                                        <td style="max-width: 240px;"><span class="text-muted small"><?= esc($remedialRow['reason']) ?></span></td>
                                        <td><span class="badge bg-<?= esc($remedialRow['badge_class']) ?>"><?= esc($remedialRow['status']) ?></span></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a href="<?= esc($remedialRow['detail_url']) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                                <a href="<?= esc($remedialRow['manage_url']) ?>" class="btn btn-sm btn-outline-warning">Kelola</a>
                                            </div>
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

<div class="card soft-card rounded-5 section-anchor" id="laporan">
    <div class="card-body p-4 p-lg-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <h2 class="h4 section-title mb-2">Rangkuman Cepat</h2>
                <p class="text-muted mb-0">Dashboard ini sudah menyiapkan placeholder yang siap dihubungkan ke modul input nilai, rekapitulasi, validasi, remedial, dan laporan tanpa mengubah struktur utama halaman.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?= site_url('dosen/dashboard#kelas-saya') ?>" class="btn btn-primary me-2 mb-2">Kelas Saya</a>
                <a href="<?= site_url('dosen/dashboard#validasi-nilai') ?>" class="btn btn-outline-success me-2 mb-2">Validasi</a>
                <a href="<?= site_url('dosen/dashboard#remedial') ?>" class="btn btn-outline-danger mb-2">Remedial</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    const chartElement = document.getElementById('progressChart');
    if (chartElement) {
        const chartData = {
            labels: <?= json_encode($chartData['labels'] ?? [], JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{
                label: 'Progress Nilai (%)',
                data: <?= json_encode($chartData['values'] ?? [], JSON_UNESCAPED_UNICODE) ?>,
                borderWidth: 0,
                backgroundColor: [
                    '#2563eb', '#14b8a6', '#f59e0b', '#ef4444', '#64748b', '#8b5cf6'
                ],
                hoverOffset: 4
            }]
        };

        new Chart(chartElement, {
            type: 'doughnut',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '68%'
            }
        });
    }
</script>
<?= $this->endSection() ?>