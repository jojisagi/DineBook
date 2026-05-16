<?php
// search_process3.php — Bookings: Search by GET parameter
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

if (!isset($_GET['booking_status']) || empty($_GET['booking_status'])) { header('Location: search_v3.php'); exit; }
$status = $_GET['booking_status'];
try { $records = iterator_to_array($bookings->find(['booking_status' => $status])); } catch (Exception $e) { $records = []; $dbError = $e->getMessage(); }
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>DineBook — Search Results (GET)</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="/dinebook/css/style.css"></head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Bookings</a></li><li class="breadcrumb-item active">Results (GET)</li></ol></nav>
        <h2 class="page-header">Bookings — Status: <?php echo htmlspecialchars($status); ?></h2>
        <?php if (isset($dbError)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif (count($records) === 0): ?><div class="alert alert-warning">No bookings found with status "<?php echo htmlspecialchars($status); ?>".</div>
        <?php else: ?>
        <div class="table-responsive"><table class="table table-striped table-hover table-bordered">
            <thead><tr><th>Date</th><th>Party</th><th>Assigned By</th><th>Status</th><th>Payment</th></tr></thead>
            <tbody><?php foreach ($records as $r): ?>
            <tr><td><?php echo htmlspecialchars($r['booking_date']??''); ?></td><td><?php echo htmlspecialchars($r['actual_party_size']??''); ?></td><td><?php echo htmlspecialchars($r['assigned_by']??''); ?></td><td><span class="badge badge-status-<?php echo htmlspecialchars($r['booking_status']??'pending'); ?>"><?php echo htmlspecialchars($r['booking_status']??'pending'); ?></span></td><td><?php echo htmlspecialchars($r['payment_status']??'unpaid'); ?></td></tr>
            <?php endforeach; ?></tbody>
        </table></div>
        <?php endif; ?>
        <a href="search_v3.php" class="btn btn-primary">Back to Links</a>
        <a href="../index.php" class="btn btn-link">&larr; Back to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script><script src="/dinebook/js/app.js"></script>
</body>
</html>
