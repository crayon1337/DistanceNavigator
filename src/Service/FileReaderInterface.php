<?php

namespace App\Service;

use App\Exceptions\InvalidJsonException;

interface FileReaderInterface
{
    /**
     * @param string $filePath
     * @return FileReaderService
     */
    public function read(string $filePath): FileReaderService;

    /**
     * @return string
     */
    public function content(): string;

    /**
     * @return array
     * @throws InvalidJsonException
     */
    public function toArray(): array;
}
