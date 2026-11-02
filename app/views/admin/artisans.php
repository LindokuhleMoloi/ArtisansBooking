<?php
$statusLabels = [
    'pending' => 'Pending review',
    'approved' => 'Approved',
    'declined' => 'Declined',
];
$statusClasses = [
    'pending' => 'status-pending',
    'approved' => 'status-approved',
    'declined' => 'status-declined',
];
?>
<section class="admin-dashboard">
    <div class="admin-hero mb-2">
        <p class="admin-hero-kicker text-uppercase mb-2">Artisans Republik · Administration</p>
        <h1 class="admin-heading fw-bold mb-2">Artisan applications</h1>
        <p class="admin-hero-copy mb-0">Review applications and manage who can appear in the marketplace.</p>
    </div>

    <?php if ($notice !== null): ?>
        <div class="alert alert-<?= $notice === 'approved' ? 'success' : 'secondary' ?> border-0 shadow-sm">
            Application <?= htmlspecialchars($notice) ?> successfully.
        </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <?php foreach ([
            'all' => ['All artisans', 'bi-people'],
            'pending' => ['Awaiting review', 'bi-hourglass-split'],
            'approved' => ['Approved', 'bi-check2-circle'],
            'declined' => ['Declined', 'bi-x-circle'],
        ] as $key => [$label, $icon]): ?>
            <div class="col-6 col-xl-3">
                <a class="text-decoration-none" href="<?= APP_URL ?>/index.php?page=admin-artisans&status=<?= urlencode($key) ?>">
                    <div class="admin-stat p-3 p-lg-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="admin-stat-label"><?= htmlspecialchars($label) ?></span>
                            <span class="admin-stat-icon"><i class="bi <?= htmlspecialchars($icon) ?>"></i></span>
                        </div>
                        <div class="admin-stat-value"><?= (int)$stats[$key] ?></div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <section class="card overflow-hidden">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 p-3 p-lg-4 border-bottom">
            <div>
                <h2 class="h5 fw-bold mb-1">Artisan directory</h2>
                <p class="text-muted small mb-0">Applications are shown newest first within each review status.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach (['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'declined' => 'Declined'] as $key => $label): ?>
                    <a class="btn btn-sm admin-filter <?= $status === $key ? 'btn-dark' : 'btn-outline-secondary' ?>"
                       href="<?= APP_URL ?>/index.php?page=admin-artisans&status=<?= urlencode($key) ?>">
                        <?= htmlspecialchars($label) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (empty($artisans)): ?>
            <div class="admin-empty text-center m-3 m-lg-4 p-5">
                <i class="bi bi-inbox fs-2 text-muted"></i>
                <h3 class="h6 fw-bold mt-3">No artisans in this view</h3>
                <p class="text-muted small mb-0">Try another status filter to see more applications.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table admin-table align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Artisan</th>
                            <th scope="col">Contact</th>
                            <th scope="col">Experience</th>
                            <th scope="col">Applied</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-lg-end">Review action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($artisans as $artisan): ?>
                            <?php
                            $artisanStatus = $artisan['approval_status'];
                            $initials = strtoupper(substr($artisan['first_name'], 0, 1) . substr($artisan['last_name'], 0, 1));
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="admin-avatar"><?= htmlspecialchars($initials) ?></span>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($artisan['first_name'] . ' ' . $artisan['last_name']) ?></div>
                                            <div class="text-muted small"><?= htmlspecialchars($artisan['business_name'] ?: 'Independent artisan') ?></div>
                                            <div class="text-muted small">
                                                <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars(trim(($artisan['location_city'] ?? '') . ', ' . ($artisan['location_province'] ?? ''), ', ' ) ?: 'Location not provided') ?>
                                            </div>
                                            <?php if (!empty($artisan['bio'])): ?>
                                                <div class="text-muted small mt-1"><?= htmlspecialchars(mb_strimwidth($artisan['bio'], 0, 100, '…')) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><?= htmlspecialchars($artisan['email']) ?></div>
                                    <div class="text-muted small"><?= htmlspecialchars($artisan['phone'] ?: 'No phone provided') ?></div>
                                </td>
                                <td><?= (int)$artisan['years_experience'] ?> years</td>
                                <td>
                                    <span><?= htmlspecialchars(date('M j, Y', strtotime($artisan['applied_at']))) ?></span>
                                    <div class="text-muted small"><?= (int)$artisan['total_bookings'] ?> bookings</div>
                                </td>
                                <td>
                                    <span class="status-pill <?= htmlspecialchars($statusClasses[$artisanStatus] ?? 'status-pending') ?>">
                                        <?= htmlspecialchars($statusLabels[$artisanStatus] ?? ucfirst($artisanStatus)) ?>
                                    </span>
                                </td>
                                <td class="text-lg-end">
                                    <?php if ($artisanStatus === 'pending'): ?>
                                        <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                                            <?php foreach (['approved' => ['Approve', 'btn-success', 'bi-check-lg'], 'declined' => ['Decline', 'btn-outline-danger', 'bi-x-lg']] as $decision => [$label, $buttonClass, $icon]): ?>
                                                <form method="post" action="<?= APP_URL ?>/index.php?page=admin-artisans&status=<?= urlencode($status) ?>">
                                                    <input type="hidden" name="<?= htmlspecialchars(CSRF_TOKEN_NAME) ?>" value="<?= htmlspecialchars(Auth::csrfToken()) ?>">
                                                    <input type="hidden" name="artisan_id" value="<?= (int)$artisan['id'] ?>">
                                                    <input type="hidden" name="decision" value="<?= htmlspecialchars($decision) ?>">
                                                    <button class="btn btn-sm <?= htmlspecialchars($buttonClass) ?>" type="submit">
                                                        <i class="bi <?= htmlspecialchars($icon) ?> me-1"></i><?= htmlspecialchars($label) ?>
                                                    </button>
                                                </form>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">Reviewed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <div class="border-top px-3 px-lg-4 py-3 text-muted small">
            Showing <?= count($artisans) ?> artisan<?= count($artisans) === 1 ? '' : 's' ?>
            <?= $status === 'all' ? 'across all review statuses' : 'with ' . htmlspecialchars($status) . ' status' ?>.
        </div>
    </section>
</section>
