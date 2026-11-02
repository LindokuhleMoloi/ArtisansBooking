<div class="row align-items-center mb-5">
    <div class="col-lg-7">
        <h1 class="display-5 fw-bold text-dark">Book skilled local artisans<br><span class="text-primary">with confidence</span></h1>
        <p class="lead text-muted">Find verified plumbers, electricians, beauty professionals and more. Get personalised recommendations based on ratings, location and your past bookings.</p>
        <a href="<?= APP_URL ?>/index.php?page=artisans" class="btn btn-brand btn-lg me-2">Find Artisans</a>
        <a href="<?= APP_URL ?>/index.php?page=recommendations" class="btn btn-outline-secondary btn-lg">See Recommendations</a>
    </div>
    <div class="col-lg-5 text-center d-none d-lg-block">
        <i class="bi bi-calendar-check display-1 text-primary opacity-75"></i>
    </div>
</div>

<?php if (!empty($recommendations)): ?>
<h3 class="mb-3">Recommended for you</h3>
<div class="row g-3 mb-5">
    <?php foreach ($recommendations as $a): ?>
    <div class="col-md-4 col-lg-2">
        <div class="card card-artisan h-100 shadow-sm">
            <div class="card-body text-center">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-2" style="width:60px;height:60px">
                    <i class="bi bi-person fs-3 text-secondary"></i>
                </div>
                <h6 class="mb-0"><?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?></h6>
                <small class="text-muted"><?= htmlspecialchars($a['business_name'] ?? '') ?></small>
                <div class="rating-stars small mt-1">
                    <?= str_repeat('★', (int)round($a['avg_rating'])) ?><?= str_repeat('☆', 5 - (int)round($a['avg_rating'])) ?>
                    <span class="text-muted"><?= number_format($a['avg_rating'], 1) ?></span>
                </div>
                <?php if (!empty($a['recommendation_reasons'])): ?>
                    <div class="mt-1">
                        <?php foreach (array_slice($a['recommendation_reasons'], 0, 2) as $r): ?>
                            <span class="badge bg-light text-dark border small"><?= htmlspecialchars($r) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <a href="<?= APP_URL ?>/index.php?page=artisan&id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary mt-2">View</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<h3 class="mb-3">Browse by category</h3>
<div class="row g-3">
    <?php foreach ($categories as $cat): ?>
    <div class="col-6 col-md-3">
        <a href="<?= APP_URL ?>/index.php?page=artisans&category_id=<?= $cat['id'] ?>" class="text-decoration-none">
            <div class="card h-100 shadow-sm text-center p-3">
                <i class="bi bi-tag fs-2 text-primary"></i>
                <div class="fw-semibold mt-2 text-dark"><?= htmlspecialchars($cat['name']) ?></div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>
