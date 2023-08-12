<?php

declare(strict_types=1);

namespace App\Service;

use App\Exceptions\InvalidJsonException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

class FileService implements FileReaderInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private string $data;

    /**
     * @param string $filePath
     * @return $this
     */
    public function make(string $filePath): FileService
    {
        $fileSystem = new Filesystem();

        if (!$fileSystem->exists($filePath)) {
            throw new FileNotFoundException("Could not find file $filePath");
        }

        $this->data = file_get_contents($filePath);

        return $this;
    }

    /**
     * @return string
     */
    public function content(): string
    {
        return $this->data;
    }

    /**
     * @return array
     * @throws InvalidJsonException
     */
    public function toArray(): array
    {
        $data = json_decode(json: $this->data, associative: true);

        if (is_null($data)) {
            throw new InvalidJsonException('Error decoding JSON');
        }

        if (!array_key_exists('destination', $data) || !array_key_exists('addresses', $data)) {
            throw new InvalidJsonException('Unable to find destination or addresses keys in the file you provided.');
        }

        return $data;
    }
}
