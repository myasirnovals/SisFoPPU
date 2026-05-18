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
                <a href="<?= esc($card['link']) ?>" class="small fw-semibold text-decoration-none text-<?= esc($card['color']) ?>">Lihat detail</a>
            </div>
        </div>
    </div>
<?php endforeach; ?>