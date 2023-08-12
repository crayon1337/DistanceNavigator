<?php

declare(strict_types=1);

namespace App\Service\External\MapClient\PositionStack;

use App\Service\External\MapClient\QueryInterface;

final class Query implements QueryInterface
{
    public function __construct(
        private string $id,
        private string $address,
        private ?string $accessKey = null,
        private readonly int $limit = 1
    ) {
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @param string $accessKey
     */
    public function setAccessKey(string $accessKey): void
    {
        $this->accessKey = $accessKey;
    }

    public function informationPayload(): array
    {
        return [
            'access_key' => $this->accessKey,
            'query' => $this->address,
            'limit' => $this->limit,
        ];
    }
}
