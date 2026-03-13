<?php

namespace Tests;

use Hexlet\Gendiff\Formatters\JsonFormatter;
use Hexlet\Gendiff\Utils\DiffTreeBuilder;
use PHPUnit\Framework\TestCase;

class JsonFormatterTest extends TestCase
{
    private JsonFormatter $formatter;

    private function normalizeLineEndings(string $value): string
    {
        return str_replace("\r\n", "\n", $value);
    }

    protected function setUp(): void
    {
        $this->formatter = new JsonFormatter();
    }

    public function testFormatSimpleDiff(): void
    {
        $diff = [[
            'key' => 'name',
            'type' => DiffTreeBuilder::CHANGED,
            'oldValue' => 'old',
            'newValue' => 'new',
        ]];

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
        $this->assertSame(
            $this->normalizeLineEndings($expected),
            $this->normalizeLineEndings($this->formatter->format($diff))
        );
    }

    public function testFormatNestedDiff(): void
    {
        $diff = [[
            'key' => 'nested',
            'type' => DiffTreeBuilder::NESTED,
            'children' => [[
                'key' => 'key',
                'type' => DiffTreeBuilder::ADDED,
                'value' => 'value',
            ]],
        ]];

        $decoded = json_decode($this->formatter->format($diff), true);

        $this->assertIsArray($decoded);
        $this->assertCount(1, $decoded);
        $this->assertSame('nested', $decoded[0]['key']);
        $this->assertArrayHasKey('children', $decoded[0]);
    }

    public function testFormatWithUnicode(): void
    {
        $diff = [[
            'key' => 'name',
            'type' => DiffTreeBuilder::CHANGED,
            'oldValue' => 'Р СџРЎР‚Р С‘Р Р†Р ВµРЎвЂљ',
            'newValue' => 'Р СљР С‘РЎР‚',
        ]];

        $result = $this->formatter->format($diff);

        $this->assertStringContainsString('Р СџРЎР‚Р С‘Р Р†Р ВµРЎвЂљ', $result);
        $this->assertStringContainsString('Р СљР С‘РЎР‚', $result);
    }

    public function testFormatEmptyDiff(): void
    {
        $this->assertSame('[]', $this->formatter->format([]));
    }
}
