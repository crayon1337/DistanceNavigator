<?php

declare(strict_types=1);

namespace App\Service\External\PositionStack;

use App\DTO\Address;
use App\Exceptions\AddressNotFoundException;
use App\Service\External\MapClientInterface;
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
        protected HttpClientInterface $positionStackClient,
        protected $accessKey
    ) {
    }

    /**
     * @param Address $address
     * @return Address
     * @throws AddressNotFoundException
     */
    public function resolveAddressInfo(Address $address): Address
    {
        try {
            $response = $this->positionStackClient->request('GET', '/v1/forward', [
                'query' => [
                    'access_key' => $this->accessKey,
                    'query' => $address->getAddress(),
                    'limit' => 1,
                ]
            ]);

            $data = $response->toArray();

            if (empty($data['data'])) {
                throw new AddressNotFoundException(message: "Could not find results for {$address->getName()}");
            }

            return $this->hydrateAddressObject($address, $data['data']);
        } catch (
            ServerExceptionInterface |
            ClientExceptionInterface |
            TransportExceptionInterface |
            RedirectionExceptionInterface |
            DecodingExceptionInterface $exception
        ) {
            $message = sprintf(
                'Could not fetch forward geo information for %s. Message: %s',
                $address->getName(),
                $exception->getMessage()
            );

            $this->logger->critical(message: $message, context: $exception->getTrace());

            throw new AddressNotFoundException(message: $message);
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
