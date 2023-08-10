<?php

namespace App\Service;

use App\DTO\Address;
use App\Service\External\PositionStackAPI;

class Geolocation
{
    public function __construct(protected PositionStackAPI $positionStackAPI)
    {

    }

    public function getDistance(Address $destination, array $locations)
    {
        $destination = $this->getDestination($destination);

        return array_map(
            callback: fn(Address $startingPoint): array =>
            [
                'from' => $startingPoint->getLabel(),
                'to' => $destination->getLabel(),
                'distance' => $this->calculateDistance(
                    destination: $destination,
                    startingPoint: $startingPoint
                )
            ]
            , array: $this->getPoints($locations)
        );
    }

    private function getPoints(array $locations)
    {
        $points = array_map(
            callback: fn($location) =>
            $this->positionStackAPI->getForward(address: $location)
            , array: $locations
        );

        return array_filter($points, fn(?Address $point) => !is_null($point));
    }

    public function getDestination(Address $address): ?Address
    {
        return $this->positionStackAPI->getForward(address: $address);
    }

    private function calculateDistance(Address $startingPoint, Address $destination)
    {
        // Convert latitude and longitude from degrees to radians
        $startPointLatitude = deg2rad($startingPoint->getLatitude());
        $startPointLongitude = deg2rad($startingPoint->getLongitude());

        $desintationLatitude = deg2rad($destination->getLatitude());
        $destinationLongitude = deg2rad($destination->getLongitude());

        // Haversine formula
        $dlat = $desintationLatitude - $startPointLatitude;
        $dlon = $destinationLongitude - $startPointLongitude;
        $a = sin($dlat / 2) ** 2 + cos($startPointLatitude) * cos($desintationLatitude) * sin($dlon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $radius_of_earth = 6371.0; // Earth's radius in kilometers
        $distance = $radius_of_earth * $c;

        return number_format($distance, 2);
    }
}
