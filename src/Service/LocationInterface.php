<?php

namespace App\Service;

use App\DTO\Address;
use App\Exceptions\AddressNotFoundException;

interface LocationInterface
{
    /**
     * @param Address $destinationAddress
     * @param Address[] $addresses
     * @return array
     * @throws AddressNotFoundException
     */
    public function getDistances(Address $destinationAddress, array $addresses): array;
}
