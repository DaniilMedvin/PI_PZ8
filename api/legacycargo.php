<?php
declare(strict_types=1);

require __DIR__ . '/data.php';

$shipment = mock_shipment(trim((string) ($_GET['id'] ?? '')));
if ($shipment === null) {
    http_response_code(404);
    exit;
}

$letters = ['A', 'B', 'C', 'D'];

header('Content-Type: text/plain; charset=utf-8');
echo $shipment['number'] . ';' . $letters[$shipment['stage']] . "\n";
foreach ($shipment['events'] as $event) {
    echo gmdate('d.m.Y H:i', $event['time']) . '|' . $event['place'] . '|' . $event['text'] . "\n";
}
