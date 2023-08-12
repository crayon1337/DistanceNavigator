<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\CsvWriterService;
use PHPUnit\Framework\TestCase;

class CsvWriterServiceTest extends TestCase
{
    public function testCsvFileCanBeExported(): void
    {
        // Setup
        $fileWriter = new CsvWriterService();
        $data = [
            [
                1,
                'Foo bar',
                '20-05-2023',
            ],
            [
                2,
                'Bar Baz',
                '25-05-2023',
            ],
        ];
        $headers = [
            'ID',
            'Name',
            'Date'
        ];

        // Act
        $fileWriter->write('test.csv', $data, $headers);

        // Assert
        $this->assertFileExists('test.csv');
        $this->assertStringEqualsFile(
            expectedFile: 'test.csv',
            actualString:
            'ID,Name,Date
1,"Foo bar",20-05-2023
2,"Bar Baz",25-05-2023
'
        );
    }
}
