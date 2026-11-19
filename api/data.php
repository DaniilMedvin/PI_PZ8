<?php
declare(strict_types=1);

function mock_shipment(string $number): ?array
{
    if ($number === '' || str_starts_with($number, 'NF')) {
        return null;
    }

    $seed = crc32($number);
    $stage = $seed % 4;
    $cities = ['Київ', 'Біла Церква', 'Полтава', 'Харків', 'Дніпро', 'Львів', 'Одеса'];
    $texts = [
        'Прийнято від відправника',
        'Відправлено із сортувального центру',
        'Прибуло до сортувального центру',
        'Передано кур\'єру',
        'Вручено одержувачу',
    ];
    $count = [1, 3, 5, 3][$stage];
    $base = strtotime('2026-11-10 08:00:00');

    $events = [];
    for ($i = 0; $i < $count; $i++) {
        $events[] = [
            'time' => $base + $i * 9 * 3600,
            'place' => $cities[($seed + $i) % count($cities)],
            'text' => $texts[$i],
        ];
    }
    if ($stage === 3) {
        $events[$count - 1]['text'] = 'Затримка: перевірка супровідних документів';
    }

    return ['number' => $number, 'stage' => $stage, 'events' => $events];
}
