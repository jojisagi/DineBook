<?php
// create.php — No-Shows: Create
require_once __DIR__ . '/../auth/guard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>DineBook — New No-Show Report</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="/dinebook/css/style.css"></head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link active" href="report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">No-Shows</a></li><li class="breadcrumb-item active">New Report</li></ol></nav>
        <h2 class="page-header">New No-Show Report</h2>
        <div class="card"><div class="card-body form-section">
            <form method="post" action="create_process.php">
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="booking_id" class="form-label">Booking ID</label><input type="text" class="form-control" id="booking_id" name="booking_id" required></div>
                    <div class="col-md-6 mb-3"><label for="guest_name" class="form-label">Guest Name</label><input type="text" class="form-control" id="guest_name" name="guest_name" required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" required></div>
                    <div class="col-md-6 mb-3"><label for="phone" class="form-label">Phone</label><input type="tel" class="form-control" id="phone" name="phone" required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="reservation_date" class="form-label">Reservation Date</label><input type="date" class="form-control" id="reservation_date" name="reservation_date" required></div>
                    <div class="col-md-6 mb-3"><label for="party_size" class="form-label">Party Size</label><select class="form-select" id="party_size" name="party_size" required><option value="">-- Select --</option><?php for($i=1;$i<=20;$i++) echo "<option value=\"$i\">$i</option>"; ?></select></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="zone" class="form-label">Zone</label><select class="form-select" id="zone" name="zone" required><option value="">-- Select --</option><option value="terrace">Terrace</option><option value="indoors">Indoors</option><option value="bar">Bar</option></select></div>
                    <div class="col-md-6 mb-3"><label for="reason_category" class="form-label">Reason Category</label><select class="form-select" id="reason_category" name="reason_category" required><option value="">-- Select --</option><option value="forgot">Forgot</option><option value="emergency">Emergency</option><option value="weather">Weather</option><option value="no_reason">No Reason</option><option value="other">Other</option></select></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Was Reminded?</label><div>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="was_reminded" value="yes" required><label class="form-check-label">Yes</label></div>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="was_reminded" value="no"><label class="form-check-label">No</label></div>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="was_reminded" value="unknown"><label class="form-check-label">Unknown</label></div></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Impact Assessment</label><div>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="impact_assessment" value="low" required><label class="form-check-label">Low</label></div>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="impact_assessment" value="medium"><label class="form-check-label">Medium</label></div>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="impact_assessment" value="high"><label class="form-check-label">High</label></div></div>
                    </div>
                </div>
                <div class="mb-3"><label class="form-label">Reminder Channel</label><div>
                    <?php foreach (['email','SMS','whatsapp','call'] as $ch): ?>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="reminder_channel[]" value="<?php echo $ch; ?>"><label class="form-check-label"><?php echo ucfirst($ch); ?></label></div>
                    <?php endforeach; ?></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="follow_up_action" class="form-label">Follow-up Action</label><select class="form-select" id="follow_up_action" name="follow_up_action"><option value="none">None</option><option value="email_sent">Email Sent</option><option value="blacklisted">Blacklisted</option><option value="offered_voucher">Offered Voucher</option></select></div>
                    <div class="col-md-6 mb-3"><label for="reported_by" class="form-label">Reported By</label><input type="text" class="form-control" id="reported_by" name="reported_by" required></div>
                </div>
                <div class="mb-3"><label for="notes" class="form-label">Notes</label><textarea class="form-control" id="notes" name="notes" maxlength="300"></textarea></div>
                <button type="submit" class="btn btn-primary">Submit No-Show Report</button>
                <button type="reset" class="btn btn-secondary btn-reset">Reset</button>
            </form>
        </div></div>
        <a href="report.php" class="btn btn-link mt-3">&larr; Back to No-Shows</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script><script src="/dinebook/js/app.js"></script>
</body>
</html>
