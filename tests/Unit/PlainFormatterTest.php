<?php

namespace Tests;

use Hexlet\Gendiff\Formatters\PlainFormatter;
use Hexlet\Gendiff\Utils\DiffTreeBuilder;
use PHPUnit\Framework\TestCase;

class PlainFormatterTest extends TestCase
{
    private PlainFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new PlainFormatter();
    }

    public function testFormatAddedProperty(): void
    {
        $diff = [[
            'key' => 'name',
            'type' => DiffTreeBuilder::ADDED,
            'value' => 'new value',
        ]];

        $this->assertSame("Property 'name' was added with value: 'new value'", $this->formatter->format($diff));
    }

    public function testFormatRemovedProperty(): void
    {
        $diff = [[
            'key' => 'oldKey',
            'type' => DiffTreeBuilder::REMOVED,
            'value' => 'old value',
        ]];

        $this->assertSame("Property 'oldKey' was removed", $this->formatter->format($diff));
    }

    public function testFormatChangedProperty(): void
    {
        $diff = [[
            'key' => 'status',
            'type' => DiffTreeBuilder::CHANGED,
            'oldValue' => 'active',
            'newValue' => 'inactive',
        ]];

        $this->assertSame(
            "Property 'status' was updated. From 'active' to 'inactive'",
            $this->formatter->format($diff)
        );
    }

    public function testFormatNestedProperties(): void
    {
        $diff = [[
            'key' => 'database',
            'type' => DiffTreeBuilder::NESTED,
            'children' => [[
                'key' => 'host',
                'type' => DiffTreeBuilder::CHANGED,
                'oldValue' => 'localhost',
                'newValue' => '127.0.0.1',
            ]],
        ]];

        $this->assertSame(
            "Property 'database.host' was updated. From 'localhost' to '127.0.0.1'",
            $this->formatter->format($diff)
        );
    }

    public function testFormatDeeplyNestedProperties(): void
    {
        $diff = [[
            'key' => 'level1',
            'type' => DiffTreeBuilder::NESTED,
            'children' => [[
                'key' => 'level2',
                'type' => DiffTreeBuilder::NESTED,
                'children' => [[
                    'key' => 'key',
                    'type' => DiffTreeBuilder::ADDED,
                    'value' => 'value',
                ]],
            ]],
        ]];

        $this->assertSame(
            "Property 'level1.level2.key' was added with value: 'value'",
            $this->formatter->format($diff)
        );
    }

    public function testFormatWithNullValue(): void
    {
        $diff = [[
            'key' => 'nullable',
            'type' => DiffTreeBuilder::CHANGED,
            'oldValue' => null,
            'newValue' => 'not null',
        ]];

        $this->assertSame(
            "Property 'nullable' was updated. From null to 'not null'",
            $this->formatter->format($diff)
        );
    }

    public function testFormatWithBooleanValue(): void
    {
        $diff = [[
            'key' => 'enabled',
            'type' => DiffTreeBuilder::CHANGED,
            'oldValue' => true,
            'newValue' => false,
        ]];

        $this->assertSame(
            "Property 'enabled' was updated. From true to false",
            $this->formatter->format($diff)
        );
    }

    public function testFormatWithNumericValue(): void
    {
        $diff = [[
            'key' => 'count',
            'type' => DiffTreeBuilder::CHANGED,
            'oldValue' => 10,
            'newValue' => 20,
        ]];

        $this->assertSame("Property 'count' was updated. From 10 to 20", $this->formatter->format($diff));
    }

    public function testFormatWithComplexValue(): void
    {
        $diff = [[
            'key' => 'settings',
            'type' => DiffTreeBuilder::ADDED,
            'value' => ['key1' => 'value1', 'key2' => 'value2'],
        ]];

        $this->assertSame(
            "Property 'settings' was added with value: [complex value]",
            $this->formatter->format($diff)
        );
    }

    public function testFormatEmptyDiff(): void
    {
        $this->assertSame('', $this->formatter->format([]));
    }

    public function testFormatMultipleChanges(): void
    {
        $diff = [
            [
                'key' => 'added',
                'type' => DiffTreeBuilder::ADDED,
                'value' => 'new',
            ],
            [
                'key' => 'removed',
                'type' => DiffTreeBuilder::REMOVED,
                'value' => 'old',
            ],
            [
                'key' => 'changed',
                'type' => DiffTreeBuilder::CHANGED,
                'oldValue' => 'from',
                'newValue' => 'to',
            ],
        ];

        $expected = <<<'EXPECTED'
Property 'added' was added with value: 'new'
Property 'removed' was removed
Property 'changed' was updated. From 'from' to 'to'
EXPECTED;

        $this->assertSame($expected, $this->formatter->format($diff));
    }
}
