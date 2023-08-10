<?php

namespace App\Service\External;

use App\DTO\Address;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * This class will be used to determine information about geo locations.
 * 
 * @method getForward(string $query)
 */
class PositionStackAPI implements LoggerAwareInterface
{
    public function __construct(
        protected HttpClientInterface $httpClient,
        protected $accessKey,
        protected LoggerInterface $logger
    ) {
        $this->httpClient = $httpClient;
        $this->accessKey = $accessKey;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getForward(Address $address): ?Address
    {
        try {
            $response = $this->httpClient->request('GET', 'http://api.positionstack.com/v1/forward', [
                'query' => [
                    'access_key' => $this->accessKey,
                    'query' => $address->getTitle()
                ]
            ]);

            $data = $response->toArray();

            $this->sortByConfidence($data);

            if (empty($data['data'])) {
                $this->logger->critical(sprintf('Could not find results for %s', $address->getTitle()));
                return null;
            }

            return $this->hydrateAddressObject($address, $data);
        } catch (ServerException | ClientException $exception) {
            $this->logger->critical(
                sprintf(
                    'Could not fetch forward geo information to %s. Message: %s',
                    $address->getTitle(),
                    $exception->getMessage()
                ), $exception->getTrace()
            );

            return null;
        }
    }

    private function sortByConfidence(array $data)
    {
        usort($data, function ($a, $b) {
            if ($a['data']['confidence'] == $b['confidence']) {
                return 0;
            }
            return ($a['confidence'] > $b['confidence']) ? -1 : 1;
        });
    }

    private function hydrateAddressObject(Address $address, array $response): Address
    {
        $address->setLabel($response['data'][0]['label']);
        $address->setLatitude($response['data'][0]['latitude']);
        $address->setLongitude($response['data'][0]['longitude']);
        $address->setData($response['data'][0]);

        return $address;
    }
}
