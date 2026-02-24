<?php

use PHPUnit\Framework\TestCase;
use Hexlet\Gendiff\Generator;

class GeneratorTest extends TestCase
{
    private array $data1;
    private array $data2;

    protected function setUp(): void
    {
        $this->data1 = [
            'host' => 'hexlet.io',
            'timeout' => 50,
            'proxy' => '123.234.53.22',
            'follow' => false,
        ];

        $this->data2 = [
            'timeout' => 20,
            'verbose' => true,
            'host' => 'hexlet.io',
        ];
    }

    public function testGeneratePlainDiff(): void
    {
        $expectedLines = [
            "Property 'verbose' was added with value: 1",
            "Property 'proxy' was removed (was: 123.234.53.22)",
            "Property 'follow' was removed (was: )",
            "Property 'timeout' changed from '50' to '20'",
        ];

        $expected = implode("\n", $expectedLines) . "\n";

        $actual = Generator::generateDiff($this->data1, $this->data2, 'plain');

        $this->assertSame($expected, $actual);
    }

    public function testGenerateStylishDiff(): void
    {
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

        $actual = Generator::generateDiff($this->data1, $this->data2, 'stylish');

        $this->assertSame($expected, $actual);
    }

    public function testGenerateJsonDiff(): void
    {
        $json = Generator::generateDiff($this->data1, $this->data2, 'json');
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

    public function testUnknownFormatThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Generator::generateDiff($this->data1, $this->data2, 'unknown');
    }
}

