<?php
// search_process2.php — Reservations: Search by selectable field
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['search_field']) || empty($_POST['search_value'])) {
    header('Location: search_v2.php');
    exit;
}
$field = $_POST['search_field'];
$value = $_POST['search_value'];
$filter = [];
switch ($field) {
    case 'guest_name':  $filter = ['guest_name' => $value]; break;
    case 'email':       $filter = ['email' => $value]; break;
    case 'phone':       $filter = ['phone' => $value]; break;
    case 'zone':        $filter = ['zone' => $value]; break;
    case 'occasion':    $filter = ['occasion' => $value]; break;
    case 'guest_type':  $filter = ['guest_type' => $value]; break;
    default:            $filter = ['guest_name' => $value]; break;
}
try {
    $cursor = $reservations->find($filter);
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
    <title>DineBook — Search Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Reservations</a></li><li class="breadcrumb-item active">Search Results</li></ol></nav>
        <h2 class="page-header">Search Results</h2>
        <p>Searching by <strong><?php echo htmlspecialchars($field); ?></strong> = "<?php echo htmlspecialchars($value); ?>"</p>
        <?php if (isset($dbError)): ?>
            <div class="alert alert-danger">Database error: <?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif (count($records) === 0): ?>
            <div class="alert alert-warning">No reservations found.</div>
        <?php else: ?>
            <p><?php echo count($records); ?> reservation(s) found.</p>
            <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead><tr><th>Guest Name</th><th>Email</th><th>Phone</th><th>Date</th><th>Time</th><th>Party</th><th>Zone</th><th>Occasion</th><th>Guest Type</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($records as $r): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['guest_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['email']); ?></td>
                    <td><?php echo htmlspecialchars($r['phone']); ?></td>
                    <td><?php echo htmlspecialchars($r['reservation_date']); ?></td>
                    <td><?php echo htmlspecialchars($r['arrival_time']); ?></td>
                    <td><?php echo htmlspecialchars($r['party_size']); ?></td>
                    <td><?php echo htmlspecialchars($r['zone']); ?></td>
                    <td><?php echo htmlspecialchars($r['occasion'] ?? 'none'); ?></td>
                    <td><span class="badge badge-tier-<?php echo htmlspecialchars($r['guest_type'] ?? 'new'); ?>"><?php echo htmlspecialchars($r['guest_type'] ?? 'new'); ?></span></td>
                    <td><span class="badge badge-status-<?php echo htmlspecialchars($r['status'] ?? 'active'); ?>"><?php echo htmlspecialchars($r['status'] ?? 'active'); ?></span></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
        <a href="search_v2.php" class="btn btn-primary">Search Again</a>
        <a href="../index.php" class="btn btn-link">&larr; Back to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/dinebook/js/app.js"></script>
</body>
</html>
