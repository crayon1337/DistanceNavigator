<?php

namespace App\Helpers;

use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class JsonReader implements FileReaderInterface
{
    use LoggerAwareTrait;

    private string $filePath;
    private string $data;

    public function make(string $filePath): JsonReader
    {
        $this->filePath = $filePath;

        try {
            $this->data = file_get_contents($filePath);
        } catch (IOExceptionInterface $exception) {
            $this->logger->critical(
                message: sprintf(
                    format: 'An error occurred while reading the JSON file: %s',
                    values: $exception->getMessage()
                ),
                context: $exception->getTrace()
            );
        }

        return $this;
    }

    public function exists(): bool
    {
        $fileSystem = new Filesystem();

        return $fileSystem->exists($this->filePath);
    }

    public function toJson(): string
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return json_decode(json: $this->data, associative: true);
    }
}
