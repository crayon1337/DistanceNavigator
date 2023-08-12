<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Exceptions\InvalidJsonException;
use App\Service\FileReaderService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class FileReaderServiceTest extends TestCase
{
    /**
     * @test
     */
    public function testNotFoundFileExceptionIsBeingThrownWhenFileDoesNotExist(): void
    {
        // Setup
        $filePath = 'files/invalid-file-path.json';
        $fileReaderService = new FileReaderService();

        // Assert
        $this->expectException(FileNotFoundException::class);

        // Act
        $data = $fileReaderService->read($filePath);
    }

    /**
     * @test
     * @throws InvalidJsonException
     */
    public function testJsonFileCanBeOpenedAndContentParsedAsAnArray(): void
    {
        // Setup
        $filePath = 'files/addresses.json';
        $fileReaderService = new FileReaderService();

        // Act
        $data = $fileReaderService->read($filePath)->toArray();

        // Assert
        $this->assertNotNull($data);
        $this->assertNotNull($data['destination']);
        $this->assertNotNull($data['addresses']);
        $this->assertCount(2, $data);
        $this->assertCount(8, $data['addresses']);
    }

    /**
     * @test
     */
    public function testJsonFileCanBeOpenedAndJsonContentIsAccessible()
    {
        // Setup
        $filePath = 'files/addresses.json';
        $fileReaderService = new FileReaderService();

        // Act
        $content = $fileReaderService->read($filePath)->content();

        // Assert
        $this->assertNotNull($content);
        $this->assertIsString($content);
    }
}
