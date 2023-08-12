<?php

declare(strict_types=1);

namespace App\Helper;

use App\DTO\Address;

class LocationHelper
{
    /**
     * Calculate distance between two points by latitude and longitude.
     *
     * @param Address $startingPoint
     * @param Address $destination
     *
     * @return float Distance between points in [km]
     */
    public static function calculateDistance(Address $startingPoint, Address $destination): float
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

    public static function getDistanceLabel(float $distance): string
    {
        return number_format($distance, 2) . ' km';
    }
}
