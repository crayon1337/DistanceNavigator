<?php

namespace App\Helpers;

class Sorter
{
    public static function make(array $data, string $key, string $direction = 'ASC'): array
    {
        usort($data, function ($a, $b) use ($key, $direction) {
            if (!isset($a[$key]) || !isset($b[$key])) {
                return 0;
            }

            if ($direction === 'DESC') {
                return $a[$key] > $b[$key] ? -1 : 1;
            }

            return $b[$key] < $a[$key] ? 1 : -1;
        });

        return $data;
    }
}
