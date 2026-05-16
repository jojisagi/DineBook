<?php
// modify_search.php — Tables: Show editable form pre-filled
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['table_number'])) {
    header('Location: modify.php');
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
    <title>DineBook — Modify Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Tables</a></li><li class="breadcrumb-item active">Modify</li></ol></nav>
        <h2 class="page-header">Modify Table</h2>
        <?php if (isset($dbError)): ?>
            <div class="alert alert-danger">Database error: <?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif ($record): ?>
        <div class="card"><div class="card-body form-section">
            <form method="post" action="modify_process.php">
                <input type="hidden" name="id" value="<?php echo (string)$record['_id']; ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="table_number" class="form-label">Table Number</label>
                        <input type="text" class="form-control" id="table_number" name="table_number" value="<?php echo htmlspecialchars($record['table_number']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="label" class="form-label">Label</label>
                        <input type="text" class="form-control" id="label" name="label" value="<?php echo htmlspecialchars($record['label'] ?? ''); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="capacity" class="form-label">Capacity</label>
                        <select class="form-select" id="capacity" name="capacity" required>
                            <option value="">-- Select --</option>
                            <?php for ($i = 1; $i <= 20; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($record['capacity'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="floor" class="form-label">Floor</label>
                        <select class="form-select" id="floor" name="floor" required>
                            <option value="">-- Select --</option>
                            <?php for ($i = 1; $i <= 3; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($record['floor'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Zone</label>
                        <div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="zone" value="terrace" <?php echo ($record['zone'] == 'terrace') ? 'checked' : ''; ?> required><label class="form-check-label">Terrace</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="zone" value="indoors" <?php echo ($record['zone'] == 'indoors') ? 'checked' : ''; ?>><label class="form-check-label">Indoors</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="zone" value="bar" <?php echo ($record['zone'] == 'bar') ? 'checked' : ''; ?>><label class="form-check-label">Bar</label></div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="shape" class="form-label">Shape</label>
                        <select class="form-select" id="shape" name="shape" required>
                            <option value="">-- Select --</option>
                            <?php foreach (['round'=>'Round','rectangular'=>'Rectangular','booth'=>'Booth'] as $v => $l): ?>
                                <option value="<?php echo $v; ?>" <?php echo ($record['shape'] == $v) ? 'selected' : ''; ?>><?php echo $l; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">-- Select --</option>
                            <?php foreach (['available'=>'Available','occupied'=>'Occupied','reserved'=>'Reserved','maintenance'=>'Maintenance'] as $v => $l): ?>
                                <option value="<?php echo $v; ?>" <?php echo ($record['status'] == $v) ? 'selected' : ''; ?>><?php echo $l; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="has_power_outlet" value="1" id="has_power_outlet" <?php echo (!empty($record['has_power_outlet'])) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="has_power_outlet">Has Power Outlet</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_accessible" value="1" id="is_accessible" <?php echo (!empty($record['is_accessible'])) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_accessible">Is Accessible</label>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" maxlength="300"><?php echo htmlspecialchars($record['notes'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Table</button>
                <a href="modify.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div></div>
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
