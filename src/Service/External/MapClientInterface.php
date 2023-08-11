<?php

namespace App\Service\External;

use App\DTO\Address;

interface MapClientInterface
{
    public function resolveAddressInfo(Address $address): ?Address;
}
