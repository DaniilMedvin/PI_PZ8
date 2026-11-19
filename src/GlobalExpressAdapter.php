<?php
declare(strict_types=1);

final class GlobalExpressAdapter implements TrackingProvider
{
    private const CODES = [
        10 => 'created',
        20 => 'in_transit',
        30 => 'delivered',
        40 => 'delayed',
    ];

    public function track(string $number): TrackingResult
    {
        $body = HttpClient::get(API_BASE . '/api/globalexpress.php?code=' . urlencode($number));
        $xml = @simplexml_load_string($body);
        if ($xml === false || !isset($xml->status['code'])) {
            throw new TrackingException('GlobalExpress повернув відповідь у невідомому форматі.');
        }

        $events = [];
        foreach ($xml->events->event as $event) {
            $events[] = [
                'time' => (string) $event['date'],
                'place' => (string) $event['place'],
                'text' => (string) $event,
            ];
        }

        return new TrackingResult(self::CODES[(int) $xml->status['code']] ?? 'created', $events);
    }
}
