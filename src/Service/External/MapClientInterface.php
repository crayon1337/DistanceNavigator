<?php

namespace App\Service\External;

use App\DTO\Address;
use App\Exceptions\AddressNotFoundException;

interface MapClientInterface
{
    /**
     * @param Address $address
     * @return Address
     * @throws AddressNotFoundException
     */
    public function resolveAddressInfo(Address $address): Address;
}
