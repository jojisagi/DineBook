<?php
// modify_process.php — Bookings: Execute update
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) { header('Location: modify.php'); exit; }
try {
    $oid = new MongoDB\BSON\ObjectId($_POST['id']);
    $updateData = [
        'reservation_id'   => $_POST['reservation_id'],
        'table_id'         => $_POST['table_id'],
        'guest_id'         => $_POST['guest_id'],
        'booking_date'     => $_POST['booking_date'],
        'check_in_time'    => $_POST['check_in_time'] ?? '',
        'check_out_time'   => $_POST['check_out_time'] ?? '',
        'actual_party_size'=> (int)$_POST['actual_party_size'],
        'assigned_by'      => $_POST['assigned_by'],
        'booking_status'   => $_POST['booking_status'] ?? 'pending',
        'payment_status'   => $_POST['payment_status'] ?? 'unpaid',
        'special_setup'    => isset($_POST['special_setup']) ? $_POST['special_setup'] : [],
        'hostess_notes'    => $_POST['hostess_notes'] ?? ''
    ];
    $result = $bookings->updateOne(['_id' => $oid], ['$set' => $updateData]);
    $updated = $result->getMatchedCount() === 1;
} catch (Exception $e) { $updated = false; $dbError = $e->getMessage(); }
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>DineBook — Booking Updated</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="/dinebook/css/style.css"></head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Bookings</a></li><li class="breadcrumb-item active">Updated</li></ol></nav>
        <h2 class="page-header">Modify Booking</h2>
        <?php if (isset($dbError)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif ($updated): ?><div class="alert alert-success success-msg">Booking updated successfully.</div>
        <?php else: ?><div class="alert alert-danger">Booking could not be updated.</div><?php endif; ?>
        <a href="modify.php" class="btn btn-primary">Modify Another</a>
        <a href="../index.php" class="btn btn-secondary">Return to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script><script src="/dinebook/js/app.js"></script>
</body>
</html>
