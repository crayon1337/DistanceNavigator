<?php

declare(strict_types=1);

namespace App\Tests\Unit\Helper;

use App\DTO\Address;
use App\Helper\LocationHelper;
use App\Service\External\MapClient\PositionStack\PositionStackAddress;
use PHPUnit\Framework\TestCase;

class LocationHelperTest extends TestCase
{
    /**
     * @test
     */
    public function testDistanceCanBeCalculatedCorrectlyAndTypeIsFloat(): void
    {
        // Setup
        $startPoint = $this->getAddress(
            name: 'Adchieve Rotterdam',
            address: 'Weena 505, 3013, The Netherlands',
            latitude: 51.92366,
            longitude: 4.471626
        );
        $destination = $this->getAddress(
            name: 'Adchieve HQ',
            address: "Sint Janssingel 92, 5211 DA 's-Hertogenbosch, The Netherlands",
            latitude: 51.6882,
            longitude: 5.298532
        );

        // Act
        $distance = LocationHelper::calculateDistance(startingPoint: $startPoint, destination: $destination);

        // Assert
        $this->assertIsFloat($distance);
        $this->assertEquals(62.58381988938476, $distance);
    }

    /**
     * @test
     */
    public function testDistanceLabelCanBeFormattedCorrectly(): void
    {
        // Setup
        $startPoint = $this->getAddress(
            name: 'Adchieve Rotterdam',
            address: 'Weena 505, 3013, The Netherlands',
            latitude: 51.92366,
            longitude: 4.471626
        );
        $destination = $this->getAddress(
            name: 'Adchieve HQ',
            address: "Sint Janssingel 92, 5211 DA 's-Hertogenbosch, The Netherlands",
            latitude: 51.6882,
            longitude: 5.298532
        );

        // Act
        $distance = LocationHelper::calculateDistance(startingPoint: $startPoint, destination: $destination);
        $label = LocationHelper::getDistanceLabel($distance);

        // Assert
        $this->assertIsString($label);
        $this->assertEquals('62.58 km', $label);
    }

    private function getAddress(string $name, string $address, float $latitude, float $longitude): PositionStackAddress
    {
        return new PositionStackAddress(
            id: $name,
            latitude: $latitude,
            longitude: $longitude,
            label: $address
        );
    }
}
