<?php
declare(strict_types=1);

final class HttpClient
{
    public static function get(string $url): string
    {
        $context = stream_context_create([
            'http' => ['timeout' => 5, 'ignore_errors' => true],
        ]);
        $body = @file_get_contents($url, false, $context);
        $code = 0;
        if (isset($http_response_header[0]) && preg_match('#\s(\d{3})\s#', $http_response_header[0], $m)) {
            $code = (int) $m[1];
        }
        if ($body === false || $code === 0) {
            throw new TrackingException('Сервіс відстеження недоступний.');
        }
        if ($code === 404) {
            throw new TrackingException('Сервіс не знайшов відправлення з таким номером.');
        }
        if ($code !== 200) {
            throw new TrackingException('Сервіс відстеження повернув помилку.');
        }
        return $body;
    }
}
