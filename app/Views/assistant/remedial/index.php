<?= $this->extend('layout/assistant_layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                Jadwal dan Data Remedial
            </h2>

            <p class="text-muted mb-0">
                Informasi mahasiswa yang mengikuti remedial dan jadwal pelaksanaannya.
            </p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <?= $this->include('assistant/dashboard/partials/remedial_widget') ?>
        </div>
    </div>

</div>

<?= $this->endSection() ?>