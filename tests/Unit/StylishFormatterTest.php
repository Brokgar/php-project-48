<?php

namespace Tests;

use Hexlet\Gendiff\Formatters\StylishFormatter;
use Hexlet\Gendiff\Utils\DiffTreeBuilder;
use PHPUnit\Framework\TestCase;

class StylishFormatterTest extends TestCase
{
    private StylishFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new StylishFormatter();
    }

    public function testRenderStylishSimpleDiff(): void
    {
        $diff = [[
            'key' => 'name',
            'type' => DiffTreeBuilder::CHANGED,
            'oldValue' => 'old',
            'newValue' => 'new',
        ]];

        $result = $this->formatter->renderStylish($diff);

        $this->assertStringContainsString('{', $result);
        $this->assertStringContainsString('}', $result);
        $this->assertStringContainsString('- name: old', $result);
        $this->assertStringContainsString('+ name: new', $result);
    }

    public function testRenderStylishAddedKey(): void
    {
        $diff = [[
            'key' => 'newKey',
            'type' => DiffTreeBuilder::ADDED,
            'value' => 'newValue',
        ]];

        $this->assertStringContainsString('+ newKey: newValue', $this->formatter->renderStylish($diff));
    }

    public function testRenderStylishRemovedKey(): void
    {
        $diff = [[
            'key' => 'oldKey',
            'type' => DiffTreeBuilder::REMOVED,
            'value' => 'oldValue',
        ]];

        $this->assertStringContainsString('- oldKey: oldValue', $this->formatter->renderStylish($diff));
    }

    public function testRenderStylishUnchangedKey(): void
    {
        $diff = [[
            'key' => 'sameKey',
            'type' => DiffTreeBuilder::UNCHANGED,
            'value' => 'sameValue',
        ]];

        $this->assertStringContainsString('sameKey: sameValue', $this->formatter->renderStylish($diff));
    }

    public function testRenderStylishNestedStructures(): void
    {
        $diff = [[
            'key' => 'nested',
            'type' => DiffTreeBuilder::NESTED,
            'children' => [[
                'key' => 'innerKey',
                'type' => DiffTreeBuilder::ADDED,
                'value' => 'innerValue',
            ]],
        ]];

        $result = $this->formatter->renderStylish($diff);

        $this->assertStringContainsString('{', $result);
        $this->assertStringContainsString('nested:', $result);
        $this->assertStringContainsString('+ innerKey: innerValue', $result);
    }

    public function testRenderStylishWithNullValue(): void
    {
        $diff = [[
            'key' => 'nullable',
            'type' => DiffTreeBuilder::CHANGED,
            'oldValue' => null,
            'newValue' => null,
        ]];

        $result = $this->formatter->renderStylish($diff);

        $this->assertStringContainsString('- nullable: null', $result);
        $this->assertStringContainsString('+ nullable: null', $result);
    }

    public function testRenderStylishWithBooleanValue(): void
    {
        $diff = [[
            'key' => 'enabled',
            'type' => DiffTreeBuilder::CHANGED,
            'oldValue' => true,
            'newValue' => false,
        ]];

        $result = $this->formatter->renderStylish($diff);

        $this->assertStringContainsString('- enabled: true', $result);
        $this->assertStringContainsString('+ enabled: false', $result);
    }

    public function testRenderStylishEmptyDiff(): void
    {
        $this->assertSame('{' . PHP_EOL . '}', $this->formatter->renderStylish([]));
    }

    public function testRenderStylishIndentation(): void
    {
        $diff = [[
            'key' => 'level1',
            'type' => DiffTreeBuilder::NESTED,
            'children' => [[
                'key' => 'level2',
                'type' => DiffTreeBuilder::NESTED,
                'children' => [[
                    'key' => 'key',
                    'type' => DiffTreeBuilder::UNCHANGED,
                    'value' => 'value',
                ]],
            ]],
        ]];

        $lines = explode("\n", $this->formatter->renderStylish($diff));
        $this->assertGreaterThan(3, count($lines));
    }

    public function testRenderStylishMultipleChanges(): void
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

        $result = $this->formatter->renderStylish($diff);

        $this->assertStringContainsString('+ added: new', $result);
        $this->assertStringContainsString('- removed: old', $result);
        $this->assertStringContainsString('- changed: from', $result);
        $this->assertStringContainsString('+ changed: to', $result);
    }
}
