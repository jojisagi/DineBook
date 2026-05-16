<?php
// search_process.php — No-Shows: Search result
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['guest_name'])) { header('Location: search.php'); exit; }
$val = $_POST['guest_name'];
try { $record = $noshows->findOne(['guest_name' => $val]); } catch (Exception $e) { $record = null; $dbError = $e->getMessage(); }
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>DineBook — Search Results</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="/dinebook/css/style.css"></head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link active" href="report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">No-Shows</a></li><li class="breadcrumb-item active">Results</li></ol></nav>
        <h2 class="page-header">Search Results</h2>
        <?php if (isset($dbError)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif ($record): ?>
        <div class="table-responsive"><table class="table table-striped table-hover table-bordered">
            <tr><th>Field</th><th>Value</th></tr>
            <tr><td>Guest Name</td><td><?php echo htmlspecialchars($record['guest_name']??''); ?></td></tr>
            <tr><td>Email</td><td><?php echo htmlspecialchars($record['email']??''); ?></td></tr>
            <tr><td>Phone</td><td><?php echo htmlspecialchars($record['phone']??''); ?></td></tr>
            <tr><td>Date</td><td><?php echo htmlspecialchars($record['reservation_date']??''); ?></td></tr>
            <tr><td>Party Size</td><td><?php echo htmlspecialchars($record['party_size']??''); ?></td></tr>
            <tr><td>Zone</td><td><?php echo htmlspecialchars($record['zone']??''); ?></td></tr>
            <tr><td>Reason</td><td><?php echo htmlspecialchars($record['reason_category']??''); ?></td></tr>
            <tr><td>Reminded</td><td><?php echo htmlspecialchars($record['was_reminded']??'unknown'); ?></td></tr>
            <tr><td>Reminder Channel</td><td><?php $ch=$record['reminder_channel']??[]; if($ch instanceof MongoDB\Model\BSONArray) $ch=$ch->getArrayCopy(); echo is_array($ch)&&count($ch)>0?htmlspecialchars(implode(', ',$ch)):'None'; ?></td></tr>
            <tr><td>Impact</td><td><?php echo htmlspecialchars($record['impact_assessment']??'low'); ?></td></tr>
            <tr><td>Follow-up</td><td><?php echo htmlspecialchars($record['follow_up_action']??'none'); ?></td></tr>
            <tr><td>Notes</td><td><?php echo htmlspecialchars($record['notes']??''); ?></td></tr>
            <tr><td>Reported By</td><td><?php echo htmlspecialchars($record['reported_by']??''); ?></td></tr>
        </table></div>
        <?php else: ?><div class="alert alert-warning">No no-show found for "<?php echo htmlspecialchars($val); ?>".</div><?php endif; ?>
        <a href="search.php" class="btn btn-primary">Search Again</a>
        <a href="../index.php" class="btn btn-link">&larr; Back to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script><script src="/dinebook/js/app.js"></script>
</body>
</html>
