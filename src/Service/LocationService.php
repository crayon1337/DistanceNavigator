<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Address;
use App\Exceptions\AddressNotFoundException;
use App\Helpers\DistanceCalculator;
use App\Service\External\MapClientInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class LocationService implements LocationInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param MapClientInterface $mapApi
     */
    public function __construct(protected MapClientInterface $mapApi)
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
        $destination = $this->mapApi->resolveAddressInfo(address: $destinationAddress);
        $distances = [];

        foreach ($addresses as $address) {
            // If things goes wrong. We need to continue. So that, we only skip the address
            // We had problem with. Better some than none.
            try {
                $addressObject = $this->mapApi->resolveAddressInfo($address);

                $distances[] = [
                    'distance' => DistanceCalculator::make($addressObject, $destination),
                    'name' => $address->getName(),
                    'address' => $address->getAddress(),
                ];
            } catch (AddressNotFoundException) {
                $this->logger->error("Could not resolve information for {$address->getName()}");
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
