<?php
// report.php — Reservations: List All
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

try {
    $cursor = $reservations->find([], ['sort' => ['created_at' => -1]]);
    $records = iterator_to_array($cursor);
} catch (Exception $e) {
    $records = [];
    $dbError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineBook — All Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item active">All Reservations</li></ol></nav>
        <h2 class="page-header">All Reservations Report</h2>

        <div class="mb-3">
            <input type="text" id="table-filter" class="form-control" placeholder="Filter results...">
        </div>

        <?php if (isset($dbError)): ?>
            <div class="alert alert-danger">Database error: <?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif (count($records) === 0): ?>
            <div class="alert alert-warning">No reservations registered.</div>
        <?php else: ?>
            <p>Total: <strong><?php echo count($records); ?></strong> reservations</p>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr><th>Reservation ID</th><th>Guest Name</th><th>Email</th><th>Phone</th><th>Date</th><th>Time</th><th>Party</th><th>Zone</th><th>Dietary</th><th>Occasion</th><th>Guest Type</th><th>Status</th><th>Confirmed</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td style="font-family:monospace; font-size:0.8rem;"><?php echo htmlspecialchars((string)$r['_id']); ?></td>
                            <td><?php echo htmlspecialchars($r['guest_name']); ?></td>
                            <td><?php echo htmlspecialchars($r['email']); ?></td>
                            <td><?php echo htmlspecialchars($r['phone']); ?></td>
                            <td><?php echo htmlspecialchars($r['reservation_date']); ?></td>
                            <td><?php echo htmlspecialchars($r['arrival_time']); ?></td>
                            <td><?php echo htmlspecialchars($r['party_size']); ?></td>
                            <td><?php echo htmlspecialchars($r['zone']); ?></td>
                            <td><?php
                                $d = $r['dietary_restrictions'] ?? [];
                                if ($d instanceof MongoDB\Model\BSONArray) $d = $d->getArrayCopy();
                                echo is_array($d) && count($d) > 0 ? htmlspecialchars(implode(', ', $d)) : 'None';
                            ?></td>
                            <td><?php echo htmlspecialchars($r['occasion'] ?? 'none'); ?></td>
                            <td><span class="badge badge-tier-<?php echo htmlspecialchars($r['guest_type'] ?? 'new'); ?>"><?php echo htmlspecialchars($r['guest_type'] ?? 'new'); ?></span></td>
                            <td><span class="badge badge-status-<?php echo htmlspecialchars($r['status'] ?? 'active'); ?>"><?php echo htmlspecialchars($r['status'] ?? 'active'); ?></span></td>
                            <td><?php echo (!empty($r['confirmation_sent'])) ? 'Yes' : 'No'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <a href="../index.php" class="btn btn-link">&larr; Back to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/dinebook/js/app.js"></script>
</body>
</html>
