<h2 class="mb-4">My Bookings</h2>
<?php if (empty($bookings)): ?>
    <div class="alert alert-info">You have no bookings yet. <a href="<?= APP_URL ?>/index.php?page=artisans">Find an artisan</a></div>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-hover bg-white shadow-sm">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Service</th>
                <th>Artisan</th>
                <th>Date</th>
                <th>Time</th>
                <th>Price</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $b): ?>
            <tr>
                <td><?= $b['id'] ?></td>
                <td><?= htmlspecialchars($b['service_title']) ?></td>
                <td><?= htmlspecialchars(($b['business_name'] ?? '') ?: ($b['artisan_first'] . ' ' . $b['artisan_last'])) ?></td>
                <td><?= date('d M Y', strtotime($b['booking_date'])) ?></td>
                <td><?= substr($b['start_time'], 0, 5) ?> – <?= substr($b['end_time'], 0, 5) ?></td>
                <td>R<?= number_format($b['total_price'], 2) ?></td>
                <td>
                    <?php
                    $badge = match($b['status']) {
                        'pending'   => 'warning',
                        'confirmed' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'secondary',
                        default     => 'light'
                    };
                    ?>
                    <span class="badge bg-<?= $badge ?>"><?= ucfirst($b['status']) ?></span>
                </td>
                <td>
                    <?php if ($b['status'] === 'completed'): ?>
                        <a href="<?= APP_URL ?>/index.php?page=review&booking_id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary">Review</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
