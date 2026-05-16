<?php
// create_process.php — Reservations: Insert
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create.html');
    exit;
}

$required = ['guest_name', 'email', 'phone', 'reservation_date', 'arrival_time', 'party_size', 'zone'];
$errors = [];
foreach ($required as $f) {
    if (empty($_POST[$f])) $errors[] = ucfirst(str_replace('_', ' ', $f)) . ' is required.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineBook — Reservation Created</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Reservations</a></li><li class="breadcrumb-item active">Create Result</li></ol></nav>
        <h2 class="page-header">Create Reservation</h2>

<?php if (!empty($errors)): ?>
    <?php foreach ($errors as $err): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
    <?php endforeach; ?>
    <a href="create.html" class="btn btn-secondary">&larr; Go Back</a>
<?php else:
    try {
        $document = [
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
            'confirmation_sent'    => (bool)isset($_POST['confirmation_sent']),
            'status'               => 'active',
            'created_at'           => new MongoDB\BSON\UTCDateTime()
        ];
        $result = $reservations->insertOne($document);
?>
        <div class="alert alert-success success-msg">Reservation for <strong><?php echo htmlspecialchars($document['guest_name']); ?></strong> created successfully. (ID: <?php echo $result->getInsertedId(); ?>)</div>
        <a href="create.html" class="btn btn-primary">New Reservation</a>
        <a href="../index.php" class="btn btn-secondary">Return to Menu</a>
<?php } catch (Exception $e) { ?>
        <div class="alert alert-danger">Database error: <?php echo htmlspecialchars($e->getMessage()); ?></div>
        <a href="create.html" class="btn btn-secondary">&larr; Go Back</a>
<?php } endif; ?>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/dinebook/js/app.js"></script>
</body>
</html>
