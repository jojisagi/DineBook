<?php
// delete_search.php — Guests: Show record + confirm delete
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['full_name'])) {
    header('Location: delete.php');
    exit;
}
$full_name = $_POST['full_name'];
try {
    $record = $guests->findOne(['full_name' => $full_name]);
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
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Guests</a></li><li class="breadcrumb-item active">Confirm Delete</li></ol></nav>
        <h2 class="page-header">Confirm Deletion</h2>
        <?php if (isset($dbError)): ?>
            <div class="alert alert-danger">Database error: <?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif ($record): ?>
            <p>The following guest was found. Confirm deletion:</p>
            <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <tr><th>Field</th><th>Value</th></tr>
                <tr><td>Full Name</td><td><?php echo htmlspecialchars($record['full_name']); ?></td></tr>
                <tr><td>Email</td><td><?php echo htmlspecialchars($record['email']); ?></td></tr>
                <tr><td>Phone</td><td><?php echo htmlspecialchars($record['phone']); ?></td></tr>
                <tr><td>Birth Date</td><td><?php echo htmlspecialchars($record['birth_date'] ?? ''); ?></td></tr>
                <tr><td>Preferred Zone</td><td><?php echo htmlspecialchars($record['preferred_zone'] ?? ''); ?></td></tr>
                <tr><td>Dietary Restrictions</td><td><?php
                    $d = $record['dietary_restrictions'] ?? [];
                    if ($d instanceof MongoDB\Model\BSONArray) $d = $d->getArrayCopy();
                    echo is_array($d) && count($d) > 0 ? htmlspecialchars(implode(', ', $d)) : 'None';
                ?></td></tr>
                <tr><td>Loyalty Tier</td><td><?php echo htmlspecialchars($record['loyalty_tier'] ?? 'standard'); ?></td></tr>
                <tr><td>Contact Preference</td><td><?php echo htmlspecialchars($record['contact_preference'] ?? ''); ?></td></tr>
                <tr><td>Marketing Opt-In</td><td><?php echo (!empty($record['marketing_opt_in'])) ? 'Yes' : 'No'; ?></td></tr>
                <tr><td>Notes</td><td><?php echo htmlspecialchars($record['notes'] ?? ''); ?></td></tr>
                <tr><td>Visit Count</td><td><?php echo htmlspecialchars($record['visit_count'] ?? 0); ?></td></tr>
                <tr><td>Last Visit</td><td><?php echo htmlspecialchars($record['last_visit'] ?? ''); ?></td></tr>
            </table>
            </div>
            <form method="post" action="delete_process.php" id="delete-confirm-form">
                <input type="hidden" name="id" value="<?php echo (string)$record['_id']; ?>">
                <button type="submit" class="btn btn-danger">Confirm Delete</button>
                <a href="delete.php" class="btn btn-secondary">Cancel</a>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">No guest found for "<?php echo htmlspecialchars($full_name); ?>".</div>
        <?php endif; ?>
        <a href="../index.php" class="btn btn-link mt-3">&larr; Back to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/dinebook/js/app.js"></script>
</body>
</html>
