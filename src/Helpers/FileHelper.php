<?php

namespace App\Helpers;

class FileHelper
{
    public static function exportDistances(string $fileName, array $distances): void
    {
        $fp = fopen('distances.csv', 'w');
        $distances = self::formatDistances($distances);
        $distances = self::appendHeaders($distances);

        foreach ($distances as $index => $distance) {
            fputcsv($fp, $distance, ',');
        }

        fclose($fp);
    }

    private static function formatDistances(array $distances)
    {
        $data = [];

        foreach ($distances as $index => $distance) {
            $data[] = [
                'id' => $index + 1,
                'from' => $distance['from'],
                'to' => $distance['to'],
                'distance' => $distance['distance_label']
            ];
        }

        return $data;
    }

    private static function appendHeaders(array $distances)
    {
        $header = [
            'SortNumber',
            'From',
            'To',
            'Distance'
        ];

        array_unshift($distances, $header);

        return $distances;
    }
}
