<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 text-gray-800">Dashboard Admin</h2>
    <span class="text-muted">Selamat datang, <?= esc($username ?? 'Administrator') ?></span>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title">Mata Kuliah Praktikum</h6>
                <h2 class="mb-0"><?= esc($total_mk ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title">Kelas Aktif</h6>
                <h2 class="mb-0"><?= esc($kelas_aktif ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-dark bg-warning shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title">Dosen & Asisten</h6>
                <h2 class="mb-0"><?= esc($total_pengajar ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title">Total Mahasiswa</h6>
                <h2 class="mb-0"><?= esc($total_mhs ?? 0) ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-primary">Monitoring Status Input Nilai Kelas</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Kelas</th>
                        <th>Mata Kuliah</th>
                        <th>Progress Penilaian</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($status_nilai ?? []) as $row): ?>
                        <tr>
                            <td><strong><?= esc($row['kelas'] ?? '-') ?></strong></td>
                            <td><?= esc($row['mk'] ?? '-') ?></td>
                            <td style="width: 30%;">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar <?= ($row['progress'] ?? '') === '100%' ? 'bg-success' : 'bg-primary' ?>"
                                        role="progressbar"
                                        style="width: <?= esc($row['progress'] ?? '0%') ?>;"
                                        aria-valuenow="<?= intval((string) ($row['progress'] ?? '0')) ?>"
                                        aria-valuemin="0" aria-valuemax="100">
                                        <?= esc($row['progress'] ?? '0%') ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php
                                $badgeClass = 'bg-danger';
                                if (($row['status'] ?? '') === 'Selesai') $badgeClass = 'bg-success';
                                if (($row['status'] ?? '') === 'In Progress') $badgeClass = 'bg-warning text-dark';
                                ?>
                                <span class="badge <?= esc($badgeClass) ?>"><?= esc($row['status'] ?? '-') ?></span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">Lihat Detail</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>