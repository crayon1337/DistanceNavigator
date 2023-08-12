<?php

declare(strict_types=1);

namespace App\Service\External\MapClient\PositionStack;

use App\Service\External\MapClient\MapAddress;

final class PositionStackAddress implements MapAddress
{
    public function __construct(
        public string $id,
        public float $latitude,
        public float $longitude,
        public ?string $type = null,
        public ?string $name = null,
        public ?string $number = null,
        public ?string $postal_code = null,
        public ?string $street = null,
        public ?float $confidence = null,
        public ?string $region = null,
        public ?string $region_code = null,
        public ?string $county = null,
        public ?string $locality = null,
        public ?string $administrative_area = null,
        public ?string $neighbourhood = null,
        public ?string $country = null,
        public ?string $country_code = null,
        public ?string $continent = null,
        public ?string $label = null
    ) {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }
}
