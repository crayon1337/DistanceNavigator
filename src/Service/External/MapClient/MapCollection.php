<?php

namespace App\Service\External\MapClient;

use App\Service\External\MapClient\PositionStack\PositionStackAddress;

interface MapCollection
{
    /**
     * Returns an array of MapAddress objects.
     *
     * @return MapAddress[]
     */
    public function all(): array;

    /**
     * Find the first MapAddress in the data array.
     *
     * @return MapAddress
     */
    public function first(): MapAddress;
}
