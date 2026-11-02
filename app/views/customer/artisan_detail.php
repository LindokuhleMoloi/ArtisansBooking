<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width:90px;height:90px">
                    <i class="bi bi-person fs-1 text-secondary"></i>
                </div>
                <h3><?= htmlspecialchars($artisan['first_name'] . ' ' . $artisan['last_name']) ?></h3>
                <p class="text-muted"><?= htmlspecialchars($profile['business_name'] ?? '') ?></p>
                <?php if (!empty($profile['is_verified'])): ?>
                    <span class="badge badge-verified mb-2">Verified Artisan</span>
                <?php endif; ?>
                <div class="rating-stars fs-5">
                    <?= str_repeat('★', (int)round($profile['avg_rating'] ?? 0)) ?><?= str_repeat('☆', 5 - (int)round($profile['avg_rating'] ?? 0)) ?>
                </div>
                <p class="small text-muted"><?= number_format($profile['avg_rating'] ?? 0, 1) ?> · <?= $profile['total_reviews'] ?? 0 ?> reviews · <?= $profile['total_bookings'] ?? 0 ?> bookings</p>
                <p class="small"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($artisan['location_city'] ?? '') ?>, <?= htmlspecialchars($artisan['location_province'] ?? '') ?></p>
                <p class="small"><?= $profile['years_experience'] ?? 0 ?> years experience</p>
            </div>
        </div>
        <?php if ($artisan['bio']): ?>
        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <h6>About</h6>
                <p class="mb-0 small"><?= nl2br(htmlspecialchars($artisan['bio'])) ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="col-lg-8">
        <h4 class="mb-3">Services</h4>
        <?php if (empty($services)): ?>
            <div class="alert alert-info">No services listed yet.</div>
        <?php else: ?>
            <div class="list-group mb-4">
                <?php foreach ($services as $s): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1"><?= htmlspecialchars($s['title']) ?></h6>
                        <small class="text-muted"><?= htmlspecialchars($s['category_name']) ?> · <?= $s['duration_minutes'] ?> min</small>
                        <?php if ($s['description']): ?>
                            <p class="small mb-0 mt-1"><?= htmlspecialchars($s['description']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">R<?= number_format($s['price'], 2) ?></div>
                        <?php if (Auth::check() && Auth::is('customer')): ?>
                            <a href="<?= APP_URL ?>/index.php?page=book&service_id=<?= $s['id'] ?>" class="btn btn-sm btn-brand mt-1">Book</a>
                        <?php else: ?>
                            <a href="<?= APP_URL ?>/index.php?page=login" class="btn btn-sm btn-outline-secondary mt-1">Login to book</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h4 class="mb-3">Recent Reviews</h4>
        <?php if (empty($reviews)): ?>
            <p class="text-muted">No reviews yet.</p>
        <?php else: ?>
            <?php foreach ($reviews as $r): ?>
            <div class="border-bottom pb-2 mb-2">
                <div class="rating-stars small"><?= str_repeat('★', $r['rating']) ?><?= str_repeat('☆', 5 - $r['rating']) ?></div>
                <strong class="small"><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></strong>
                <span class="text-muted small"><?= date('d M Y', strtotime($r['created_at'])) ?></span>
                <?php if ($r['comment']): ?>
                    <p class="small mb-0 mt-1"><?= htmlspecialchars($r['comment']) ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
