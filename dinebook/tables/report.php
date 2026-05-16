<?php
// report.php — Tables: List All
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

try {
    $cursor = $tables->find([], ['sort' => ['created_at' => -1]]);
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
    <title>DineBook — All Tables</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item active">All Tables</li></ol></nav>
        <h2 class="page-header">All Tables Report</h2>

        <div class="mb-3">
            <input type="text" id="table-filter" class="form-control" placeholder="Filter results...">
        </div>

        <?php if (isset($dbError)): ?>
            <div class="alert alert-danger">Database error: <?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif (count($records) === 0): ?>
            <div class="alert alert-warning">No tables registered.</div>
        <?php else: ?>
            <p>Total: <strong><?php echo count($records); ?></strong> tables</p>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr><th>Table #</th><th>Label</th><th>Capacity</th><th>Floor</th><th>Zone</th><th>Shape</th><th>Status</th><th>Power</th><th>Accessible</th><th>Notes</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['table_number']); ?></td>
                            <td><?php echo htmlspecialchars($r['label'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($r['capacity']); ?></td>
                            <td><?php echo htmlspecialchars($r['floor']); ?></td>
                            <td><?php echo htmlspecialchars($r['zone']); ?></td>
                            <td><?php echo htmlspecialchars($r['shape']); ?></td>
                            <td><span class="badge badge-status-<?php echo htmlspecialchars($r['status']); ?>"><?php echo htmlspecialchars($r['status']); ?></span></td>
                            <td><?php echo (!empty($r['has_power_outlet'])) ? 'Yes' : 'No'; ?></td>
                            <td><?php echo (!empty($r['is_accessible'])) ? 'Yes' : 'No'; ?></td>
                            <td><?php echo htmlspecialchars($r['notes'] ?? ''); ?></td>
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
