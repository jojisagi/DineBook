<?php
// delete_search.php — Tables: Show record + confirm delete
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['table_number'])) {
    header('Location: delete.php');
    exit;
}
$table_number = $_POST['table_number'];
try {
    $record = $tables->findOne(['table_number' => (int)$table_number]);
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
    <title>DineBook — Confirm Delete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Tables</a></li><li class="breadcrumb-item active">Confirm Delete</li></ol></nav>
        <h2 class="page-header">Confirm Deletion</h2>
        <?php if (isset($dbError)): ?>
            <div class="alert alert-danger">Database error: <?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif ($record): ?>
            <p>The following table was found. Confirm deletion:</p>
            <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <tr><th>Field</th><th>Value</th></tr>
                <tr><td>Table Number</td><td><?php echo htmlspecialchars($record['table_number']); ?></td></tr>
                <tr><td>Label</td><td><?php echo htmlspecialchars($record['label'] ?? ''); ?></td></tr>
                <tr><td>Capacity</td><td><?php echo htmlspecialchars($record['capacity']); ?></td></tr>
                <tr><td>Floor</td><td><?php echo htmlspecialchars($record['floor']); ?></td></tr>
                <tr><td>Zone</td><td><?php echo htmlspecialchars($record['zone']); ?></td></tr>
                <tr><td>Shape</td><td><?php echo htmlspecialchars($record['shape']); ?></td></tr>
                <tr><td>Status</td><td><span class="badge badge-status-<?php echo htmlspecialchars($record['status']); ?>"><?php echo htmlspecialchars($record['status']); ?></span></td></tr>
                <tr><td>Has Power Outlet</td><td><?php echo (!empty($record['has_power_outlet'])) ? 'Yes' : 'No'; ?></td></tr>
                <tr><td>Is Accessible</td><td><?php echo (!empty($record['is_accessible'])) ? 'Yes' : 'No'; ?></td></tr>
                <tr><td>Notes</td><td><?php echo htmlspecialchars($record['notes'] ?? ''); ?></td></tr>
            </table>
            </div>
            <form method="post" action="delete_process.php" id="delete-confirm-form">
                <input type="hidden" name="id" value="<?php echo (string)$record['_id']; ?>">
                <button type="submit" class="btn btn-danger">Confirm Delete</button>
                <a href="delete.php" class="btn btn-secondary">Cancel</a>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">No table found for number "<?php echo htmlspecialchars($table_number); ?>".</div>
        <?php endif; ?>
        <a href="../index.php" class="btn btn-link mt-3">&larr; Back to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/dinebook/js/app.js"></script>
</body>
</html>
