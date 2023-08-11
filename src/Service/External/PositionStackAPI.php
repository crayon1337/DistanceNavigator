<?php

namespace App\Service\External;

use App\DTO\Address;
use App\Helpers\Sorter;
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
class PositionStackAPI implements MapClientInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        protected HttpClientInterface $httpClient,
        protected $accessKey
    ) {
    }

    public function resolveAddressInfo(Address $address): ?Address
    {
        try {
            $response = $this->httpClient->request('GET', 'http://api.positionstack.com/v1/forward', [
                'query' => [
                    'access_key' => $this->accessKey,
                    'query' => $address->getName()
                ]
            ]);

            $data = $response->toArray();
            $data = Sorter::make(data: $data['data'], key: 'confidence', direction: 'DESC');

            if (empty($data)) {
                $this->logger->critical(sprintf('Could not find results for %s', $address->getName()));
                return null;
            }

            return $this->hydrateAddressObject($address, $data);
        } catch (
            ServerExceptionInterface |
            ClientExceptionInterface |
            TransportExceptionInterface |
            RedirectionExceptionInterface |
            DecodingExceptionInterface $exception
        ) {
            $this->logger->critical(
                sprintf(
                    'Could not fetch forward geo information to %s. Message: %s',
                    $address->getName(),
                    $exception->getMessage()
                ), $exception->getTrace()
            );

            return null;
        }
    }

    private function hydrateAddressObject(Address $address, array $response): Address
    {
        $address->setLatitude($response[0]['latitude']);
        $address->setLongitude($response[0]['longitude']);
        $address->setData($response[0]);

        return $address;
    }
}
