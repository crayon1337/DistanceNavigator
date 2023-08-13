<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Address;
use App\Exceptions\AddressNotFoundException;
use App\Helper\LocationHelper;
use App\Service\External\MapClient\MapClientInterface;
use App\Service\External\MapClient\PositionStack\Query;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

final class LocationService implements LocationInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param MapClientInterface $mapApi
     */
    public function __construct(private readonly MapClientInterface $mapApi)
    {
    }

    /**
     * @param Address $destinationAddress
     * @param Address[] $addresses
     * @return array
     * @throws AddressNotFoundException
     */
    public function getDistances(Address $destinationAddress, array $addresses): array
    {
        $query = new Query(id: $destinationAddress->getName(), address: $destinationAddress->getAddress());
        $destination = $this->mapApi->fetchGeoInformation($query)->first();

        foreach ($addresses as $address) {
            try {
                $query->setId($address->getName());
                $query->setAddress($address->getAddress());
                $addressObject = $this->mapApi->fetchGeoInformation($query)->first();

                $distances[] = [
                    'distance' => LocationHelper::calculateDistance($addressObject, $destination),
                    'name' => $address->getName(),
                    'address' => $address->getAddress(),
                ];
            } catch (AddressNotFoundException $e) {
                $this->logger->error("Could not resolve information for {$address->getName()}", $e->getTrace());
            }
        }

        usort($distances, fn ($a, $b) => $a['distance'] <=> $b['distance']);

        return $this->formatDistances($distances);
    }

    /**
     * @param array $distances
     * @return array
     */
    private function formatDistances(array $distances): array
    {
        return array_map(function ($index, $distance) {
            return [
                'id' => ++$index,
                'distance' => LocationHelper::getDistanceLabel($distance['distance']),
                'name' => $distance['name'],
                'address' => $distance['address']
            ];
        }, array_keys($distances), $distances);
    }
}
