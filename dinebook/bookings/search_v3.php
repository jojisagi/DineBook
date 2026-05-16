<?php
// search_v3.php — Bookings: Search by GET (link-based)
require_once __DIR__ . '/../auth/guard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>DineBook — Search Booking by Link</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="/dinebook/css/style.css"></head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Bookings</a></li><li class="breadcrumb-item active">Search by Link</li></ol></nav>
        <h2 class="page-header">Search Booking by Link</h2>
        <p>Click a status to search:</p>
        <ul class="list-group">
            <li class="list-group-item"><a href="search_process3.php?booking_status=pending">Pending Bookings</a></li>
            <li class="list-group-item"><a href="search_process3.php?booking_status=confirmed">Confirmed Bookings</a></li>
            <li class="list-group-item"><a href="search_process3.php?booking_status=seated">Seated Bookings</a></li>
            <li class="list-group-item"><a href="search_process3.php?booking_status=completed">Completed Bookings</a></li>
            <li class="list-group-item"><a href="search_process3.php?booking_status=no-show">No-Show Bookings</a></li>
        </ul>
        <a href="../index.php" class="btn btn-link mt-3">&larr; Back to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script><script src="/dinebook/js/app.js"></script>
</body>
</html>
