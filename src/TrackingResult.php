<?php
declare(strict_types=1);

final class TrackingResult
{
    public function __construct(
        public readonly string $status,
        public readonly array $events
    ) {
    }
}
