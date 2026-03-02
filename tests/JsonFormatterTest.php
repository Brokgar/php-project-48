<?php

namespace Tests;

use Hexlet\Gendiff\Formatters\JsonFormatter;
use Hexlet\Gendiff\Utils\ArrayComparator;
use PHPUnit\Framework\TestCase;

class JsonFormatterTest extends TestCase
{
    /**
     * Тест простого JSON вывода.
     */
    public function testFormatSimpleDiff(): void
    {
        $diff = [
            [
                'key' => 'name',
                'type' => ArrayComparator::CHANGED,
                'oldValue' => 'old',
                'newValue' => 'new',
            ],
        ];

        $result = JsonFormatter::format($diff);
        $expected = <<<'JSON'
[
    {
        "key": "name",
        "type": "changed",
        "oldValue": "old",
        "newValue": "new"
    }
]
JSON;

        $this->assertSame($expected, $result);
    }

    /**
     * Тест JSON вывода с вложенными структурами.
     */
    public function testFormatNestedDiff(): void
    {
        $diff = [
            [
                'key' => 'nested',
                'type' => ArrayComparator::NESTED,
                'children' => [
                    [
                        'key' => 'key',
                        'type' => ArrayComparator::ADDED,
                        'value' => 'value',
                    ],
                ],
            ],
        ];

        $result = JsonFormatter::format($diff);
        $decoded = json_decode($result, true);

        $this->assertIsArray($decoded);
        $this->assertCount(1, $decoded);
        $this->assertSame('nested', $decoded[0]['key']);
        $this->assertSame('nested', $decoded[0]['key']);
        $this->assertArrayHasKey('children', $decoded[0]);
    }

    /**
     * Тест JSON вывода с unicode.
     */
    public function testFormatWithUnicode(): void
    {
        $diff = [
            [
                'key' => 'name',
                'type' => ArrayComparator::CHANGED,
                'oldValue' => 'Привет',
                'newValue' => 'Мир',
            ],
        ];

        $result = JsonFormatter::format($diff);

        $this->assertStringContainsString('Привет', $result);
        $this->assertStringContainsString('Мир', $result);
    }

    /**
     * Тест пустого diff.
     */
    public function testFormatEmptyDiff(): void
    {
        $result = JsonFormatter::format([]);

        $this->assertSame('[]', $result);
    }
}
