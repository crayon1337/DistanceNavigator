<?php

namespace App\Service;

use App\DTO\Address;
use App\Exceptions\AddressNotFoundException;

interface LocationInterface
{
    /**
     * Calculate the distance between given array of addresses to a given destination.
     *
     * @param Address $destinationAddress
     * @param Address[] $addresses
     * @return array
     * @throws AddressNotFoundException
     */
    public function getDistances(Address $destinationAddress, array $addresses): array;
}
