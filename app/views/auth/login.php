<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="mb-4">Login</h3>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-brand w-100">Login</button>
                </form>
                <p class="mt-3 mb-0 text-center small">
                    No account? <a href="<?= APP_URL ?>/index.php?page=register">Register</a>
                </p>
                <hr>
                <p class="small text-muted mb-0">
                    <strong>Demo accounts</strong> (password: <code>password</code> or the seed hash works with common test):<br>
                    Customer: lerato@example.com<br>
                    Artisan: thabo@example.com
                </p>
            </div>
        </div>
    </div>
</div>
