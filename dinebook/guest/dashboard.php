<?php
// guest/dashboard.php — Guest-facing dashboard
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

// Allow both guests AND staff/admin to see this page (staff can preview guest view)
$isGuest = ($_SESSION['role'] ?? '') === 'guest';

// Count guest's own reservations
try {
    $myResCount = $bookings->countDocuments(['guest_user' => $_SESSION['user']]);
} catch (Exception $e) {
    $myResCount = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineBook — Guest Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dinebook/guest/dashboard.php">DineBook</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active" href="/dinebook/guest/dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/dinebook/guest/floormap.php">Reserve a Table</a></li>
                    <li class="nav-item"><a class="nav-link" href="/dinebook/guest/my_reservations.php">My Reservations</a></li>
                    <?php if (!$isGuest): ?>
                        <li class="nav-item"><a class="nav-link" href="/dinebook/index.php">Staff Dashboard</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="/dinebook/auth/logout.php">Logout</a></li>
                </ul>
                <span class="navbar-text ms-auto" style="color:var(--dinebook-gold);">
                    Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="text-center mb-4">
            <h1 style="color:var(--dinebook-red); font-weight:700;">Welcome to DineBook</h1>
            <p class="lead text-muted">Find your perfect table and make a reservation</p>
        </div>

        <!-- Quick Stats -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-4 col-6 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div class="stat-number"><?php echo $myResCount; ?></div>
                        <small class="text-muted">My Reservations</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="row justify-content-center">
            <!-- Reserve a Table -->
            <div class="col-md-5 mb-4">
                <div class="card entity-card h-100">
                    <div class="card-body text-center">
                        <div class="entity-icon">&#128205;</div>
                        <h5 class="card-title mt-2">Reserve a Table</h5>
                        <p class="card-text text-muted">Browse the interactive floor map, pick your zone (interior or exterior), choose a table and time slot.</p>
                        <a href="/dinebook/guest/floormap.php" class="btn btn-primary">View Floor Map</a>
                    </div>
                </div>
            </div>
            <!-- My Reservations -->
            <div class="col-md-5 mb-4">
                <div class="card entity-card h-100">
                    <div class="card-body text-center">
                        <div class="entity-icon">&#128197;</div>
                        <h5 class="card-title mt-2">My Reservations</h5>
                        <p class="card-text text-muted">View your upcoming reservations or cancel if your plans change.</p>
                        <a href="/dinebook/guest/my_reservations.php" class="btn btn-primary">View Reservations</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer container"><p>DineBook &copy; 2026 — SDG 9: Industry, Innovation & Infrastructure | SDG 12: Responsible Consumption</p></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
