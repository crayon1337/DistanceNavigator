<?php

namespace App\Service;

use App\DTO\Address;
use App\Helpers\DistanceCalculator;
use App\Helpers\Sorter;
use App\Service\External\MapClientInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class Geolocation implements GeolocationInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(protected MapClientInterface $mapApi)
    {
    }

    public function getDistances(Address $destinationAddress, array $addresses): array
    {
        $destination = $this->mapApi->resolveAddressInfo(address: $destinationAddress);

        if (empty($destination)) {
            $this->logger->critical('Could not fetch destination info. Terminating...');
            return [];
        }

        $distances = [];

        foreach ($addresses as $address) {
            $addressObject = $this->mapApi->resolveAddressInfo($address);

            if (is_null($addressObject)) {
                continue;
            }

            $distance = DistanceCalculator::make($addressObject, $destination);

            $distances[] = [
                'distance' => $distance,
                'name' => $address->getName(),
                'address' => $address->getAddress(),
            ];
        }

        $distances = Sorter::make(data: $distances, key: 'distance');

        $formattedDistances = [];

        foreach ($distances as $index => $distance) {
            $formattedDistances[] = [
                'id' => $index + 1,
                'distance' => DistanceCalculator::label($distance['distance']),
                'name' => $distance['name'],
                'address' => $distance['address']
            ];
        }

        return $formattedDistances;
    }
}
