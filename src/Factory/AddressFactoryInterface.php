<?php

namespace App\Factory;

use App\DTO\Address;

interface AddressFactoryInterface
{
    public function make(array $data): ?Address;
}
