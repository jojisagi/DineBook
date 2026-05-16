<?php
// modify_search.php — Reservations: Show editable form pre-filled
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['guest_name'])) {
    header('Location: modify.php');
    exit;
}
$guest_name = $_POST['guest_name'];
try {
    $record = $reservations->findOne(['guest_name' => $guest_name]);
} catch (Exception $e) {
    $record = null;
    $dbError = $e->getMessage();
}

$dietary = [];
if ($record && isset($record['dietary_restrictions'])) {
    $dietary = $record['dietary_restrictions'];
    if ($dietary instanceof MongoDB\Model\BSONArray) $dietary = $dietary->getArrayCopy();
}
$dietaryOptions = ['Vegetarian','Vegan','Gluten-free','Nut allergy','Lactose intolerant','Shellfish allergy'];
$timeSlots = ['11:00'=>'11:00 AM','11:30'=>'11:30 AM','12:00'=>'12:00 PM','12:30'=>'12:30 PM','13:00'=>'1:00 PM','13:30'=>'1:30 PM','14:00'=>'2:00 PM','14:30'=>'2:30 PM','15:00'=>'3:00 PM','17:00'=>'5:00 PM','17:30'=>'5:30 PM','18:00'=>'6:00 PM','18:30'=>'6:30 PM','19:00'=>'7:00 PM','19:30'=>'7:30 PM','20:00'=>'8:00 PM','20:30'=>'8:30 PM','21:00'=>'9:00 PM','21:30'=>'9:30 PM','22:00'=>'10:00 PM'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineBook — Modify Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Reservations</a></li><li class="breadcrumb-item active">Modify</li></ol></nav>
        <h2 class="page-header">Modify Reservation</h2>
        <?php if (isset($dbError)): ?>
            <div class="alert alert-danger">Database error: <?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif ($record): ?>
        <div class="card"><div class="card-body form-section">
            <form method="post" action="modify_process.php">
                <input type="hidden" name="id" value="<?php echo (string)$record['_id']; ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="guest_name" class="form-label">Guest Full Name</label>
                        <input type="text" class="form-control" id="guest_name" name="guest_name" value="<?php echo htmlspecialchars($record['guest_name']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($record['email']); ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($record['phone']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="reservation_date" class="form-label">Reservation Date</label>
                        <input type="date" class="form-control" id="reservation_date" name="reservation_date" value="<?php echo htmlspecialchars($record['reservation_date']); ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="arrival_time" class="form-label">Arrival Time</label>
                        <select class="form-select" id="arrival_time" name="arrival_time" required>
                            <option value="">-- Select --</option>
                            <?php foreach ($timeSlots as $v => $l): ?>
                                <option value="<?php echo $v; ?>" <?php echo ($record['arrival_time'] == $v) ? 'selected' : ''; ?>><?php echo $l; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="party_size" class="form-label">Party Size</label>
                        <select class="form-select" id="party_size" name="party_size" required>
                            <option value="">-- Select --</option>
                            <?php for ($i = 1; $i <= 20; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($record['party_size'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                        <div id="party-warning" class="warning-msg d-none mt-2">Large party — a private area may be assigned.</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Seating Zone</label>
                        <div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="zone" value="terrace" <?php echo ($record['zone'] == 'terrace') ? 'checked' : ''; ?> required><label class="form-check-label">Terrace</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="zone" value="indoors" <?php echo ($record['zone'] == 'indoors') ? 'checked' : ''; ?>><label class="form-check-label">Indoors</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="zone" value="bar" <?php echo ($record['zone'] == 'bar') ? 'checked' : ''; ?>><label class="form-check-label">Bar</label></div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guest Status</label>
                        <div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="guest_type" value="new" <?php echo (($record['guest_type'] ?? 'new') == 'new') ? 'checked' : ''; ?> required><label class="form-check-label">New</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="guest_type" value="returning" <?php echo (($record['guest_type'] ?? '') == 'returning') ? 'checked' : ''; ?>><label class="form-check-label">Returning</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="guest_type" value="VIP" <?php echo (($record['guest_type'] ?? '') == 'VIP') ? 'checked' : ''; ?>><label class="form-check-label">VIP</label></div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Allergies / Dietary Restrictions</label>
                    <div>
                        <?php foreach ($dietaryOptions as $opt): ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="dietary_restrictions[]" value="<?php echo $opt; ?>" <?php echo in_array($opt, (array)$dietary) ? 'checked' : ''; ?>>
                                <label class="form-check-label"><?php echo $opt; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="occasion" class="form-label">Special Occasion</label>
                        <select class="form-select" id="occasion" name="occasion">
                            <?php foreach (['none'=>'None','birthday'=>'Birthday','anniversary'=>'Anniversary','business'=>'Business','other'=>'Other'] as $v => $l): ?>
                                <option value="<?php echo $v; ?>" <?php echo (($record['occasion'] ?? 'none') == $v) ? 'selected' : ''; ?>><?php echo $l; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="confirmation_sent" value="1" id="confirmation_sent" <?php echo (!empty($record['confirmation_sent'])) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="confirmation_sent">Confirmation Sent</label>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="special_requests" class="form-label">Special Requests</label>
                    <textarea class="form-control" id="special_requests" name="special_requests" maxlength="300"><?php echo htmlspecialchars($record['special_requests'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Reservation</button>
                <a href="modify.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div></div>
        <?php else: ?>
            <div class="alert alert-warning">No reservation found for "<?php echo htmlspecialchars($guest_name); ?>".</div>
        <?php endif; ?>
        <a href="../index.php" class="btn btn-link mt-3">&larr; Back to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/dinebook/js/app.js"></script>
</body>
</html>
