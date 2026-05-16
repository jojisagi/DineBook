<?php
// modify_search.php — Bookings: Pre-filled editable form
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['assigned_by'])) { header('Location: modify.php'); exit; }
$val = $_POST['assigned_by'];
try { $record = $bookings->findOne(['assigned_by' => $val]); } catch (Exception $e) { $record = null; $dbError = $e->getMessage(); }
$setup = [];
if ($record && isset($record['special_setup'])) { $setup = $record['special_setup']; if ($setup instanceof MongoDB\Model\BSONArray) $setup = $setup->getArrayCopy(); }
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>DineBook — Modify Booking</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="/dinebook/css/style.css"></head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link" href="../guests/report.php">Guests</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Bookings</a></li><li class="breadcrumb-item active">Modify</li></ol></nav>
        <h2 class="page-header">Modify Booking</h2>
        <?php if (isset($dbError)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif ($record): ?>
        <div class="card"><div class="card-body form-section">
            <form method="post" action="modify_process.php">
                <input type="hidden" name="id" value="<?php echo (string)$record['_id']; ?>">
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="reservation_id" class="form-label">Reservation ID</label><input type="text" class="form-control" id="reservation_id" name="reservation_id" value="<?php echo htmlspecialchars($record['reservation_id']??''); ?>" required></div>
                    <div class="col-md-6 mb-3"><label for="table_id" class="form-label">Table ID</label><input type="text" class="form-control" id="table_id" name="table_id" value="<?php echo htmlspecialchars($record['table_id']??''); ?>" required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="guest_id" class="form-label">Guest ID</label><input type="text" class="form-control" id="guest_id" name="guest_id" value="<?php echo htmlspecialchars($record['guest_id']??''); ?>" required></div>
                    <div class="col-md-6 mb-3"><label for="booking_date" class="form-label">Booking Date</label><input type="date" class="form-control" id="booking_date" name="booking_date" value="<?php echo htmlspecialchars($record['booking_date']??''); ?>" required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="check_in_time" class="form-label">Check-in Time</label><input type="text" class="form-control" id="check_in_time" name="check_in_time" value="<?php echo htmlspecialchars($record['check_in_time']??''); ?>"></div>
                    <div class="col-md-6 mb-3"><label for="check_out_time" class="form-label">Check-out Time</label><input type="text" class="form-control" id="check_out_time" name="check_out_time" value="<?php echo htmlspecialchars($record['check_out_time']??''); ?>"></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="actual_party_size" class="form-label">Actual Party Size</label><select class="form-select" id="actual_party_size" name="actual_party_size" required><?php for($i=1;$i<=20;$i++) echo "<option value=\"$i\"".($record['actual_party_size']==$i?' selected':'').">$i</option>"; ?></select></div>
                    <div class="col-md-6 mb-3"><label for="assigned_by" class="form-label">Assigned By</label><input type="text" class="form-control" id="assigned_by" name="assigned_by" value="<?php echo htmlspecialchars($record['assigned_by']??''); ?>" required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Booking Status</label><div>
                        <?php foreach (['pending','confirmed','seated','completed','no-show','cancelled'] as $s): ?>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="booking_status" value="<?php echo $s; ?>" <?php echo ($record['booking_status']??'pending')==$s?'checked':''; ?> required><label class="form-check-label"><?php echo ucfirst($s); ?></label></div>
                        <?php endforeach; ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payment Status</label><div>
                        <?php foreach (['unpaid','paid','comp'] as $p): ?>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="payment_status" value="<?php echo $p; ?>" <?php echo ($record['payment_status']??'unpaid')==$p?'checked':''; ?> required><label class="form-check-label"><?php echo ucfirst($p); ?></label></div>
                        <?php endforeach; ?></div>
                    </div>
                </div>
                <div class="mb-3"><label class="form-label">Special Setup</label><div>
                    <?php foreach (['candles','flowers','cake','balloons','banner'] as $ss): ?>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="special_setup[]" value="<?php echo $ss; ?>" <?php echo in_array($ss,(array)$setup)?'checked':''; ?>><label class="form-check-label"><?php echo ucfirst($ss); ?></label></div>
                    <?php endforeach; ?></div>
                </div>
                <div class="mb-3"><label for="hostess_notes" class="form-label">Hostess Notes</label><textarea class="form-control" id="hostess_notes" name="hostess_notes" maxlength="300"><?php echo htmlspecialchars($record['hostess_notes']??''); ?></textarea></div>
                <button type="submit" class="btn btn-primary">Update Booking</button>
                <a href="modify.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div></div>
        <?php else: ?><div class="alert alert-warning">No booking found for "<?php echo htmlspecialchars($val); ?>".</div><?php endif; ?>
        <a href="../index.php" class="btn btn-link mt-3">&larr; Back to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script><script src="/dinebook/js/app.js"></script>
</body>
</html>
