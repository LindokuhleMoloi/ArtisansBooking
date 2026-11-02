<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3>Leave a Review</h3>
                <p class="text-muted">Booking #<?= $booking['id'] ?> · <?= htmlspecialchars($booking['service_title']) ?></p>
                <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
                <?php if (!$success): ?>
                <form method="post">
                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <select name="rating" class="form-select" required>
                            <option value="">Select…</option>
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <option value="<?= $i ?>"><?= $i ?> star<?= $i > 1 ? 's' : '' ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comment (optional)</label>
                        <textarea name="comment" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-brand w-100">Submit Review</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
