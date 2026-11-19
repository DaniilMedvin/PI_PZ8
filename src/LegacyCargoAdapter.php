<?php
declare(strict_types=1);

final class LegacyCargoAdapter implements TrackingProvider
{
    private const LETTERS = [
        'A' => 'created',
        'B' => 'in_transit',
        'C' => 'delivered',
        'D' => 'delayed',
    ];

    public function track(string $number): TrackingResult
    {
        $body = HttpClient::get(API_BASE . '/api/legacycargo.php?id=' . urlencode($number));
        $lines = preg_split('/\r\n|\n/', trim($body));
        $head = explode(';', (string) array_shift($lines));
        if (count($head) !== 2 || !isset(self::LETTERS[$head[1]])) {
            throw new TrackingException('LegacyCargo повернув відповідь у невідомому форматі.');
        }

        $events = [];
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            if (count($parts) !== 3) {
                continue;
            }
            $date = DateTime::createFromFormat('d.m.Y H:i', $parts[0]);
            $events[] = [
                'time' => $date ? $date->format('Y-m-d H:i') : $parts[0],
                'place' => $parts[1],
                'text' => $parts[2],
            ];
        }

        return new TrackingResult(self::LETTERS[$head[1]], $events);
    }
}
