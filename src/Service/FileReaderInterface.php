<?php

namespace App\Service;

interface FileReaderInterface
{
    public function make(string $filePath): FileReader;

    public function exists(): bool;

    public function content(): string;

    public function toArray(): ?array;
}
