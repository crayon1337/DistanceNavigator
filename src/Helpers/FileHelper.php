<?php

namespace App\Helpers;

class FileHelper
{
    public static function export(string $fileName, array $data, array $headers): void
    {
        $fp = fopen($fileName, 'w');
        $data = self::prependHeaders($data, $headers);

        foreach ($data as $row) {
            fputcsv($fp, $row, ',');
        }

        fclose($fp);
    }

    private static function prependHeaders(array $data, array $headers)
    {
        array_unshift($data, $headers);

        return $data;
    }
}
