<?php

declare(strict_types=1);

namespace App\Service\External\MapClient\PositionStack;

use App\Service\External\MapClient\MapAddress;
use App\Service\External\MapClient\MapCollection;

final class PositionStackCollection implements MapCollection
{
    /**
     * @param array $data
     */
    public function __construct(
        public array $data
    ) {
    }

    /**
     * Returns an array of PositionStackAddress objects.
     *
     * @return MapAddress[]
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Find the first MapAddress in the data array.
     *
     * @return MapAddress
     */
    public function first(): MapAddress
    {
        return reset($this->data);
    }
}
