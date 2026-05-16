<?php
// search_process3.php — No-Shows: Search by GET parameter
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';
if (!isset($_GET['reason_category']) || empty($_GET['reason_category'])) { header('Location: search_v3.php'); exit; }
$reason = $_GET['reason_category'];
try { $records = iterator_to_array($noshows->find(['reason_category' => $reason])); } catch (Exception $e) { $records = []; $dbError = $e->getMessage(); }
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>DineBook — No-Show Results (GET)</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="/dinebook/css/style.css"></head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link active" href="report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">No-Shows</a></li><li class="breadcrumb-item active">Results (GET)</li></ol></nav>
        <h2 class="page-header">No-Shows — Reason: <?php echo htmlspecialchars($reason); ?></h2>
        <?php if (isset($dbError)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif (count($records)===0): ?><div class="alert alert-warning">No records found for reason "<?php echo htmlspecialchars($reason); ?>".</div>
        <?php else: ?>
        <div class="table-responsive"><table class="table table-striped table-hover table-bordered">
            <thead><tr><th>Guest</th><th>Date</th><th>Party</th><th>Zone</th><th>Impact</th><th>Follow-up</th></tr></thead>
            <tbody><?php foreach ($records as $r): ?>
            <tr><td><?php echo htmlspecialchars($r['guest_name']??''); ?></td><td><?php echo htmlspecialchars($r['reservation_date']??''); ?></td><td><?php echo htmlspecialchars($r['party_size']??''); ?></td><td><?php echo htmlspecialchars($r['zone']??''); ?></td><td><?php echo htmlspecialchars($r['impact_assessment']??'low'); ?></td><td><?php echo htmlspecialchars($r['follow_up_action']??'none'); ?></td></tr>
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
