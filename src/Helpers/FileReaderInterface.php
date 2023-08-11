<?php

namespace App\Helpers;

interface FileReaderInterface
{
    public function make(string $filePath): JsonReader;

    public function exists(): bool;

    public function toJson(): string;

    public function toArray(): array;
}
