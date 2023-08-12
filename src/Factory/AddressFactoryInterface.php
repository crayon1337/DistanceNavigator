<?php

namespace App\Factory;

use App\DTO\Address;
use App\Exceptions\InvalidDataException;

interface AddressFactoryInterface
{
    /**
     * @param array $data
     * @return Address
     * @throws InvalidDataException
     */
    public function make(array $data): Address;

    /**
     * @param array $data
     * @return array
     * @throws InvalidDataException
     */
    public function resolveAddresses(array $data): array;

    /**
     * @param array $data
     * @return bool
     */
    public function validate(array $data): bool;
}
