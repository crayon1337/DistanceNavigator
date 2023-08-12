<?php

namespace App\Service;

interface FileWriterInterface
{
    /**
     * Writes an array of data to a file
     *
     * @param string $fileName
     * @param array $data
     * @param array $headers
     * @return void
     */
    public function write(string $fileName, array $data, array $headers): void;
}
