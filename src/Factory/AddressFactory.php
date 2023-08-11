<?php

namespace App\Factory;

use App\DTO\Address;

class AddressFactory implements AddressFactoryInterface
{
    public function make(array $data): ?Address
    {
        if (!$this->validateData($data)) {
            return null;
        }

        return new Address(name: $data['name'], address: $data['address']);
    }

    private function validateData(array $data): bool
    {
        return array_key_exists('name', $data) && array_key_exists('address', $data);
    }
}
