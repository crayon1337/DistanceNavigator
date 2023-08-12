<?php

namespace App\Service\External\MapClient;

use App\DTO\Address;
use App\Exceptions\AddressNotFoundException;

interface MapClientInterface
{
    /**
     * Fetches information about Geo location from a configured API.
     *
     * @param QueryInterface $query
     * @return MapCollection
     * @throws AddressNotFoundException
     */
    public function fetchGeoInformation(QueryInterface $query): MapCollection;
}
