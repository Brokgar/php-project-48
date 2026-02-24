<?php

use PHPUnit\Framework\TestCase;
use Hexlet\Gendiff\Gendiff;
use function Differ\Differ\genDiff;

class GendiffTest extends TestCase
{
    private string $file1;
    private string $file2;

    protected function setUp(): void
    {
        $this->file1 = __DIR__ . '/fixtures/file1.json';
        $this->file2 = __DIR__ . '/fixtures/file2.json';
    }

    public function testCompareFilesPlainFormatViaClass(): void
    {
        $result = Gendiff::compareFiles($this->file1, $this->file2, ['format' => 'plain']);

        $expectedLines = [
            "Property 'verbose' was added with value: 1",
            "Property 'proxy' was removed (was: 123.234.53.22)",
            "Property 'follow' was removed (was: )",
            "Property 'timeout' changed from '50' to '20'",
        ];

        $expected = implode("\n", $expectedLines) . "\n";

        $this->assertSame($expected, $result);
    }

    public function testCompareFilesStylishFormatViaFunction(): void
    {
        $result = genDiff($this->file1, $this->file2, ['format' => 'stylish']);

        $expectedLines = [
            '{',
            '  - follow: false',
            '    host: hexlet.io',
            '  - proxy: 123.234.53.22',
            '  ~ timeout: 50 → 20',
            '  + verbose: true',
            '}',
        ];

        $expected = implode("\n", $expectedLines) . "\n";

        $this->assertSame($expected, $result);
    }

    public function testCompareFilesJsonFormat(): void
    {
        $json = genDiff($this->file1, $this->file2, ['format' => 'json']);
        $decoded = json_decode($json, true);

        $expected = [
            'added' => [
                'verbose' => true,
            ],
            'removed' => [
                'proxy' => '123.234.53.22',
                'follow' => false,
            ],
            'changed' => [
                'timeout' => [
                    'old' => 50,
                    'new' => 20,
                ],
            ],
            'unchanged' => [
                'host' => 'hexlet.io',
            ],
        ];

        $this->assertSame($expected, $decoded);
    }
}

