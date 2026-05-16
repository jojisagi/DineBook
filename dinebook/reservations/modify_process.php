<?php
// modify_process.php — Reservations: Execute update
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: modify.php');
    exit;
}
try {
    $oid = new MongoDB\BSON\ObjectId($_POST['id']);
    $updateData = [
        'guest_name'           => $_POST['guest_name'],
        'email'                => $_POST['email'],
        'phone'                => $_POST['phone'],
        'reservation_date'     => $_POST['reservation_date'],
        'arrival_time'         => $_POST['arrival_time'],
        'party_size'           => (int)$_POST['party_size'],
        'zone'                 => $_POST['zone'],
        'dietary_restrictions' => isset($_POST['dietary_restrictions']) ? $_POST['dietary_restrictions'] : [],
        'occasion'             => $_POST['occasion'] ?? 'none',
        'special_requests'     => $_POST['special_requests'] ?? '',
        'guest_type'           => $_POST['guest_type'] ?? 'new',
        'confirmation_sent'    => (bool)isset($_POST['confirmation_sent'])
    ];
    $result = $reservations->updateOne(['_id' => $oid], ['$set' => $updateData]);
    $updated = $result->getMatchedCount() === 1;
} catch (Exception $e) {
    $updated = false;
    $dbError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineBook — Reservation Updated</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Reservations</a></li><li class="breadcrumb-item active">Updated</li></ol></nav>
        <h2 class="page-header">Modify Reservation</h2>
        <?php if (isset($dbError)): ?>
            <div class="alert alert-danger">Database error: <?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif ($updated): ?>
            <div class="alert alert-success success-msg">Reservation for <strong><?php echo htmlspecialchars($updateData['guest_name']); ?></strong> updated successfully.</div>
        <?php else: ?>
            <div class="alert alert-danger">The reservation could not be updated.</div>
        <?php endif; ?>
        <a href="modify.php" class="btn btn-primary">Modify Another</a>
        <a href="../index.php" class="btn btn-secondary">Return to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/dinebook/js/app.js"></script>
</body>
</html>
