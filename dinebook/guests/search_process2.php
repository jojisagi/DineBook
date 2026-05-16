<?php
// search_process2.php — Guests: Search by selectable field
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
    case 'full_name':          $filter = ['full_name' => $value]; break;
    case 'email':              $filter = ['email' => $value]; break;
    case 'phone':              $filter = ['phone' => $value]; break;
    case 'preferred_zone':     $filter = ['preferred_zone' => $value]; break;
    case 'loyalty_tier':       $filter = ['loyalty_tier' => $value]; break;
    case 'contact_preference': $filter = ['contact_preference' => $value]; break;
    default:                   $filter = ['full_name' => $value]; break;
}
try {
    $cursor = $guests->find($filter);
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
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Guests</a></li><li class="breadcrumb-item active">Search Results</li></ol></nav>
        <h2 class="page-header">Search Results</h2>
        <p>Searching by <strong><?php echo htmlspecialchars($field); ?></strong> = "<?php echo htmlspecialchars($value); ?>"</p>
        <?php if (isset($dbError)): ?>
            <div class="alert alert-danger">Database error: <?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif (count($records) === 0): ?>
            <div class="alert alert-warning">No guests found.</div>
        <?php else: ?>
            <p><?php echo count($records); ?> guest(s) found.</p>
            <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead><tr><th>Full Name</th><th>Email</th><th>Phone</th><th>Preferred Zone</th><th>Loyalty Tier</th><th>Contact Preference</th><th>Visit Count</th><th>Marketing</th></tr></thead>
                <tbody>
                <?php foreach ($records as $r): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['email']); ?></td>
                    <td><?php echo htmlspecialchars($r['phone']); ?></td>
                    <td><?php echo htmlspecialchars($r['preferred_zone'] ?? ''); ?></td>
                    <td><span class="badge badge-tier-<?php echo htmlspecialchars($r['loyalty_tier'] ?? 'standard'); ?>"><?php echo htmlspecialchars($r['loyalty_tier'] ?? 'standard'); ?></span></td>
                    <td><?php echo htmlspecialchars($r['contact_preference'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($r['visit_count'] ?? 0); ?></td>
                    <td><?php echo (!empty($r['marketing_opt_in'])) ? 'Yes' : 'No'; ?></td>
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
