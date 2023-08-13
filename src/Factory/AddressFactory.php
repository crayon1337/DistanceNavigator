<?php

namespace App\Factory;

use App\DTO\Address;
use App\Exceptions\InvalidDataException;

class AddressFactory implements AddressFactoryInterface
{
    /**
     * @param array $data
     * @return Address
     * @throws InvalidDataException
     */
    public function make(array $data): Address
    {
        if (!$this->validate($data)) {
            throw new InvalidDataException("Unable to resolve destination's name and address of the destination in your file");
        }

        return new Address(name: $data['name'], address: $data['address']);
    }

    /**
     * @param array $data
     * @return array
     */
    public function resolveAddresses(array $data): array
    {
        $addresses = [];

        foreach ($data as $address) {
            try {
                $addresses[] = $this->make($address);
            } catch (InvalidDataException) {
                // We need to skip this row. To see, if we are able to resolve some other addresses.
                continue;
            }
        }

        return $addresses;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function validate(array $data): bool
    {
        return array_key_exists('name', $data) && array_key_exists('address', $data);
    }
}
