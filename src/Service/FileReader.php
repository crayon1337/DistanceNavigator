<?php

namespace App\Service;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileReader implements FileReaderInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private string $filePath;
    private string $data;

    public function make(string $filePath): FileReader
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

    public function content(): string
    {
        return $this->data;
    }

    public function toArray(): ?array
    {
        return json_decode(json: $this->data, associative: true);
    }
}
