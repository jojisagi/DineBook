<?php
// create_process.php — Tables: Insert
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create.html');
    exit;
}

$required = ['table_number', 'capacity', 'zone', 'shape', 'status', 'floor'];
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
    <title>DineBook — Table Created</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Tables</a></li><li class="breadcrumb-item active">Create Result</li></ol></nav>
        <h2 class="page-header">Create Table</h2>

<?php if (!empty($errors)): ?>
    <?php foreach ($errors as $err): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
    <?php endforeach; ?>
    <a href="create.html" class="btn btn-secondary">&larr; Go Back</a>
<?php else:
    try {
        $document = [
            'table_number'     => (int)$_POST['table_number'],
            'capacity'         => (int)$_POST['capacity'],
            'zone'             => $_POST['zone'],
            'shape'            => $_POST['shape'],
            'has_power_outlet' => (bool)isset($_POST['has_power_outlet']),
            'is_accessible'    => (bool)isset($_POST['is_accessible']),
            'status'           => $_POST['status'],
            'notes'            => $_POST['notes'] ?? '',
            'floor'            => (int)$_POST['floor'],
            'label'            => $_POST['label'] ?? '',
            'created_at'       => new MongoDB\BSON\UTCDateTime()
        ];
        $result = $tables->insertOne($document);
?>
        <div class="alert alert-success success-msg">Table <strong>#<?php echo htmlspecialchars($document['table_number']); ?></strong> created successfully. (ID: <?php echo $result->getInsertedId(); ?>)</div>
        <a href="create.html" class="btn btn-primary">New Table</a>
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
