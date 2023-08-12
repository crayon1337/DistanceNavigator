<?php

namespace App\Service\External\MapClient;

interface MapAddress
{
    /**
     * The unique identifier of the address.
     * usually being set from internal entities.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Finds float of map address's latitude collected by the API
     *
     * @return float
     */
    public function getLatitude(): float;

    /**
     * Float of map address's longitude collected by the API
     *
     * @return float
     */
    public function getLongitude(): float;

    /**
     * The label of the address which is collected by the API.
     *
     * @return string|null
     */
    public function getLabel(): ?string;
}
