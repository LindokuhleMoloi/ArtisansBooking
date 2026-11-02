<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="mb-3">Book: <?= htmlspecialchars($service['title']) ?></h3>
                <p class="text-muted">
                    with <?= htmlspecialchars($service['first_name'] . ' ' . $service['last_name']) ?>
                    · R<?= number_format($service['price'], 2) ?>
                    · <?= $service['duration_minutes'] ?> minutes
                </p>

                <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

                <?php if (!$success): ?>
                <form method="post">
                    <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="booking_date" class="form-control" required
                               min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Preferred start time</label>
                        <input type="time" name="start_time" class="form-control" required value="09:00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Service address</label>
                        <input type="text" name="address" class="form-control" placeholder="Where should the artisan come?">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (optional)</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-brand w-100">Request Booking</button>
                </form>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/index.php?page=my-bookings" class="btn btn-outline-primary">View my bookings</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
