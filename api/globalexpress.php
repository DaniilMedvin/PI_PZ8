<?php
declare(strict_types=1);

require __DIR__ . '/data.php';

$shipment = mock_shipment(trim((string) ($_GET['code'] ?? '')));
if ($shipment === null) {
    http_response_code(404);
    exit;
}

$codes = [10, 20, 30, 40];
$x = fn (string $v): string => htmlspecialchars($v, ENT_XML1 | ENT_QUOTES, 'UTF-8');

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<shipment><code>' . $x($shipment['number']) . '</code>';
echo '<status code="' . $codes[$shipment['stage']] . '"/>';
echo '<events>';
foreach ($shipment['events'] as $event) {
    echo '<event date="' . gmdate('Y-m-d H:i', $event['time']) . '" place="' . $x($event['place']) . '">' . $x($event['text']) . '</event>';
}
echo '</events></shipment>';
