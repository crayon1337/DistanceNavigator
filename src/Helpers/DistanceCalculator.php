<?php

declare(strict_types=1);

namespace App\Helpers;

use App\DTO\Address;

class DistanceCalculator
{
    /**
     * Optimized algorithm from http://www.codexworld.com
     *
     * @param Address $startingPoint
     * @param Address $destination
     *
     * @return float Distance between points in [km]
     */
    public static function make(Address $startingPoint, Address $destination): float
    {
        $rad = M_PI / 180;

        //Calculate distance from latitude and longitude
        $theta = $startingPoint->getLongitude() - $destination->getLongitude();

        $dist = sin($startingPoint->getLatitude() * $rad)
            * sin($destination->getLatitude() * $rad)
            + cos($startingPoint->getLatitude() * $rad)
            * cos($destination->getLatitude() * $rad)
            * cos($theta * $rad);

        return acos($dist) / $rad * 60 *  1.853;
    }

    public static function label(float $distance): string
    {
        return number_format($distance, 2) . ' km';
    }
}
