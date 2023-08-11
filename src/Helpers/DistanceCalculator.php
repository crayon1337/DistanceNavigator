<?php

namespace App\Helpers;

use App\DTO\Address;

class DistanceCalculator
{
    /**
     * Calculates the distance between two given address objects.
     * @param Address $startingPoint
     * @param Address $destination
     *
     * @return float
     */
    public static function make(Address $startingPoint, Address $destination): float
    {
        $earthRadius = 6371.0; // Earth's radius in kilometers

        // Convert latitude and longitude from degrees to radians
        $startPointLatitude = deg2rad($startingPoint->getLatitude());
        $startPointLongitude = deg2rad($startingPoint->getLongitude());

        $destinationLatitude = deg2rad($destination->getLatitude());
        $destinationLongitude = deg2rad($destination->getLongitude());

        // Haversine formula
        $dlat = $destinationLatitude - $startPointLatitude;
        $dlon = $destinationLongitude - $startPointLongitude;
        $a = sin($dlat / 2) ** 2 + cos($startPointLatitude) * cos($destinationLatitude) * sin($dlon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public static function label(float $distance): string
    {
        return number_format($distance, 2) . ' km';
    }
}
