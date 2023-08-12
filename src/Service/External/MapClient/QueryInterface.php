<?php

namespace App\Service\External\MapClient;

interface QueryInterface
{
    /**
     * @param string $id
     */
    public function setId(string $id): void;

    /**
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
     * @return array
     */
    public function all(): array;
}
