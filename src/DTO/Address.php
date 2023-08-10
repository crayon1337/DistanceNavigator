<?php

namespace App\DTO;

class Address
{
    public function __construct(
        private string $title,
        private ?float $latitude = null,
        private ?float $longitude = null,
        private ?array $data = []
    ) {

    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
