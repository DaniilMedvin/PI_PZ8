<?php
declare(strict_types=1);

final class FastPostAdapter implements TrackingProvider
{
    private const STATES = [
        'NEW' => 'created',
        'MOVING' => 'in_transit',
        'DONE' => 'delivered',
        'HOLD' => 'delayed',
    ];

    public function track(string $number): TrackingResult
    {
        $body = HttpClient::get(API_BASE . '/api/fastpost.php?number=' . urlencode($number));
        $data = json_decode($body, true);
        if (!is_array($data) || !isset($data['state'], $data['history'])) {
            throw new TrackingException('FastPost повернув відповідь у невідомому форматі.');
        }

        $events = [];
        foreach ($data['history'] as $item) {
            $events[] = [
                'time' => date('Y-m-d H:i', strtotime((string) $item['ts'])),
                'place' => (string) $item['city'],
                'text' => (string) $item['note'],
            ];
        }

        return new TrackingResult(self::STATES[$data['state']] ?? 'created', $events);
    }
}
