<?php
// api/availability.php — JSON: available tables for date+zone
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$date = $_GET['date'] ?? '';
$zone = $_GET['zone'] ?? '';

if (empty($date) || empty($zone)) {
    echo json_encode(['error' => 'Parameters date and zone are required.']);
    exit;
}

try {
    // Get all tables in the specified zone
    $allTables = iterator_to_array($tables->find(['zone' => $zone, 'status' => 'available']));

    // Get booked table IDs for that date+zone
    $bookedCursor = $bookings->find(['booking_date' => $date]);
    $bookedTableIds = [];
    foreach ($bookedCursor as $b) {
        $bookedTableIds[] = $b['table_id'] ?? '';
    }

    // Filter available tables
    $available = [];
    foreach ($allTables as $t) {
        $tid = (string)$t['_id'];
        if (!in_array($tid, $bookedTableIds)) {
            $available[] = [
                'id'       => $tid,
                'number'   => $t['table_number'] ?? 0,
                'capacity' => $t['capacity'] ?? 0,
                'zone'     => $t['zone'] ?? '',
                'shape'    => $t['shape'] ?? '',
            ];
        }
    }
    echo json_encode($available);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
