<h2 class="mb-3">Recommended Artisans</h2>
<p class="text-muted">Personalised suggestions based on ratings, popularity, category interest and location.</p>

<form class="row g-2 mb-4" method="get">
    <input type="hidden" name="page" value="recommendations">
    <div class="col-md-4">
        <select name="category_id" class="form-select">
            <option value="">Any category preference</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($categoryId ?? '') == $c['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-brand">Update</button>
    </div>
</form>

<div class="row g-3">
    <?php foreach ($recommendations as $a): ?>
    <div class="col-md-6 col-lg-4">
        <div class="card card-artisan h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <h5 class="mb-1"><?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?></h5>
                    <span class="badge bg-primary">Score <?= number_format($a['recommendation_score'], 2) ?></span>
                </div>
                <p class="text-muted small mb-1"><?= htmlspecialchars($a['business_name'] ?? '') ?></p>
                <div class="rating-stars small mb-2">
                    <?= str_repeat('★', (int)round($a['avg_rating'])) ?><?= str_repeat('☆', 5 - (int)round($a['avg_rating'])) ?>
                    <?= number_format($a['avg_rating'], 1) ?>
                </div>
                <?php if (!empty($a['recommendation_reasons'])): ?>
                    <div class="mb-2">
                        <?php foreach ($a['recommendation_reasons'] as $r): ?>
                            <span class="badge bg-light text-dark border me-1"><?= htmlspecialchars($r) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <a href="<?= APP_URL ?>/index.php?page=artisan&id=<?= $a['id'] ?>" class="btn btn-sm btn-brand">View & Book</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
