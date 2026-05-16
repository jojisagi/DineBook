<?php
// report_json.php — Reservations: JSON-powered report (fetches from API)
require_once __DIR__ . '/../auth/guard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineBook — Reservations (JSON Report)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Reservations</a></li><li class="breadcrumb-item active">JSON Report</li></ol></nav>
        <h2 class="page-header">Reservations Report (via JSON API)</h2>

        <div id="loading" class="text-center my-4">
            <div class="spinner-border text-danger" role="status"><span class="visually-hidden">Loading...</span></div>
            <p>Loading reservations...</p>
        </div>

        <div class="mb-3 d-none" id="filter-bar">
            <input type="text" id="table-filter" class="form-control" placeholder="Filter results...">
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered d-none" id="json-table">
                <thead><tr><th>Guest</th><th>Date</th><th>Zone</th><th>Party</th><th>Status</th></tr></thead>
                <tbody id="json-tbody"></tbody>
            </table>
        </div>

        <div id="no-data" class="alert alert-warning d-none">No reservations found.</div>
        <div id="api-error" class="alert alert-danger d-none"></div>

        <a href="report.php" class="btn btn-primary mt-3">Standard Report</a>
        <a href="../index.php" class="btn btn-link mt-3">&larr; Back to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/dinebook/js/app.js"></script>
    <script>
        // Fetch reservations from JSON API
        fetch('../api/reservations.php')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                document.getElementById('loading').classList.add('d-none');
                if (data.length === 0) {
                    document.getElementById('no-data').classList.remove('d-none');
                    return;
                }
                document.getElementById('json-table').classList.remove('d-none');
                document.getElementById('filter-bar').classList.remove('d-none');
                var tbody = document.getElementById('json-tbody');
                data.forEach(function(r) {
                    var tr = document.createElement('tr');
                    tr.innerHTML = '<td>' + r.guest + '</td><td>' + r.date + '</td><td>' + r.zone + '</td><td>' + r.party + '</td><td><span class="badge badge-status-' + r.status + '">' + r.status + '</span></td>';
                    tbody.appendChild(tr);
                });
            })
            .catch(function(err) {
                document.getElementById('loading').classList.add('d-none');
                document.getElementById('api-error').classList.remove('d-none');
                document.getElementById('api-error').textContent = 'Error loading data: ' + err.message;
            });
    </script>
</body>
</html>
