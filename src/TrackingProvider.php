<?php
declare(strict_types=1);

interface TrackingProvider
{
    public function track(string $number): TrackingResult;
}
