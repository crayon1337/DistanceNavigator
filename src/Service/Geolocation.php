<?php

namespace App\Service;

use App\DTO\Address;
use App\Helpers\Sorter;
use App\Service\External\PositionStackAPI;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class Geolocation implements LoggerAwareInterface
{
    public function __construct(protected PositionStackAPI $positionStackAPI, protected LoggerInterface $logger)
    {

    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getDistances(Address $destinationAddress, array $locations)
    {
        $destination = $this->positionStackAPI->getForward(address: $destinationAddress);

        if (empty($destination)) {
            $this->logger->critical('Could not fetch destination info. Terminating...');
            return;
        }

        $startPoints = $this->getPoints($locations);

        return $this->resolveDistances($destination, $startPoints);
    }

    private function resolveDistances(Address $destination, array $startPoints)
    {
        $data = [
            [
                'ID',
                'From',
                'To',
                'Distance',
                'Distance Label'
            ]
        ];

        foreach ($startPoints as $index => $startPoint) {
            list($distance, $label) = $this->calculateDistance(
                destination: $destination,
                startingPoint: $startPoint
            );

            $data[] = [
                'id' => $index + 1,
                'from' => $startPoint->getTitle(),
                'to' => $destination->getTitle(),
                'distance' => $distance,
                'distance_label' => $label
            ];
        }

        // Sorting results so that the closest route will have higher priorty.
        $data = Sorter::make(data: $data, key: 'distance');

        return $data;
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

    private function calculateDistance(Address $startingPoint, Address $destination): array
    {
        $earthRadius = 6371.0; // Earth's radius in kilometers

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
        $distance = $earthRadius * $c;

        return [
            $distance,
            number_format($distance, 2) . " km",
        ];
    }
}
