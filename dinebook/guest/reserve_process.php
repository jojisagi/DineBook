<?php
// guest/reserve_process.php — Process a guest table reservation (AJAX)
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../auth/security.php';
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST required']);
    exit;
}

// CSRF check
$sent = $_POST['csrf_token'] ?? '';
$real = $_SESSION['csrf_token'] ?? '';
if (!is_string($sent) || !is_string($real) || $real === '' || !hash_equals($real, $sent)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid security token. Refresh the page.']);
    exit;
}

// Sanitize inputs
$tableId   = clean_string($_POST['table_id'] ?? '', 30);
$date      = clean_string($_POST['date'] ?? '', 10);
$timeSlot  = clean_string($_POST['time_slot'] ?? '', 5);
$partySize = clean_int($_POST['party_size'] ?? 2, 1, 20);
$duration  = clean_int($_POST['duration'] ?? 60, 30, 180);
$notes     = clean_string($_POST['notes'] ?? '', 300);

// Validate
if (!preg_match('/^[a-f0-9]{24}$/', $tableId)) {
    echo json_encode(['error' => 'Invalid table.']);
    exit;
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || $date < date('Y-m-d')) {
    echo json_encode(['error' => 'Invalid or past date.']);
    exit;
}
if (!preg_match('/^\d{2}:\d{2}$/', $timeSlot)) {
    echo json_encode(['error' => 'Invalid time slot.']);
    exit;
}

try {
    // Build list of consecutive 30-min slots this reservation needs
    $slotsNeeded = (int)ceil($duration / 30);
    $allSlots = [];
    list($startH, $startM) = explode(':', $timeSlot);
    $startMin = (int)$startH * 60 + (int)$startM;
    for ($i = 0; $i < $slotsNeeded; $i++) {
        $min = $startMin + ($i * 30);
        $allSlots[] = sprintf('%02d:%02d', intdiv($min, 60), $min % 60);
    }

    // Check ALL needed slots are free (race condition guard)
    $existing = $bookings->findOne([
        'table_id'     => $tableId,
        'booking_date' => $date,
        'time_slot'    => ['$in' => $allSlots],
        'status'       => ['$ne' => 'cancelled'],
    ]);
    if ($existing) {
        echo json_encode(['error' => 'Sorry, one or more slots were just booked. Please choose another time.']);
        exit;
    }

    // Verify the table exists
    $table = $tables->findOne(['_id' => new MongoDB\BSON\ObjectId($tableId)]);
    if (!$table) {
        echo json_encode(['error' => 'Table not found.']);
        exit;
    }

    // Check party size vs capacity
    $capacity = (int)($table['capacity'] ?? 0);
    if ($partySize > $capacity) {
        echo json_encode(['error' => "Party of $partySize exceeds table capacity ($capacity seats)."]);
        exit;
    }

    // Look up guest user record for name/email/phone
    $guestUser  = $_SESSION['user'];
    $guestEmail = $_SESSION['email'] ?? '';
    $userDoc    = $db->users->findOne(['username' => (string)$guestUser]);
    $guestPhone = (string)($userDoc['phone'] ?? '');
    $guestName  = (string)($userDoc['username'] ?? $guestUser);
    if ($guestEmail === '' && $userDoc) {
        $guestEmail = (string)($userDoc['email'] ?? '');
    }
    $zoneStr = (string)($table['zone'] ?? '');

    // End time for display
    $endMin = $startMin + $duration;
    $endTime = sprintf('%02d:%02d', intdiv($endMin, 60), $endMin % 60);

    // 1) Insert into reservations collection (staff report)
    $resDoc = $reservations->insertOne([
        'guest_name'          => $guestName,
        'email'               => (string)$guestEmail,
        'phone'               => $guestPhone,
        'reservation_date'    => $date,
        'arrival_time'        => $timeSlot,
        'end_time'            => $endTime,
        'duration'            => $duration,
        'party_size'          => $partySize,
        'zone'                => $zoneStr,
        'dietary_restrictions'=> [],
        'occasion'            => 'none',
        'guest_type'          => 'new',
        'status'              => 'pending',
        'confirmation_sent'   => false,
        'special_requests'    => $notes,
        'booked_by'           => 'guest',
        'guest_user'          => (string)$guestUser,
        'created_at'          => new MongoDB\BSON\UTCDateTime(),
    ]);
    $reservationId = (string)$resDoc->getInsertedId();

    // 2) Insert one booking row PER slot (so each 30-min block is occupied on the map)
    foreach ($allSlots as $slot) {
        $bookings->insertOne([
            'reservation_id' => $reservationId,
            'table_id'       => $tableId,
            'table_number'   => (int)($table['table_number'] ?? 0),
            'booking_date'   => $date,
            'time_slot'      => $slot,
            'booking_time'   => $slot,
            'start_time'     => $timeSlot,
            'end_time'       => $endTime,
            'duration'       => $duration,
            'guest_user'     => (string)$guestUser,
            'guest_email'    => (string)$guestEmail,
            'party_size'     => $partySize,
            'notes'          => $notes,
            'zone'           => $zoneStr,
            'status'         => 'pending',
            'booked_by'      => 'guest',
            'created_at'     => new MongoDB\BSON\UTCDateTime(),
        ]);
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log('Reserve error: ' . $e->getMessage());
    echo json_encode(['error' => 'Server error. Please try again.']);
}
?>
