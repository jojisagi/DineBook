<?php
// api/guests.php — JSON API endpoint for guests
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');
try {
    $cursor = $guests->find([], ['sort' => ['registered_at' => -1]]);
    $data = [];
    foreach ($cursor as $doc) {
        $dietary = $doc['dietary_restrictions'] ?? [];
        if ($dietary instanceof MongoDB\Model\BSONArray) $dietary = $dietary->getArrayCopy();
        $data[] = [
            'id'            => (string)$doc['_id'],
            'full_name'     => $doc['full_name'] ?? '',
            'email'         => $doc['email'] ?? '',
            'phone'         => $doc['phone'] ?? '',
            'loyalty_tier'  => $doc['loyalty_tier'] ?? 'standard',
            'visit_count'   => $doc['visit_count'] ?? 0,
            'dietary'       => is_array($dietary) ? $dietary : [],
        ];
    }
    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
