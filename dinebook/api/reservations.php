<?php
// api/reservations.php — JSON API endpoint for reservations
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');
try {
    $cursor = $reservations->find([], ['limit' => 50, 'sort' => ['created_at' => -1]]);
    $data = [];
    foreach ($cursor as $doc) {
        $data[] = [
            'id'     => (string)$doc['_id'],
            'guest'  => $doc['guest_name'] ?? '',
            'date'   => $doc['reservation_date'] ?? '',
            'zone'   => $doc['zone'] ?? '',
            'party'  => $doc['party_size'] ?? 0,
            'status' => $doc['status'] ?? 'active',
        ];
    }
    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
