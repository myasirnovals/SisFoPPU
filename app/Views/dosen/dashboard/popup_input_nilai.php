<?php
$class = $class ?? [];
$student = $student ?? [];
$components = $components ?? [];
$existingScores = $existingScores ?? [];
?>
<div class="popup-input-nilai">
    <!-- Header Info -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center"
                style="width: 48px; height: 48px; font-size: 20px;">
                <i class="bi bi-person"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold"><?= esc($student['full_name'] ?? 'Mahasiswa') ?></h6>
                <small class="text-muted"><code><?= esc($student['id'] ?? '-') ?></code></small>
            </div>
        </div>
        <div class="text-end">
            <span class="badge bg-primary"><?= esc($class['course_code'] ?? '-') ?></span>
            <div class="small text-muted mt-1"><?= esc($class['course_name'] ?? '-') ?></div>
        </div>
    </div>

    <form action="<?= base_url('dosen/simpan-nilai') ?>" method="post" class="ajax-form">
        <?= csrf_field() ?>
        <input type="hidden" name="practicum_class_id" value="<?= $class['id'] ?>">
        <input type="hidden" name="student_id" value="<?= $student['id'] ?>">

        <div class="table-responsive mb-3">
            <table class="table table-sm table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 30%">Komponen</th>
                        <th style="width: 12%" class="text-center">Bobot</th>
                        <th style="width: 22%">Nilai</th>
                        <th style="width: 12%" class="text-center">Max</th>
                        <th style="width: 12%" class="text-center">%</th>
                        <th style="width: 12%" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($components)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                Belum ada komponen penilaian
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($components as $comp): ?>
                            <?php if (!empty($comp['subcomponents'])): ?>
                                <!-- Component with subcomponents -->
                                <tr class="table-secondary">
                                    <td colspan="6" class="fw-semibold py-2">
                                        <i class="bi bi-folder me-1"></i><?= esc($comp['component_name']) ?>
                                        <span class="text-muted fw-normal">(Total Bobot: <?= $comp['weight'] ?>%)</span>
                                    </td>
                                </tr>
                                <?php foreach ($comp['subcomponents'] as $sub): ?>
                                    <?php
                                    $key = "sub_{$sub['id']}";
                                    $existing = $existingScores[$key] ?? null;
                                    $score = $existing['score'] ?? '';
                                    ?>
                                    <tr>
                                        <td class="ps-4">
                                            <i class="bi bi-file-earmark-text me-1 text-muted"></i>
                                            <?= esc($sub['subcomponent_name']) ?>
                                        </td>
                                        <td class="text-center"><?= $sub['weight'] ?>%</td>
                                        <td>
                                            <input type="number"
                                                name="scores[sub][<?= $sub['id'] ?>]"
                                                value="<?= $score !== null && $score !== '' ? $score : '' ?>"
                                                step="0.01" min="0" max="<?= $sub['max_score'] ?>"
                                                class="form-control form-control-sm score-input"
                                                placeholder="0.00"
                                                data-max="<?= $sub['max_score'] ?>"
                                                oninput="calculatePercentage(this)">
                                        </td>
                                        <td class="text-center text-muted"><?= $sub['max_score'] ?></td>
                                        <td class="text-center percentage-cell">-</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary status-badge">Belum</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Simple component -->
                                <?php
                                $key = "comp_{$comp['id']}";
                                $existing = $existingScores[$key] ?? null;
                                $score = $existing['score'] ?? '';
                                ?>
                                <tr>
                                    <td class="fw-medium">
                                        <i class="bi bi-file-earmark me-1 text-muted"></i>
                                        <?= esc($comp['component_name']) ?>
                                    </td>
                                    <td class="text-center"><?= $comp['weight'] ?>%</td>
                                    <td>
                                        <input type="number"
                                            name="scores[comp][<?= $comp['id'] ?>]"
                                            value="<?= $score !== null && $score !== '' ? $score : '' ?>"
                                            step="0.01" min="0" max="<?= $comp['max_score'] ?>"
                                            class="form-control form-control-sm score-input"
                                            placeholder="0.00"
                                            data-max="<?= $comp['max_score'] ?>"
                                            oninput="calculatePercentage(this)">
                                    </td>
                                    <td class="text-center text-muted"><?= $comp['max_score'] ?></td>
                                    <td class="text-center percentage-cell">-</td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary status-badge">Belum</span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-semibold">
                <i class="bi bi-chat-left-text me-1"></i>Catatan
            </label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Tambahkan catatan tentang penilaian..."></textarea>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-2 border-top">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                <i class="bi bi-x-lg me-1"></i>Batal
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Simpan Nilai
            </button>
        </div>
    </form>
</div>

<script>
    (function() {
        function calculatePercentage(input) {
            const max = parseFloat(input.dataset.max) || 100;
            const score = parseFloat(input.value);
            const percentageCell = input.closest('tr').querySelector('.percentage-cell');
            const statusBadge = input.closest('tr').querySelector('.status-badge');

            if (isNaN(score) || input.value === '') {
                percentageCell.textContent = '-';
                statusBadge.className = 'badge bg-secondary status-badge';
                statusBadge.textContent = 'Belum';
                return;
            }

            const percentage = max > 0 ? ((score / max) * 100) : 0;
            percentageCell.textContent = percentage.toFixed(1) + '%';

            // Update status badge
            if (percentage >= 80) {
                statusBadge.className = 'badge bg-success status-badge';
                statusBadge.textContent = 'Baik';
            } else if (percentage >= 60) {
                statusBadge.className = 'badge bg-info status-badge';
                statusBadge.textContent = 'Cukup';
            } else if (percentage >= 40) {
                statusBadge.className = 'badge bg-warning status-badge';
                statusBadge.textContent = 'Kurang';
            } else {
                statusBadge.className = 'badge bg-danger status-badge';
                statusBadge.textContent = 'Buruk';
            }
        }

        // Calculate all on load
        document.querySelectorAll('.score-input').forEach(input => calculatePercentage(input));

        // Expose to global scope for inline oninput
        window.calculatePercentage = calculatePercentage;
    })();
</script>