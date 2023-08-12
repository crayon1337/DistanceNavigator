<?php

declare(strict_types=1);

namespace App\DTO;

class Address
{
    public function __construct(
        private readonly string $name,
        private readonly string $address
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }
}
