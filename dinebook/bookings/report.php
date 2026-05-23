<?php
// report.php — Bookings: List All (with application-level joins)
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

try {
    $cursor = $bookings->find([], ['sort' => ['created_at' => -1]]);
    $allRows = iterator_to_array($cursor);
    // Deduplicate multi-slot bookings: keep only one row per reservation_id
    $seen = [];
    $records = [];
    foreach ($allRows as $row) {
        $resId = (string)($row['reservation_id'] ?? '');
        if ($resId !== '' && isset($seen[$resId])) continue;
        if ($resId !== '') $seen[$resId] = true;
        $records[] = $row;
    }
} catch (Exception $e) {
    $records = [];
    $dbError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>DineBook — All Bookings</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="/dinebook/css/style.css"></head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item active">All Bookings</li></ol></nav>
        <h2 class="page-header">All Bookings Report</h2>
        <div class="mb-3"><input type="text" id="table-filter" class="form-control" placeholder="Filter results..."></div>
        <?php if (isset($dbError)): ?>
            <div class="alert alert-danger">Database error: <?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif (count($records) === 0): ?>
            <div class="alert alert-warning">No bookings registered.</div>
        <?php else: ?>
            <p>Total: <strong><?php echo count($records); ?></strong> bookings</p>
            <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead><tr><th>Booking ID</th><th>Guest Name</th><th>Table #</th><th>Date</th><th>Check-in</th><th>Check-out</th><th>Party</th><th>Assigned By</th><th>Status</th><th>Payment</th><th>Setup</th></tr></thead>
                <tbody>
                <?php foreach ($records as $b):
                    // Application-level join: resolve references
                    $guestName = '—'; $tableNum = '—';
                    try {
                        if (!empty($b['reservation_id'])) {
                            $res = $reservations->findOne(['_id' => new MongoDB\BSON\ObjectId($b['reservation_id'])]);
                            if ($res) $guestName = $res['guest_name'] ?? '—';
                        }
                    } catch (Exception $e) { $guestName = $b['reservation_id'] ?? '—'; }
                    try {
                        if (!empty($b['table_id'])) {
                            $tbl = $tables->findOne(['_id' => new MongoDB\BSON\ObjectId($b['table_id'])]);
                            if ($tbl) $tableNum = $tbl['table_number'] ?? '—';
                        }
                    } catch (Exception $e) { $tableNum = $b['table_id'] ?? '—'; }
                    $setup = $b['special_setup'] ?? [];
                    if ($setup instanceof MongoDB\Model\BSONArray) $setup = $setup->getArrayCopy();
                ?>
                <tr>
                    <td style="font-family:monospace; font-size:0.8rem;"><?php echo htmlspecialchars((string)$b['_id']); ?></td>
                    <td><?php echo htmlspecialchars($guestName); ?></td>
                    <td><?php echo htmlspecialchars($tableNum); ?></td>
                    <td><?php echo htmlspecialchars($b['booking_date'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($b['check_in_time'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($b['check_out_time'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($b['actual_party_size'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($b['assigned_by'] ?? ''); ?></td>
                    <td><span class="badge badge-status-<?php echo htmlspecialchars($b['booking_status'] ?? 'pending'); ?>"><?php echo htmlspecialchars($b['booking_status'] ?? 'pending'); ?></span></td>
                    <td><?php echo htmlspecialchars($b['payment_status'] ?? 'unpaid'); ?></td>
                    <td><?php echo is_array($setup) && count($setup) > 0 ? htmlspecialchars(implode(', ', $setup)) : 'None'; ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
        <a href="../index.php" class="btn btn-link">&larr; Back to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script><script src="/dinebook/js/app.js"></script>
</body>
</html>
