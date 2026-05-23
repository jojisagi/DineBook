<?php
// guest/cancel_process.php — Cancel a guest's own reservation (AJAX)
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../auth/security.php';
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST required']);
    exit;
}

// CSRF
$sent = $_POST['csrf_token'] ?? '';
$real = $_SESSION['csrf_token'] ?? '';
if (!is_string($sent) || !is_string($real) || $real === '' || !hash_equals($real, $sent)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid security token.']);
    exit;
}

$bookingId = clean_string($_POST['booking_id'] ?? '', 30);
if (!preg_match('/^[a-f0-9]{24}$/', $bookingId)) {
    echo json_encode(['error' => 'Invalid booking ID.']);
    exit;
}

try {
    // Find the booking — must belong to this user
    $booking = $bookings->findOne([
        '_id'        => new MongoDB\BSON\ObjectId($bookingId),
        'guest_user' => $_SESSION['user'],
    ]);

    if (!$booking) {
        echo json_encode(['error' => 'Reservation not found or not yours.']);
        exit;
    }

    // Cannot cancel past bookings
    $bDate = (string)($booking['booking_date'] ?? '');
    if ($bDate < date('Y-m-d')) {
        echo json_encode(['error' => 'Cannot cancel past reservations.']);
        exit;
    }

    // Cannot cancel already-cancelled bookings
    if (($booking['status'] ?? '') === 'cancelled') {
        echo json_encode(['error' => 'Already cancelled.']);
        exit;
    }

    // Cancel ALL booking slots for this reservation (multi-slot support)
    $resId = $booking['reservation_id'] ?? null;
    if ($resId) {
        $bookings->updateMany(
            ['reservation_id' => (string)$resId],
            ['$set' => [
                'status'         => 'cancelled',
                'booking_status' => 'cancelled',
                'cancelled_at'   => new MongoDB\BSON\UTCDateTime(),
            ]]
        );
        $reservations->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($resId)],
            ['$set' => [
                'status'       => 'cancelled',
                'cancelled_at' => new MongoDB\BSON\UTCDateTime(),
            ]]
        );
    } else {
        $bookings->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($bookingId)],
            ['$set' => [
                'status'       => 'cancelled',
                'cancelled_at' => new MongoDB\BSON\UTCDateTime(),
            ]]
        );
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log('Cancel error: ' . $e->getMessage());
    echo json_encode(['error' => 'Server error.']);
}
?>
