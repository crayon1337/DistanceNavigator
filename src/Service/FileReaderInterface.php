<?php

namespace App\Service;

use App\Exceptions\InvalidJsonException;

interface FileReaderInterface
{
    /**
     * @param string $filePath
     * @return FileService
     */
    public function make(string $filePath): FileService;

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
