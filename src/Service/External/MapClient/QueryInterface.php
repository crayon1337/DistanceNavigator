<?php

namespace App\Service\External\MapClient;

interface QueryInterface
{
    /**
     * Sets a unique identifier to identify query's instance.
     *
     * @param string $id
     */
    public function setId(string $id): void;

    /**
     * The unique identifier to identify query's instance.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * @param string $address
     */
    public function setAddress(string $address): void;

    /**
     * @param string $accessKey
     */
    public function setAccessKey(string $accessKey): void;

    /**
     * The payload used to query the MapAPI to return information about geolocation.
     *
     * @return array
     */
    public function informationPayload(): array;
}
