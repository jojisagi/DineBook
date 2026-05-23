<?php
// create.php — Bookings: Create
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

// Pre-generate a Booking ID so staff can see/copy it before submitting
$bookingId = new MongoDB\BSON\ObjectId();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineBook — New Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Bookings</a></li><li class="breadcrumb-item active">New Booking</li></ol></nav>
        <h2 class="page-header">New Booking</h2>
        <div class="card"><div class="card-body form-section">
            <form method="post" action="create_process.php">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Booking ID</label>
                        <input type="text" class="form-control" value="<?php echo (string)$bookingId; ?>" readonly style="font-family:monospace; background:#f0f0f0;">
                        <input type="hidden" name="booking_id" value="<?php echo (string)$bookingId; ?>">
                        <div class="form-text">Auto-generated. Use this ID for no-show reports.</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="reservation_id" class="form-label">Reservation ID</label>
                        <input type="text" class="form-control" id="reservation_id" name="reservation_id" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="table_id" class="form-label">Table ID</label>
                        <input type="text" class="form-control" id="table_id" name="table_id" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="guest_id" class="form-label">Guest ID</label>
                        <input type="text" class="form-control" id="guest_id" name="guest_id" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="booking_date" class="form-label">Booking Date</label>
                        <input type="date" class="form-control" id="booking_date" name="booking_date" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="check_in_time" class="form-label">Check-in Time</label>
                        <input type="text" class="form-control" id="check_in_time" name="check_in_time" placeholder="e.g. 19:00">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="check_out_time" class="form-label">Check-out Time</label>
                        <input type="text" class="form-control" id="check_out_time" name="check_out_time" placeholder="e.g. 21:00">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="actual_party_size" class="form-label">Actual Party Size</label>
                        <select class="form-select" id="actual_party_size" name="actual_party_size" required>
                            <option value="">-- Select --</option>
                            <?php for ($i = 1; $i <= 20; $i++) echo "<option value=\"$i\">$i</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="assigned_by" class="form-label">Assigned By (Hostess)</label>
                        <input type="text" class="form-control" id="assigned_by" name="assigned_by" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Booking Status</label>
                        <div>
                            <?php foreach (['pending','confirmed','seated','completed','no-show','cancelled'] as $s): ?>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="booking_status" value="<?php echo $s; ?>" <?php echo $s==='pending'?'checked':''; ?> required><label class="form-check-label"><?php echo ucfirst($s); ?></label></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payment Status</label>
                        <div>
                            <?php foreach (['unpaid','paid','comp'] as $p): ?>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="payment_status" value="<?php echo $p; ?>" <?php echo $p==='unpaid'?'checked':''; ?> required><label class="form-check-label"><?php echo ucfirst($p); ?></label></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Special Setup</label>
                    <div>
                        <?php foreach (['candles','flowers','cake','balloons','banner'] as $ss): ?>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="special_setup[]" value="<?php echo $ss; ?>"><label class="form-check-label"><?php echo ucfirst($ss); ?></label></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="hostess_notes" class="form-label">Hostess Notes</label>
                    <textarea class="form-control" id="hostess_notes" name="hostess_notes" maxlength="300"></textarea>
                </div>
                <div id="extra-details" style="display:none;">
                    <div class="alert alert-info">Extra details section — use this for any additional notes about the booking setup.</div>
                </div>
                <button type="button" id="toggle-details" class="btn btn-link mb-2">Show details</button><br>
                <button type="submit" class="btn btn-primary">Create Booking</button>
                <button type="reset" class="btn btn-secondary btn-reset">Reset</button>
            </form>
        </div></div>
        <a href="report.php" class="btn btn-link mt-3">&larr; Back to Bookings</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/dinebook/js/app.js"></script>
</body>
</html>
