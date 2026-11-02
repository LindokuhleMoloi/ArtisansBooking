<h2 class="mb-4">Dashboard</h2>
<p>Welcome, <strong><?= htmlspecialchars(Auth::user()['first_name']) ?></strong> (<?= Auth::role() ?>).</p>

<?php if (Auth::is('customer')): ?>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">My Bookings</h6>
                    <div class="fs-3"><?= count($data['bookings']) ?></div>
                    <a href="<?= APP_URL ?>/index.php?page=my-bookings">View all</a>
                </div>
            </div>
        </div>
    </div>
    <?php if (!empty($data['recommendations'])): ?>
        <h5>Recommended for you</h5>
        <div class="row g-2">
            <?php foreach ($data['recommendations'] as $a): ?>
            <div class="col-md-3">
                <div class="card card-artisan h-100">
                    <div class="card-body py-2">
                        <strong><?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?></strong>
                        <div class="rating-stars small"><?= number_format($a['avg_rating'], 1) ?> ★</div>
                        <a href="<?= APP_URL ?>/index.php?page=artisan&id=<?= $a['id'] ?>" class="small">View</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php elseif (Auth::is('artisan')): ?>
    <?php if (($data['approval_status'] ?? null) !== 'approved'): ?>
        <?php
            $approvalStatus = $data['approval_status'] ?? 'pending';
            $approvalMessages = [
                'pending' => ['warning', 'Your application is being reviewed. Your profile will be visible to customers once approved.'],
                'declined' => ['danger', 'Your application was declined. Please contact support if you believe this was a mistake.'],
            ];
            [$approvalAlert, $approvalMessage] = $approvalMessages[$approvalStatus] ?? $approvalMessages['pending'];
        ?>
        <div class="alert alert-<?= $approvalAlert ?> border-0 shadow-sm mb-4">
            <strong>Application <?= htmlspecialchars(ucfirst($approvalStatus)) ?>.</strong>
            <?= htmlspecialchars($approvalMessage) ?>
        </div>
    <?php endif; ?>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Bookings</h6>
                    <div class="fs-3"><?= count($data['bookings']) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <h6 class="text-muted">Pending</h6>
                    <div class="fs-3"><?= count($data['pending']) ?></div>
                    <a href="<?= APP_URL ?>/index.php?page=manage-bookings">Manage</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
