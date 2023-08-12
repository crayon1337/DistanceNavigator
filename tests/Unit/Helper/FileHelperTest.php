<?php

declare(strict_types=1);

namespace App\Tests\Unit\Helper;

use App\Helper\FileHelper;
use PHPUnit\Framework\TestCase;

class FileHelperTest extends TestCase
{
    public function testCsvFileCanBeExported(): void
    {
        // Setup
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
        FileHelper::export('test.csv', $data, $headers);

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
