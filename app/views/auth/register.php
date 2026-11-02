<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="mb-4">Create Account</h3>
                <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">I want to</label>
                        <select name="role" id="roleSelect" class="form-select" onchange="toggleArtisanFields()">
                            <option value="customer">Book services (Customer)</option>
                            <option value="artisan">Offer services (Artisan)</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First name</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last name</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" name="location_city" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Province</label>
                            <input type="text" name="location_province" class="form-control">
                        </div>
                    </div>
                    <div id="artisanFields" style="display:none">
                        <div class="mb-3">
                            <label class="form-label">Business name</label>
                            <input type="text" name="business_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Years of experience</label>
                            <input type="number" name="years_experience" class="form-control" min="0" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (min 6 characters)</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-brand w-100">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function toggleArtisanFields() {
    document.getElementById('artisanFields').style.display =
        document.getElementById('roleSelect').value === 'artisan' ? 'block' : 'none';
}
</script>
