<?php
// guest/my_reservations.php — View and cancel own reservations
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../auth/security.php';
require_once __DIR__ . '/../config.php';

$isGuest = ($_SESSION['role'] ?? '') === 'guest';
$user = $_SESSION['user'];

// Fetch this user's bookings, sorted by date desc
try {
    $myBookings = iterator_to_array(
        $bookings->find(
            ['guest_user' => $user],
            ['sort' => ['booking_date' => -1, 'time_slot' => -1]]
        )
    );
} catch (Exception $e) {
    $myBookings = [];
}

$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineBook — My Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $isGuest ? '/dinebook/guest/dashboard.php' : '/dinebook/index.php'; ?>">DineBook</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <?php if ($isGuest): ?>
                        <li class="nav-item"><a class="nav-link" href="/dinebook/guest/dashboard.php">Home</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/dinebook/index.php">Staff Dashboard</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="/dinebook/guest/floormap.php">Floor Map</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/dinebook/guest/my_reservations.php">My Reservations</a></li>
                    <li class="nav-item"><a class="nav-link" href="/dinebook/auth/logout.php">Logout</a></li>
                </ul>
                <span class="navbar-text ms-auto" style="color:var(--dinebook-gold);">
                    <?php echo htmlspecialchars($user); ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="page-header">My Reservations</h2>

        <div id="cancel-msg"></div>

        <?php if (empty($myBookings)): ?>
            <div class="text-center py-5">
                <div style="font-size:3rem;">&#128197;</div>
                <p class="lead text-muted">You have no reservations yet.</p>
                <a href="/dinebook/guest/floormap.php" class="btn btn-primary">Reserve a Table</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Table</th>
                            <th>Zone</th>
                            <th>Party</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($myBookings as $b):
                        $bDate  = (string)($b['booking_date'] ?? '');
                        $bTime  = (string)($b['time_slot'] ?? $b['booking_time'] ?? '');
                        $bTable = (int)($b['table_number'] ?? 0);
                        $bZone  = (string)($b['zone'] ?? '');
                        $bParty = (int)($b['party_size'] ?? 0);
                        $bStatus = (string)($b['status'] ?? 'confirmed');
                        $bNotes = (string)($b['notes'] ?? '');
                        $bId    = (string)$b['_id'];
                        $canCancel = ($bDate >= $today && in_array($bStatus, ['confirmed', 'pending']));
                    ?>
                        <tr id="row-<?php echo e($bId); ?>">
                            <td><?php echo e($bDate); ?></td>
                            <td><?php echo e($bTime); ?></td>
                            <td>T<?php echo $bTable; ?></td>
                            <td><?php echo e(ucfirst($bZone)); ?></td>
                            <td><?php echo $bParty; ?></td>
                            <td>
                                <?php if ($bStatus === 'confirmed'): ?>
                                    <span class="badge bg-success">Confirmed</span>
                                <?php elseif ($bStatus === 'cancelled'): ?>
                                    <span class="badge bg-secondary">Cancelled</span>
                                <?php elseif ($bStatus === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">Pending Approval</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark"><?php echo e(ucfirst($bStatus)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($bNotes); ?></td>
                            <td>
                                <?php if ($canCancel): ?>
                                    <button class="btn btn-sm btn-outline-danger btn-cancel"
                                            data-id="<?php echo e($bId); ?>">Cancel</button>
                                <?php elseif ($bStatus === 'cancelled'): ?>
                                    <span class="text-muted">—</span>
                                <?php else: ?>
                                    <span class="text-muted">Past</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer container"><p>DineBook &copy; 2026</p></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(function() {
        $('.btn-cancel').on('click', function() {
            const $btn = $(this);
            const id = $btn.data('id');
            if (!confirm('Are you sure you want to cancel this reservation?')) return;

            $btn.prop('disabled', true).text('Cancelling...');
            $.post('/dinebook/guest/cancel_process.php', {
                csrf_token: '<?= htmlspecialchars(csrf_token()) ?>',
                booking_id: id
            })
            .done(function(res) {
                if (res.success) {
                    $('#cancel-msg').html('<div class="alert alert-success">Reservation cancelled successfully.</div>');
                    // Update row visually
                    const $row = $('#row-' + id);
                    $row.find('.badge').removeClass('bg-success').addClass('bg-secondary').text('Cancelled');
                    $btn.replaceWith('<span class="text-muted">—</span>');
                } else {
                    $('#cancel-msg').html('<div class="alert alert-danger">' + (res.error || 'Could not cancel.') + '</div>');
                    $btn.prop('disabled', false).text('Cancel');
                }
            })
            .fail(function() {
                $('#cancel-msg').html('<div class="alert alert-danger">Network error.</div>');
                $btn.prop('disabled', false).text('Cancel');
            });
        });
    });
    </script>
</body>
</html>
