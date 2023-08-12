<?php

declare(strict_types=1);

namespace App\Service\External\MapClient\PositionStack;

use App\Exceptions\AddressNotFoundException;
use App\Service\External\MapClient\MapClientInterface;
use App\Service\External\MapClient\MapCollection;
use App\Service\External\MapClient\QueryInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * This class will be used to interact with PositionStack API
 * In order to find out geolocation information
 */
final class PositionStackAPI implements MapClientInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly HttpClientInterface $positionStackClient,
        private $accessKey
    ) {
    }

    /**
     * @param QueryInterface $query
     * @return MapCollection
     * @throws AddressNotFoundException
     */
    public function fetchGeoInformation(QueryInterface $query): MapCollection
    {
        $query->setAccessKey($this->accessKey);

        try {
            $response = $this->positionStackClient->request('GET', '/v1/forward', [
                'query' => $query->informationPayload()
            ]);

            return $this->hydrateCollection(id: $query->getId(), data: $response->toArray());
        } catch (
            ServerExceptionInterface | ClientExceptionInterface | TransportExceptionInterface |
            RedirectionExceptionInterface | DecodingExceptionInterface $exception
        ) {
            $message = sprintf(
                'Could not fetch forward GEO information for %s. Message: %s',
                $query->getId(),
                $exception->getMessage()
            );

            $this->logger->critical(message: $message, context: $exception->getTrace());

            // Wrapping all exceptions to one custom exception.
            throw new AddressNotFoundException(message: $message);
        }
    }

    /**
     * @throws AddressNotFoundException
     */
    private function hydrateCollection(string $id, array $data): PositionStackCollection
    {
        if (empty($data['data'])) {
            throw new AddressNotFoundException(message: "Could not find results for $id");
        }

        $addresses = array_map(
            callback: fn ($row) => new PositionStackAddress(
                id: $id,
                latitude: $row['latitude'],
                longitude: $row['longitude'],
                type: $row['type'],
                name: $row['name'],
                number: $row['number'],
                postal_code: $row['postal_code'],
                street: $row['street'],
                confidence: $row['confidence'],
                region: $row['region'],
                region_code: $row['region_code'],
                county: $row['county'],
                locality: $row['locality'],
                administrative_area: $row['administrative_area'],
                neighbourhood: $row['neighbourhood'],
                country: $row['country'],
                country_code: $row['country_code'],
                continent: $row['continent'],
                label: $row['label']
            ),
            array: $data['data']
        );

        return new PositionStackCollection(data: $addresses);
    }
}
