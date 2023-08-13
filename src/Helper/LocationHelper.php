<?php

declare(strict_types=1);

namespace App\Helper;

use App\Service\External\MapClient\MapAddress;

final class LocationHelper
{
    /**
     * Calculate distance between two points by latitude and longitude.
     *
     * @param MapAddress $startingPoint
     * @param MapAddress $destination
     *
     * @return float Distance between points in [km]
     */
    public static function calculateDistance(MapAddress $startingPoint, MapAddress $destination): float
    {
        $rad = M_PI / 180;

        //Calculate distance from latitude and longitude
        $theta = $startingPoint->getLongitude() - $destination->getLongitude();

        $distance = sin($startingPoint->getLatitude() * $rad)
            * sin($destination->getLatitude() * $rad)
            + cos($startingPoint->getLatitude() * $rad)
            * cos($destination->getLatitude() * $rad)
            * cos($theta * $rad);

        return acos($distance) / $rad * 60 *  1.853;
    }

    /**
     * @param float $distance
     * @return string
     */
    public static function getDistanceLabel(float $distance): string
    {
        return number_format($distance, 2) . ' km';
    }
}
