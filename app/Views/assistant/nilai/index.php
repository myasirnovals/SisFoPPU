<?= $this->extend('layout/assistant_layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                Manajemen Nilai
            </h2>

            <p class="text-muted mb-0">
                Pantau kelengkapan nilai praktikum dan mahasiswa yang masih memiliki nilai kosong.
            </p>
        </div>
    </div>

    <!-- Komponen Nilai Belum Lengkap -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-0">
            <?= $this->include('assistant/dashboard/partials/incomplete_scores') ?>
        </div>
    </div>

    <!-- Mahasiswa Dengan Nilai Kosong -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <?= $this->include('assistant/dashboard/partials/missing_scores') ?>
        </div>
    </div>

</div>

<?= $this->endSection() ?>