<?php

namespace App\Service;

use App\DTO\Address;

interface GeolocationInterface
{
    public function getDistances(Address $destinationAddress, array $addresses): array;
}
