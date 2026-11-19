<?php
declare(strict_types=1);

final class TrackingService
{
    private const PROVIDERS = [
        'fastpost' => ['name' => 'FastPost', 'format' => 'JSON', 'class' => FastPostAdapter::class],
        'globalexpress' => ['name' => 'GlobalExpress', 'format' => 'XML', 'class' => GlobalExpressAdapter::class],
        'legacycargo' => ['name' => 'LegacyCargo', 'format' => 'Текст', 'class' => LegacyCargoAdapter::class],
    ];

    public static function carriers(): array
    {
        return array_map(fn (array $p): string => $p['name'], self::PROVIDERS);
    }

    public static function describe(): array
    {
        return self::PROVIDERS;
    }

    public function provider(string $carrier): TrackingProvider
    {
        if (!isset(self::PROVIDERS[$carrier])) {
            throw new TrackingException('Невідомий перевізник.');
        }
        $class = self::PROVIDERS[$carrier]['class'];
        return new $class();
    }

    public function track(string $carrier, string $number): TrackingResult
    {
        return $this->provider($carrier)->track($number);
    }
}
