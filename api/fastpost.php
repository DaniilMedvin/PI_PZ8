<?php
declare(strict_types=1);

require __DIR__ . '/data.php';

header('Content-Type: application/json; charset=utf-8');

$shipment = mock_shipment(trim((string) ($_GET['number'] ?? '')));
if ($shipment === null) {
    http_response_code(404);
    echo json_encode(['error' => 'not_found']);
    exit;
}

$states = ['NEW', 'MOVING', 'DONE', 'HOLD'];
$history = array_map(fn (array $e): array => [
    'ts' => gmdate('Y-m-d\TH:i:s\Z', $e['time']),
    'city' => $e['place'],
    'note' => $e['text'],
], $shipment['events']);

echo json_encode([
    'trackingNumber' => $shipment['number'],
    'state' => $states[$shipment['stage']],
    'history' => $history,
], JSON_UNESCAPED_UNICODE);
