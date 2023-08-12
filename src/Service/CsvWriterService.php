<?php

declare(strict_types=1);

namespace App\Service;

final class CsvWriterService implements FileWriterInterface
{
    /**
     * Writes an array of data to a CSV file
     *
     * @param string $fileName
     * @param array $data
     * @param array $headers
     * @return void
     */
    public function write(string $fileName, array $data, array $headers): void
    {
        $fp = fopen($fileName, 'w');

        $data = $this->prependHeaders($data, $headers);

        foreach ($data as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);
    }

    /**
     * Appends header to the first of given data array.
     *
     * @param array $data
     * @param array $headers
     * @return array
     */
    private function prependHeaders(array $data, array $headers): array
    {
        array_unshift($data, $headers);

        return $data;
    }
}
