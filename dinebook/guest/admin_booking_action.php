<?php
// guest/admin_booking_action.php — Staff/admin: approve, reject, or cancel a booking (AJAX)
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../auth/security.php';
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

// Only staff, host, or admin can use this
$role = $_SESSION['role'] ?? '';
if ($role === 'guest') {
    http_response_code(403);
    echo json_encode(['error' => 'Guests cannot perform this action.']);
    exit;
}

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
$newStatus = clean_string($_POST['status'] ?? '', 20);

if (!preg_match('/^[a-f0-9]{24}$/', $bookingId)) {
    echo json_encode(['error' => 'Invalid booking ID.']);
    exit;
}
if (!in_array($newStatus, ['confirmed', 'cancelled'], true)) {
    echo json_encode(['error' => 'Invalid status. Must be confirmed or cancelled.']);
    exit;
}

try {
    $booking = $bookings->findOne(['_id' => new MongoDB\BSON\ObjectId($bookingId)]);
    if (!$booking) {
        echo json_encode(['error' => 'Booking not found.']);
        exit;
    }

    // Update ALL booking slots for this reservation (multi-slot support)
    $resId = $booking['reservation_id'] ?? null;
    if ($resId) {
        // Update all booking rows that share the same reservation_id
        $bookings->updateMany(
            ['reservation_id' => (string)$resId],
            ['$set' => [
                'status'     => $newStatus,
                'updated_by' => $_SESSION['user'],
                'updated_at' => new MongoDB\BSON\UTCDateTime(),
            ]]
        );

        // Also update the reservation record
        $resStatus = $newStatus === 'confirmed' ? 'active' : 'cancelled';
        $resUpdate = ['status' => $resStatus, 'updated_at' => new MongoDB\BSON\UTCDateTime()];
        if ($newStatus === 'confirmed') {
            $resUpdate['confirmation_sent'] = true;
        }
        $reservations->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($resId)],
            ['$set' => $resUpdate]
        );
    } else {
        // Fallback: update single booking
        $bookings->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($bookingId)],
            ['$set' => [
                'status'     => $newStatus,
                'updated_by' => $_SESSION['user'],
                'updated_at' => new MongoDB\BSON\UTCDateTime(),
            ]]
        );
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log('Admin booking action error: ' . $e->getMessage());
    echo json_encode(['error' => 'Server error.']);
}
?>
