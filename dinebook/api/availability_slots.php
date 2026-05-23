<?php
// api/availability_slots.php — JSON: 30-min slot availability per table for a date + zone
// Returns booking details for occupied slots (so admin can see who booked)
require_once __DIR__ . '/../auth/security.php';
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$date = clean_string($_GET['date'] ?? '', 10);
$zone = clean_string($_GET['zone'] ?? '', 20);

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || $zone === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Parameters date (YYYY-MM-DD) and zone are required.']);
    exit;
}

try {
    // 1) Get all tables in the requested zone
    $allTables = iterator_to_array(
        $tables->find(['zone' => (string)$zone], ['sort' => ['table_number' => 1]])
    );

    // 2) Get all ACTIVE bookings for that date (exclude cancelled ones so those slots become free)
    $bookedCursor = $bookings->find([
        'booking_date' => $date,
        'status'       => ['$ne' => 'cancelled'],
    ]);

    // Build lookup: table_id => { time_slot => booking_info }
    $occupied = [];
    foreach ($bookedCursor as $b) {
        $tid  = (string)($b['table_id'] ?? '');
        $time = (string)($b['time_slot'] ?? $b['booking_time'] ?? '');
        if ($tid !== '' && $time !== '') {
            $occupied[$tid][$time] = [
                'guest_user'  => (string)($b['guest_user'] ?? ''),
                'guest_email' => (string)($b['guest_email'] ?? ''),
                'party_size'  => (int)($b['party_size'] ?? 0),
                'notes'       => (string)($b['notes'] ?? ''),
                'status'      => (string)($b['status'] ?? 'confirmed'),
                'booked_by'   => (string)($b['booked_by'] ?? ''),
                'booking_id'  => (string)$b['_id'],
            ];
        }
    }

    // 3) Generate 30-min slots from restaurant opening to closing
    $openHour  = 10; // 10:00 AM
    $closeHour = 22; // 10:00 PM (last slot 21:30)
    $slots = [];
    for ($h = $openHour; $h < $closeHour; $h++) {
        $slots[] = sprintf('%02d:00', $h);
        $slots[] = sprintf('%02d:30', $h);
    }

    // 4) Build response: each table with its slot availability + booking details
    $result = [];
    foreach ($allTables as $t) {
        $tid = (string)$t['_id'];
        $tableSlots = [];
        $occupiedSlots = $occupied[$tid] ?? [];
        foreach ($slots as $s) {
            $slotData = [
                'time'      => $s,
                'available' => !isset($occupiedSlots[$s]),
            ];
            // Include booking details for occupied slots (admin can see who booked)
            if (isset($occupiedSlots[$s])) {
                $slotData['booking'] = $occupiedSlots[$s];
            }
            $tableSlots[] = $slotData;
        }
        $result[] = [
            'id'       => $tid,
            'number'   => $t['table_number'] ?? 0,
            'capacity' => $t['capacity'] ?? 0,
            'zone'     => $t['zone'] ?? '',
            'shape'    => $t['shape'] ?? 'square',
            'status'   => $t['status'] ?? 'available',
            'slots'    => $tableSlots,
        ];
    }

    echo json_encode($result);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
?>
