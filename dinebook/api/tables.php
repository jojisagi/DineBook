<?php
// api/tables.php — JSON API endpoint for tables
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');
try {
    $cursor = $tables->find([], ['sort' => ['table_number' => 1]]);
    $data = [];
    foreach ($cursor as $doc) {
        $data[] = [
            'id'       => (string)$doc['_id'],
            'number'   => $doc['table_number'] ?? 0,
            'capacity' => $doc['capacity'] ?? 0,
            'zone'     => $doc['zone'] ?? '',
            'shape'    => $doc['shape'] ?? '',
            'status'   => $doc['status'] ?? 'available',
        ];
    }
    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
