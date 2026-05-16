<?php
// search_v2.php — Guests: Search by Selectable Field
require_once __DIR__ . '/../auth/guard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineBook — Search by Field</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Guests</a></li><li class="breadcrumb-item active">Search by Field</li></ol></nav>
        <h2 class="page-header">Search Guest by Field</h2>
        <div class="card"><div class="card-body form-section">
            <form method="post" action="search_process2.php">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="search_field" class="form-label">Search By</label>
                        <select class="form-select" id="search_field" name="search_field" required>
                            <option value="">-- Select Field --</option>
                            <option value="full_name">Full Name</option>
                            <option value="email">Email</option>
                            <option value="phone">Phone</option>
                            <option value="preferred_zone">Preferred Zone</option>
                            <option value="loyalty_tier">Loyalty Tier</option>
                            <option value="contact_preference">Contact Preference</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="search_value" class="form-label">Search Value</label>
                        <input type="text" class="form-control" id="search_value" name="search_value" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div></div>
        <a href="../index.php" class="btn btn-link mt-3">&larr; Back to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/dinebook/js/app.js"></script>
</body>
</html>
