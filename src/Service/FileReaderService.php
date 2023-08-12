<?php

declare(strict_types=1);

namespace App\Service;

use App\Exceptions\InvalidJsonException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

final class FileReaderService implements FileReaderInterface
{
    private string $data = '{}';

    /**
     * @param string $filePath
     * @return FileReaderService
     */
    public function read(string $filePath): FileReaderService
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
