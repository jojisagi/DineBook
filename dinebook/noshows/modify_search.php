<?php
// modify_search.php — No-Shows: Pre-filled editable form
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['guest_name'])) { header('Location: modify.php'); exit; }
$val = $_POST['guest_name'];
try { $record = $noshows->findOne(['guest_name' => $val]); } catch (Exception $e) { $record = null; $dbError = $e->getMessage(); }
$channels = [];
if ($record && isset($record['reminder_channel'])) { $channels = $record['reminder_channel']; if ($channels instanceof MongoDB\Model\BSONArray) $channels = $channels->getArrayCopy(); }
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>DineBook — Modify No-Show</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="/dinebook/css/style.css"></head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link active" href="report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">No-Shows</a></li><li class="breadcrumb-item active">Modify</li></ol></nav>
        <h2 class="page-header">Modify No-Show Report</h2>
        <?php if (isset($dbError)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif ($record): ?>
        <div class="card"><div class="card-body form-section">
            <form method="post" action="modify_process.php">
                <input type="hidden" name="id" value="<?php echo (string)$record['_id']; ?>">
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="booking_id" class="form-label">Booking ID</label><input type="text" class="form-control" id="booking_id" name="booking_id" value="<?php echo htmlspecialchars($record['booking_id']??''); ?>" required></div>
                    <div class="col-md-6 mb-3"><label for="guest_name" class="form-label">Guest Name</label><input type="text" class="form-control" id="guest_name" name="guest_name" value="<?php echo htmlspecialchars($record['guest_name']??''); ?>" required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($record['email']??''); ?>" required></div>
                    <div class="col-md-6 mb-3"><label for="phone" class="form-label">Phone</label><input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($record['phone']??''); ?>" required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="reservation_date" class="form-label">Date</label><input type="date" class="form-control" id="reservation_date" name="reservation_date" value="<?php echo htmlspecialchars($record['reservation_date']??''); ?>" required></div>
                    <div class="col-md-6 mb-3"><label for="party_size" class="form-label">Party Size</label><select class="form-select" id="party_size" name="party_size" required><?php for($i=1;$i<=20;$i++) echo "<option value=\"$i\"".((($record['party_size']??0)==$i)?' selected':'').">$i</option>"; ?></select></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="zone" class="form-label">Zone</label><select class="form-select" id="zone" name="zone" required><?php foreach(['terrace','indoors','bar'] as $z) echo "<option value=\"$z\"".(($record['zone']??'')==$z?' selected':'').">".ucfirst($z)."</option>"; ?></select></div>
                    <div class="col-md-6 mb-3"><label for="reason_category" class="form-label">Reason</label><select class="form-select" id="reason_category" name="reason_category" required><?php foreach(['forgot','emergency','weather','no_reason','other'] as $rc) echo "<option value=\"$rc\"".(($record['reason_category']??'')==$rc?' selected':'').">".ucfirst(str_replace('_',' ',$rc))."</option>"; ?></select></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Was Reminded?</label><div>
                        <?php foreach(['yes','no','unknown'] as $wr): ?><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="was_reminded" value="<?php echo $wr; ?>" <?php echo ($record['was_reminded']??'unknown')==$wr?'checked':''; ?> required><label class="form-check-label"><?php echo ucfirst($wr); ?></label></div><?php endforeach; ?></div>
                    </div>
                    <div class="col-md-6 mb-3"><label class="form-label">Impact</label><div>
                        <?php foreach(['low','medium','high'] as $ia): ?><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="impact_assessment" value="<?php echo $ia; ?>" <?php echo ($record['impact_assessment']??'low')==$ia?'checked':''; ?> required><label class="form-check-label"><?php echo ucfirst($ia); ?></label></div><?php endforeach; ?></div>
                    </div>
                </div>
                <div class="mb-3"><label class="form-label">Reminder Channel</label><div>
                    <?php foreach(['email','SMS','whatsapp','call'] as $ch): ?><div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="reminder_channel[]" value="<?php echo $ch; ?>" <?php echo in_array($ch,(array)$channels)?'checked':''; ?>><label class="form-check-label"><?php echo ucfirst($ch); ?></label></div><?php endforeach; ?></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="follow_up_action" class="form-label">Follow-up</label><select class="form-select" id="follow_up_action" name="follow_up_action"><?php foreach(['none','email_sent','blacklisted','offered_voucher'] as $fa) echo "<option value=\"$fa\"".(($record['follow_up_action']??'none')==$fa?' selected':'').">".ucfirst(str_replace('_',' ',$fa))."</option>"; ?></select></div>
                    <div class="col-md-6 mb-3"><label for="reported_by" class="form-label">Reported By</label><input type="text" class="form-control" id="reported_by" name="reported_by" value="<?php echo htmlspecialchars($record['reported_by']??''); ?>" required></div>
                </div>
                <div class="mb-3"><label for="notes" class="form-label">Notes</label><textarea class="form-control" id="notes" name="notes" maxlength="300"><?php echo htmlspecialchars($record['notes']??''); ?></textarea></div>
                <button type="submit" class="btn btn-primary">Update Report</button>
                <a href="modify.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div></div>
        <?php else: ?><div class="alert alert-warning">No record found for "<?php echo htmlspecialchars($val); ?>".</div><?php endif; ?>
        <a href="../index.php" class="btn btn-link mt-3">&larr; Back to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script><script src="/dinebook/js/app.js"></script>
</body>
</html>
