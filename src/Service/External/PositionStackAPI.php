<?php

namespace App\Service\External;

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

    public function getForward(string $query)
    {
        try {
            $response = $this->httpClient->request('GET', 'http://api.positionstack.com/v1/forward', [
                'query' => [
                    'access_key' => $this->accessKey,
                    'query' => $query
                ]
            ]);

            return $response->toArray();
        } catch (ServerException | ClientException $exception) {
            $this->logger->critical(sprintf('Could not fetch forward geo information to %s', $query), $exception->getTrace());

            return [];
        }
    }
}
