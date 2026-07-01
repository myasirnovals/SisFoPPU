<?php
$class = $class ?? [];
$components = $components ?? [];
$students = $students ?? [];

$filenamePrefix = 'rekap_' . preg_replace('/[^a-zA-Z0-9]/', '_', $class['course_code'] ?? 'kelas');
?>
<div class="popup-rekap">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded-3">
        <div>
            <h6 class="mb-1 fw-bold"><?= esc($class['course_name'] ?? 'Kelas') ?></h6>
            <small class="text-muted">
                <span class="badge bg-primary me-1"><?= esc($class['course_code'] ?? '-') ?></span>
                <?= esc($class['class_name'] ?? '-') ?>
            </small>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportRekap()">
            <i class="bi bi-download me-1"></i>Export CSV
        </button>
    </div>

    <!-- Summary Stats -->
    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <div class="p-2 bg-primary-subtle rounded text-center">
                <div class="fw-bold text-primary"><?= count($students) ?></div>
                <small class="text-muted">Mahasiswa</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-2 bg-success-subtle rounded text-center">
                <div class="fw-bold text-success">
                    <?php
                    $lulus = count(array_filter($students, function ($s) {
                        return isset($s['final_score']) && $s['final_score'] !== '-' && is_numeric($s['final_score']) && $s['final_score'] >= 60;
                    }));
                    echo $lulus;
                    ?>
                </div>
                <small class="text-muted">Lulus</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-2 bg-danger-subtle rounded text-center">
                <div class="fw-bold text-warning">
                    <?php
                    $belum = count(array_filter($students, function ($s) {
                        return !isset($s['final_score']) || $s['final_score'] === '-';
                    }));
                    echo $belum;
                    ?>
                </div>
                <small class="text-muted">Tidak Lulus</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-2 bg-warning-subtle rounded text-center">
                <div class="fw-bold text-warning">
                    <?php
                    $belum = count(array_filter($students, fn($s) => $s['final_score'] === '-'));
                    echo $belum;
                    ?>
                </div>
                <small class="text-muted">Belum Dinilai</small>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive rekap-table" style="max-height: 450px; overflow-y: auto;">
        <table class="table table-sm table-bordered table-hover align-middle" id="rekapTable">
            <thead class="table-dark sticky-top">
                <tr>
                    <th class="text-center" style="width: 3%">No</th>
                    <th style="width: 10%">NIM</th>
                    <th style="width: 15%">Nama</th>
                    <?php foreach ($components as $comp): ?>
                        <th class="text-center" style="min-width: 80px;">
                            <div class="small"><?= esc($comp['component_name']) ?></div>
                            <small class="fw-normal opacity-75">(<?= $comp['weight'] ?>%)</small>
                        </th>
                    <?php endforeach; ?>
                    <th class="text-center" style="width: 8%">Nilai Akhir</th>
                    <th class="text-center" style="width: 5%">Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="<?= 5 + count($components) ?>" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Belum ada mahasiswa terdaftar
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $i => $s): ?>
                        <tr>
                            <td class="text-center"><?= $i + 1 ?></td>
                            <td><code class="small"><?= esc($s['nim']) ?></code></td>
                            <td class="fw-medium"><?= esc($s['student_name']) ?></td>
                            <?php foreach ($components as $comp): ?>
                                <?php $score = $s['scores'][$comp['id']] ?? null; ?>
                                <td class="text-center">
                                    <?php if ($score && $score['score'] !== null): ?>
                                        <span class="<?= $score['percentage'] >= 60 ? 'text-success' : 'text-danger' ?> fw-semibold">
                                            <?= number_format($score['score'], 1) ?>
                                        </span>
                                        <small class="text-muted d-block"><?= number_format($score['percentage'], 0) ?>%</small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="text-center fw-bold">
                                <?php if ($s['final_score'] !== '-'): ?>
                                    <span class="<?= $s['final_score'] >= 60 ? 'text-success' : 'text-danger' ?>">
                                        <?= $s['final_score'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php
                                $grade = $s['grade'] ?? '-';
                                $gradeClass = match ($grade) {
                                    'A' => 'bg-success',
                                    'B' => 'bg-success bg-opacity-75',
                                    'C' => 'bg-info',
                                    'D' => 'bg-warning',
                                    'E' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $gradeClass ?>"><?= $s['grade'] ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function exportRekap() {
        const table = document.getElementById('rekapTable');
        let csv = [];
        const rows = table.querySelectorAll('tr');

        rows.forEach(row => {
            const cols = row.querySelectorAll('td, th');
            const rowData = [];
            cols.forEach(col => {
                // Clean text: remove extra spaces and newlines
                let text = col.textContent.replace(/\s+/g, ' ').trim();
                rowData.push('"' + text.replace(/"/g, '""') + '"');
            });
            csv.push(rowData.join(','));
        });

        const csvContent = '\uFEFF' + csv.join('\n');
        const blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });
        const link = document.createElement('a');
        const timestamp = new Date().toISOString().slice(0, 10).replace(/-/g, '');
        link.href = URL.createObjectURL(blob);
        link.download = '<?= $filenamePrefix ?>_' + timestamp + '.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>