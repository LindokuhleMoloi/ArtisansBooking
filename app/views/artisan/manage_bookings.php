<h2 class="mb-4">Manage Bookings</h2>
<?php if (empty($bookings)): ?>
    <div class="alert alert-info">No bookings received yet.</div>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-hover bg-white shadow-sm">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $b): ?>
            <tr>
                <td><?= $b['id'] ?></td>
                <td>
                    <?= htmlspecialchars($b['customer_first'] . ' ' . $b['customer_last']) ?>
                    <?php if ($b['customer_phone']): ?><br><small class="text-muted"><?= htmlspecialchars($b['customer_phone']) ?></small><?php endif; ?>
                </td>
                <td><?= htmlspecialchars($b['service_title']) ?></td>
                <td><?= date('d M Y', strtotime($b['booking_date'])) ?></td>
                <td><?= substr($b['start_time'], 0, 5) ?></td>
                <td><span class="badge bg-secondary"><?= ucfirst($b['status']) ?></span></td>
                <td>
                    <?php if (in_array($b['status'], ['pending', 'confirmed'])): ?>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                        <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                            <option value="">Change…</option>
                            <option value="confirmed">Confirm</option>
                            <option value="completed">Mark completed</option>
                            <option value="cancelled">Cancel</option>
                            <option value="no_show">No-show</option>
                        </select>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
