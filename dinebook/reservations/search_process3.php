<?php
// search_process3.php — Reservations: Search by GET parameter
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

if (!isset($_GET['guest_name']) || empty($_GET['guest_name'])) {
    header('Location: search_v3.php');
    exit;
}
$guest_name = $_GET['guest_name'];
try {
    $record = $reservations->findOne(['guest_name' => $guest_name]);
} catch (Exception $e) {
    $record = null;
    $dbError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineBook — Search Results (GET)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Reservations</a></li><li class="breadcrumb-item active">Search Results (GET)</li></ol></nav>
        <h2 class="page-header">Search Results (GET)</h2>
        <?php if (isset($dbError)): ?>
            <div class="alert alert-danger">Database error: <?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif ($record): ?>
            <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <tr><th>Field</th><th>Value</th></tr>
                <tr><td>Guest Name</td><td><?php echo htmlspecialchars($record['guest_name']); ?></td></tr>
                <tr><td>Email</td><td><?php echo htmlspecialchars($record['email']); ?></td></tr>
                <tr><td>Phone</td><td><?php echo htmlspecialchars($record['phone']); ?></td></tr>
                <tr><td>Reservation Date</td><td><?php echo htmlspecialchars($record['reservation_date']); ?></td></tr>
                <tr><td>Arrival Time</td><td><?php echo htmlspecialchars($record['arrival_time']); ?></td></tr>
                <tr><td>Party Size</td><td><?php echo htmlspecialchars($record['party_size']); ?></td></tr>
                <tr><td>Zone</td><td><?php echo htmlspecialchars($record['zone']); ?></td></tr>
                <tr><td>Dietary Restrictions</td><td><?php
                    $d = $record['dietary_restrictions'] ?? [];
                    if ($d instanceof MongoDB\Model\BSONArray) $d = $d->getArrayCopy();
                    echo is_array($d) && count($d) > 0 ? htmlspecialchars(implode(', ', $d)) : 'None';
                ?></td></tr>
                <tr><td>Occasion</td><td><?php echo htmlspecialchars($record['occasion'] ?? 'none'); ?></td></tr>
                <tr><td>Special Requests</td><td><?php echo htmlspecialchars($record['special_requests'] ?? ''); ?></td></tr>
                <tr><td>Guest Type</td><td><?php echo htmlspecialchars($record['guest_type'] ?? 'new'); ?></td></tr>
                <tr><td>Confirmation Sent</td><td><?php echo (!empty($record['confirmation_sent'])) ? 'Yes' : 'No'; ?></td></tr>
            </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">No reservation found for "<?php echo htmlspecialchars($guest_name); ?>".</div>
        <?php endif; ?>
        <a href="search_v3.php" class="btn btn-primary">Back to Links</a>
        <a href="../index.php" class="btn btn-link">&larr; Back to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/dinebook/js/app.js"></script>
</body>
</html>
