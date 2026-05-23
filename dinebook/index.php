<?php
// index.php — DineBook Dashboard (staff/host/admin only)
require_once __DIR__ . '/auth/guard.php';

// Guests should use the guest portal, not the staff dashboard
if (($_SESSION['role'] ?? '') === 'guest') {
    header('Location: /dinebook/guest/dashboard.php');
    exit;
}

require_once __DIR__ . '/config.php';

try {
    $totalRes    = $reservations->countDocuments();
    $totalGuests = $guests->countDocuments();
    $totalTables = $tables->countDocuments(['status' => 'available']);
    $today       = date('Y-m-d');
    $todayBook   = $bookings->countDocuments(['booking_date' => $today]);
} catch (Exception $e) {
    $totalRes = $totalGuests = $totalTables = $todayBook = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineBook — Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dinebook/index.php">DineBook</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active" href="/dinebook/index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/dinebook/reservations/report.php">Reservations</a></li>
                    <li class="nav-item"><a class="nav-link" href="/dinebook/tables/report.php">Tables</a></li>
                    <li class="nav-item"><a class="nav-link" href="/dinebook/guests/report.php">Guests</a></li>
                    <li class="nav-item"><a class="nav-link" href="/dinebook/bookings/report.php">Bookings</a></li>
                    <li class="nav-item"><a class="nav-link" href="/dinebook/noshows/report.php">No-Shows</a></li>
                    <li class="nav-item"><a class="nav-link" href="/dinebook/guest/floormap.php">Floor Map</a></li>
                    <li class="nav-item"><a class="nav-link" href="/dinebook/auth/logout.php">Logout</a></li>
                </ul>
                <span class="navbar-text ms-auto" style="color:var(--dinebook-gold);">Welcome, <?php echo htmlspecialchars($_SESSION['user'] ?? 'Guest'); ?></span>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Hero -->
        <div class="text-center mb-4">
            <h1 style="color:var(--dinebook-red); font-weight:700;">DineBook</h1>
            <p class="lead text-muted">Restaurant Table Reservation & Management System</p>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-3"><div class="card stat-card"><div class="card-body text-center"><div class="stat-number"><?php echo $totalRes; ?></div><small class="text-muted">Total Reservations</small></div></div></div>
            <div class="col-md-3 col-6 mb-3"><div class="card stat-card"><div class="card-body text-center"><div class="stat-number"><?php echo $totalGuests; ?></div><small class="text-muted">Total Guests</small></div></div></div>
            <div class="col-md-3 col-6 mb-3"><div class="card stat-card"><div class="card-body text-center"><div class="stat-number"><?php echo $totalTables; ?></div><small class="text-muted">Tables Available</small></div></div></div>
            <div class="col-md-3 col-6 mb-3"><div class="card stat-card"><div class="card-body text-center"><div class="stat-number"><?php echo $todayBook; ?></div><small class="text-muted">Bookings Today</small></div></div></div>
        </div>

        <!-- Entity Cards -->
        <div class="row">
            <!-- Reservations -->
            <div class="col-md-4 mb-4"><div class="card entity-card h-100"><div class="card-body text-center">
                <div class="entity-icon">&#128221;</div>
                <h5 class="card-title mt-2">Reservations</h5>
                <p class="card-text text-muted">Manage guest reservations</p>
                <a href="/dinebook/reservations/create.html" class="btn btn-primary btn-sm">New</a>
                <a href="/dinebook/reservations/report.php" class="btn btn-outline-secondary btn-sm">View All</a>
                <a href="/dinebook/reservations/search.php" class="btn btn-outline-secondary btn-sm">Search</a>
            </div></div></div>
            <!-- Tables -->
            <div class="col-md-4 mb-4"><div class="card entity-card h-100"><div class="card-body text-center">
                <div class="entity-icon">&#128373;</div>
                <h5 class="card-title mt-2">Tables</h5>
                <p class="card-text text-muted">Table inventory & layout</p>
                <a href="/dinebook/tables/create.html" class="btn btn-primary btn-sm">New</a>
                <a href="/dinebook/tables/report.php" class="btn btn-outline-secondary btn-sm">View All</a>
                <a href="/dinebook/tables/search.php" class="btn btn-outline-secondary btn-sm">Search</a>
            </div></div></div>
            <!-- Guests -->
            <div class="col-md-4 mb-4"><div class="card entity-card h-100"><div class="card-body text-center">
                <div class="entity-icon">&#128100;</div>
                <h5 class="card-title mt-2">Guests</h5>
                <p class="card-text text-muted">Guest CRM profiles</p>
                <a href="/dinebook/guests/create.html" class="btn btn-primary btn-sm">New</a>
                <a href="/dinebook/guests/report.php" class="btn btn-outline-secondary btn-sm">View All</a>
                <a href="/dinebook/guests/search.php" class="btn btn-outline-secondary btn-sm">Search</a>
            </div></div></div>
            <!-- Bookings -->
            <div class="col-md-4 mb-4"><div class="card entity-card h-100"><div class="card-body text-center">
                <div class="entity-icon">&#128197;</div>
                <h5 class="card-title mt-2">Bookings</h5>
                <p class="card-text text-muted">Reservation + table assignments</p>
                <a href="/dinebook/bookings/create.php" class="btn btn-primary btn-sm">New</a>
                <a href="/dinebook/bookings/report.php" class="btn btn-outline-secondary btn-sm">View All</a>
                <a href="/dinebook/bookings/search.php" class="btn btn-outline-secondary btn-sm">Search</a>
            </div></div></div>
            <!-- No-Shows -->
            <div class="col-md-4 mb-4"><div class="card entity-card h-100"><div class="card-body text-center">
                <div class="entity-icon">&#128683;</div>
                <h5 class="card-title mt-2">No-Show Reports</h5>
                <p class="card-text text-muted">Track & manage no-shows</p>
                <a href="/dinebook/noshows/create.php" class="btn btn-primary btn-sm">New</a>
                <a href="/dinebook/noshows/report.php" class="btn btn-outline-secondary btn-sm">View All</a>
                <a href="/dinebook/noshows/search.php" class="btn btn-outline-secondary btn-sm">Search</a>
            </div></div></div>
            <!-- JSON Report -->
            <div class="col-md-4 mb-4"><div class="card entity-card h-100"><div class="card-body text-center">
                <div class="entity-icon">&#128202;</div>
                <h5 class="card-title mt-2">JSON Reports</h5>
                <p class="card-text text-muted">Dynamic API-powered views</p>
                <a href="/dinebook/reservations/report_json.php" class="btn btn-primary btn-sm">Reservations JSON</a>
                <a href="/dinebook/api/guests.php" class="btn btn-outline-secondary btn-sm">Guests API</a>
            </div></div></div>
        </div>
    </div>

    <div class="footer container"><p>DineBook &copy; 2026 — SDG 9: Industry, Innovation & Infrastructure | SDG 12: Responsible Consumption</p></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
