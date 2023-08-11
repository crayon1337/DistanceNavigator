<?php

namespace App\Helpers;

class FileHelper
{
    public static function export(string $fileName, array $data): void
    {
        $fp = fopen($fileName, 'w');

        foreach ($data as $row) {
            fputcsv($fp, $row, ',');
        }

        fclose($fp);
    }
}
