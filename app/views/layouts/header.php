<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Home') ?> | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --brand: #252525;
            --brand-soft: #1b2535;
            --accent: #27a8b5;
            --accent-dark: #208b98;
            --surface: #1b2535;
            --canvas: #101416;
            --ink: #f2f5f8;
            --muted: #9aa6b2;
            --line: #303b48;
            --radius: 16px;
            --shadow: 0 12px 32px rgba(0, 0, 0, .24);
        }
        body {
            background: var(--canvas);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
            min-height: 100vh;
        }
        .text-muted { color: var(--muted) !important; }
        .navbar { background: var(--brand) !important; box-shadow: 0 4px 20px rgba(0, 0, 0, .22); }
        .navbar-brand { letter-spacing: -.025em; }
        .navbar .nav-link { color: rgba(255,255,255,.78); transition: color .18s ease; }
        .navbar .nav-link:hover, .navbar .nav-link:focus { color: #fff; }
        main.container { min-height: 65vh; }
        .btn-brand { background: var(--accent); border-color: var(--accent); color: #07191b; }
        .btn-brand:hover, .btn-brand:focus { background: var(--accent-dark); border-color: var(--accent-dark); color: #fff; }
        .btn { border-radius: 10px; font-weight: 600; }
        .form-control, .form-select {
            background-color: #171d22;
            border-color: var(--line);
            color: var(--ink);
            border-radius: 10px;
            min-height: 44px;
        }
        .form-control::placeholder { color: #7e8a96; }
        .form-control:disabled, .form-select:disabled { background-color: #1a2025; color: var(--muted); }
        .form-select option { background: #171d22; color: var(--ink); }
        .form-control:focus, .form-select:focus {
            background-color: #171d22;
            border-color: var(--accent);
            color: var(--ink);
            box-shadow: 0 0 0 .22rem rgba(39, 168, 181, .16);
        }
        .card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            color: var(--ink);
            box-shadow: 0 6px 20px rgba(0, 0, 0, .12);
        }
        .card-artisan { transition: transform .18s ease, box-shadow .18s ease; }
        .card-artisan:hover { transform: translateY(-4px); box-shadow: var(--shadow); }
        .rating-stars { color: #d69e2e; }
        .badge-verified { background: #25866b; }
        footer { background: var(--brand); color: #cbd5e0; }
        .admin-dashboard { max-width: 1180px; margin: 0 auto; }
        .admin-hero { padding: 28px 16px 36px; text-align: center; }
        .admin-hero-kicker { color: var(--accent); font-size: .75rem; font-weight: 750; letter-spacing: .16em; }
        .admin-heading { color: #f7f9fb; letter-spacing: -.035em; }
        .admin-hero .admin-heading { font-size: clamp(1.7rem, 3vw, 2.2rem); }
        .admin-hero-copy { color: #9aa6b2; }
        .admin-stat {
            border: 1px solid var(--line);
            border-radius: var(--radius);
            background: var(--surface);
            box-shadow: 0 6px 20px rgba(0, 0, 0, .14);
            height: 100%;
            transition: border-color .18s ease, transform .18s ease, background .18s ease;
        }
        .admin-stat:hover { background: #202c3e; border-color: #435265; transform: translateY(-2px); }
        .admin-stat-label { color: #aab5c0; font-size: .82rem; font-weight: 650; }
        .admin-stat-value { color: #f7f9fb; font-size: 1.8rem; font-weight: 750; letter-spacing: -.04em; }
        .admin-stat-icon {
            align-items: center; background: rgba(39, 168, 181, .14); border-radius: 12px; color: #4bc1ca;
            display: inline-flex; height: 42px; justify-content: center; width: 42px;
        }
        .admin-filter { border-radius: 999px; }
        .admin-filter.btn-dark { background: var(--accent); border-color: var(--accent); color: #07191b; }
        .admin-filter.btn-outline-secondary { border-color: #485462; color: #c3cbd3; }
        .admin-filter.btn-outline-secondary:hover { background: #293443; border-color: #657384; color: #fff; }
        .admin-table { --bs-table-bg: transparent; --bs-table-color: var(--ink); --bs-table-border-color: var(--line); margin-bottom: 0; }
        .admin-table thead th {
            background: #171e27; border-bottom: 1px solid var(--line); color: #9aa6b2;
            font-size: .72rem; font-weight: 700; letter-spacing: .07em; padding: 14px 18px;
            text-transform: uppercase; white-space: nowrap;
        }
        .admin-table tbody tr { background: var(--surface); }
        .admin-table tbody tr:hover { background: #202c3e; }
        .admin-table tbody td { border-color: var(--line); padding: 17px 18px; vertical-align: middle; }
        .admin-avatar {
            align-items: center; background: rgba(39, 168, 181, .14); border-radius: 12px; color: #65cbd2;
            display: inline-flex; flex: 0 0 42px; font-weight: 750; height: 42px; justify-content: center;
        }
        .status-pill { border-radius: 999px; font-size: .76rem; font-weight: 700; padding: .42rem .7rem; }
        .status-pending { background: rgba(238, 178, 71, .14); color: #f0c66f; }
        .status-approved { background: rgba(58, 181, 127, .14); color: #75d6a5; }
        .status-declined { background: rgba(222, 103, 95, .14); color: #f0928c; }
        .admin-empty { background: #171e27; border: 1px dashed #3a4653; border-radius: 14px; }
        .admin-dashboard .border-bottom, .admin-dashboard .border-top { border-color: var(--line) !important; }
        .admin-dashboard .btn-success { background: #218c68; border-color: #218c68; color: #fff; }
        .admin-dashboard .btn-outline-danger { border-color: #a95350; color: #f0928c; }
        .admin-dashboard .btn-outline-danger:hover { background: #873d3b; border-color: #873d3b; color: #fff; }
        .admin-dashboard .alert-success { background: #173a30; color: #9fe0bf; }
        .admin-dashboard .alert-secondary { background: #292f37; color: #d2d7de; }
        .alert { border-radius: 12px; }
        .table-responsive { color: var(--ink); }
        @media (max-width: 767.98px) {
            .admin-table thead { display: none; }
            .admin-table tbody tr { display: block; padding: 10px 0; }
            .admin-table tbody td { border: 0; display: block; padding: 6px 16px; }
            .admin-table tbody td:last-child { padding-top: 12px; }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= APP_URL ?>/index.php">
            <i class="bi bi-tools"></i> Artisans Bookings
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/index.php?page=artisans">Find Artisans</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/index.php?page=recommendations">Recommendations</a></li>
            </ul>
            <ul class="navbar-nav">
                <?php if (Auth::check()): ?>
                    <?php if (Auth::is('admin')): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/index.php?page=admin-artisans">Admin</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/index.php?page=dashboard">Dashboard</a></li>
                    <?php endif; ?>
                    <?php if (Auth::is('customer')): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/index.php?page=my-bookings">My Bookings</a></li>
                    <?php elseif (Auth::is('artisan')): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/index.php?page=manage-bookings">Manage Bookings</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><span class="nav-link text-white-50"><?= htmlspecialchars(Auth::user()['first_name']) ?></span></li>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/index.php?page=logout">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/index.php?page=login">Login</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-brand btn-sm ms-2 px-3" href="<?= APP_URL ?>/index.php?page=register">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-4">
