<?php
// search_process.php — Bookings: Search result (POST)
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['assigned_by'])) { header('Location: search.php'); exit; }
$val = $_POST['assigned_by'];
try { $record = $bookings->findOne(['assigned_by' => $val]); } catch (Exception $e) { $record = null; $dbError = $e->getMessage(); }
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>DineBook — Search Results</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="/dinebook/css/style.css"></head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Bookings</a></li><li class="breadcrumb-item active">Search Results</li></ol></nav>
        <h2 class="page-header">Search Results</h2>
        <?php if (isset($dbError)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif ($record): ?>
        <div class="table-responsive"><table class="table table-striped table-hover table-bordered">
            <tr><th>Field</th><th>Value</th></tr>
            <tr><td>Reservation ID</td><td><?php echo htmlspecialchars($record['reservation_id'] ?? ''); ?></td></tr>
            <tr><td>Table ID</td><td><?php echo htmlspecialchars($record['table_id'] ?? ''); ?></td></tr>
            <tr><td>Guest ID</td><td><?php echo htmlspecialchars($record['guest_id'] ?? ''); ?></td></tr>
            <tr><td>Booking Date</td><td><?php echo htmlspecialchars($record['booking_date'] ?? ''); ?></td></tr>
            <tr><td>Check-in</td><td><?php echo htmlspecialchars($record['check_in_time'] ?? ''); ?></td></tr>
            <tr><td>Check-out</td><td><?php echo htmlspecialchars($record['check_out_time'] ?? ''); ?></td></tr>
            <tr><td>Party Size</td><td><?php echo htmlspecialchars($record['actual_party_size'] ?? ''); ?></td></tr>
            <tr><td>Assigned By</td><td><?php echo htmlspecialchars($record['assigned_by'] ?? ''); ?></td></tr>
            <tr><td>Booking Status</td><td><span class="badge badge-status-<?php echo htmlspecialchars($record['booking_status'] ?? 'pending'); ?>"><?php echo htmlspecialchars($record['booking_status'] ?? 'pending'); ?></span></td></tr>
            <tr><td>Payment Status</td><td><?php echo htmlspecialchars($record['payment_status'] ?? 'unpaid'); ?></td></tr>
            <tr><td>Special Setup</td><td><?php $ss=$record['special_setup']??[]; if($ss instanceof MongoDB\Model\BSONArray) $ss=$ss->getArrayCopy(); echo is_array($ss)&&count($ss)>0?htmlspecialchars(implode(', ',$ss)):'None'; ?></td></tr>
            <tr><td>Hostess Notes</td><td><?php echo htmlspecialchars($record['hostess_notes'] ?? ''); ?></td></tr>
        </table></div>
        <?php else: ?><div class="alert alert-warning">No booking found for "<?php echo htmlspecialchars($val); ?>".</div><?php endif; ?>
        <a href="search.php" class="btn btn-primary">Search Again</a>
        <a href="../index.php" class="btn btn-link">&larr; Back to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script><script src="/dinebook/js/app.js"></script>
</body>
</html>
