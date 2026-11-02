<h2 class="mb-4">Find Artisans</h2>

<form class="row g-2 mb-4" method="get">
    <input type="hidden" name="page" value="artisans">
    <div class="col-md-3">
        <input type="text" name="city" class="form-control" placeholder="City" value="<?= htmlspecialchars($filters['city'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <select name="category_id" class="form-select">
            <option value="">All categories</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($filters['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="min_rating" class="form-select">
            <option value="">Any rating</option>
            <option value="4" <?= ($filters['min_rating'] ?? '') == 4 ? 'selected' : '' ?>>4+</option>
            <option value="4.5" <?= ($filters['min_rating'] ?? '') == 4.5 ? 'selected' : '' ?>>4.5+</option>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-brand w-100">Filter</button>
    </div>
</form>

<div class="row g-3">
    <?php if (empty($artisans)): ?>
        <div class="col-12"><div class="alert alert-info">No artisans match your filters.</div></div>
    <?php else: ?>
        <?php foreach ($artisans as $a): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card card-artisan h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title mb-1"><?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?></h5>
                        <?php if ($a['is_verified']): ?><span class="badge badge-verified">Verified</span><?php endif; ?>
                    </div>
                    <p class="text-muted small mb-1"><?= htmlspecialchars($a['business_name'] ?? '') ?></p>
                    <p class="small mb-2"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($a['location_city'] ?? '') ?>, <?= htmlspecialchars($a['location_province'] ?? '') ?></p>
                    <div class="rating-stars mb-2">
                        <?= str_repeat('★', (int)round($a['avg_rating'])) ?><?= str_repeat('☆', 5 - (int)round($a['avg_rating'])) ?>
                        <span class="text-muted small"><?= number_format($a['avg_rating'], 1) ?> (<?= $a['total_reviews'] ?> reviews)</span>
                    </div>
                    <p class="small text-muted"><?= htmlspecialchars(mb_strimwidth($a['bio'] ?? '', 0, 100, '…')) ?></p>
                    <a href="<?= APP_URL ?>/index.php?page=artisan&id=<?= $a['id'] ?>" class="btn btn-sm btn-brand">View profile & book</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
