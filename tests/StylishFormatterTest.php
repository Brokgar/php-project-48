<?php

namespace Tests;

use Hexlet\Gendiff\Formatters\StylishFormatter;
use Hexlet\Gendiff\Utils\ArrayComparator;
use PHPUnit\Framework\TestCase;

class StylishFormatterTest extends TestCase
{
    private StylishFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new StylishFormatter();
    }

    /**
     * Тест простого stylish вывода.
     */
    public function testRenderStylishSimpleDiff(): void
    {
        $diff = [
            [
                'key' => 'name',
                'type' => ArrayComparator::CHANGED,
                'oldValue' => 'old',
                'newValue' => 'new',
            ],
        ];

        $result = $this->formatter->renderStylish($diff);

        $this->assertStringContainsString('{', $result);
        $this->assertStringContainsString('}', $result);
        $this->assertStringContainsString('- name: old', $result);
        $this->assertStringContainsString('+ name: new', $result);
    }

    /**
     * Тест добавления ключа.
     */
    public function testRenderStylishAddedKey(): void
    {
        $diff = [
            [
                'key' => 'newKey',
                'type' => ArrayComparator::ADDED,
                'value' => 'newValue',
            ],
        ];

        $result = $this->formatter->renderStylish($diff);

        $this->assertStringContainsString('+ newKey: newValue', $result);
    }

    /**
     * Тест удаления ключа.
     */
    public function testRenderStylishRemovedKey(): void
    {
        $diff = [
            [
                'key' => 'oldKey',
                'type' => ArrayComparator::REMOVED,
                'value' => 'oldValue',
            ],
        ];

        $result = $this->formatter->renderStylish($diff);

        $this->assertStringContainsString('- oldKey: oldValue', $result);
    }

    /**
     * Тест неизменённых ключей.
     */
    public function testRenderStylishUnchangedKey(): void
    {
        $diff = [
            [
                'key' => 'sameKey',
                'type' => ArrayComparator::UNCHANGED,
                'value' => 'sameValue',
            ],
        ];

        $result = $this->formatter->renderStylish($diff);

        $this->assertStringContainsString('sameKey: sameValue', $result);
    }

    /**
     * Тест вложенных структур.
     */
    public function testRenderStylishNestedStructures(): void
    {
        $diff = [
            [
                'key' => 'nested',
                'type' => ArrayComparator::NESTED,
                'children' => [
                    [
                        'key' => 'innerKey',
                        'type' => ArrayComparator::ADDED,
                        'value' => 'innerValue',
                    ],
                ],
            ],
        ];

        $result = $this->formatter->renderStylish($diff);

        $this->assertStringContainsString('{', $result);
        $this->assertStringContainsString('nested:', $result);
        $this->assertStringContainsString('+ innerKey: innerValue', $result);
    }

    /**
     * Тест с null значением.
     */
    public function testRenderStylishWithNullValue(): void
    {
        $diff = [
            [
                'key' => 'nullable',
                'type' => ArrayComparator::CHANGED,
                'oldValue' => null,
                'newValue' => null,
            ],
        ];

        $result = $this->formatter->renderStylish($diff);

        $this->assertStringContainsString('- nullable: null', $result);
        $this->assertStringContainsString('+ nullable: null', $result);
    }

    /**
     * Тест с boolean значением.
     */
    public function testRenderStylishWithBooleanValue(): void
    {
        $diff = [
            [
                'key' => 'enabled',
                'type' => ArrayComparator::CHANGED,
                'oldValue' => true,
                'newValue' => false,
            ],
        ];

        $result = $this->formatter->renderStylish($diff);

        $this->assertStringContainsString('- enabled: true', $result);
        $this->assertStringContainsString('+ enabled: false', $result);
    }

    /**
     * Тест пустого diff.
     */
    public function testRenderStylishEmptyDiff(): void
    {
        $result = $this->formatter->renderStylish([]);

        $this->assertSame('{' . PHP_EOL . '}', $result);
    }

    /**
     * Тест отступов для вложенных структур.
     */
    public function testRenderStylishIndentation(): void
    {
        $diff = [
            [
                'key' => 'level1',
                'type' => ArrayComparator::NESTED,
                'children' => [
                    [
                        'key' => 'level2',
                        'type' => ArrayComparator::NESTED,
                        'children' => [
                            [
                                'key' => 'key',
                                'type' => ArrayComparator::UNCHANGED,
                                'value' => 'value',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->formatter->renderStylish($diff);

        // Проверяем, что есть правильные отступы
        $lines = explode("\n", $result);
        $this->assertGreaterThan(3, count($lines));
    }

    /**
     * Тест нескольких изменений.
     */
    public function testRenderStylishMultipleChanges(): void
    {
        $diff = [
            [
                'key' => 'added',
                'type' => ArrayComparator::ADDED,
                'value' => 'new',
            ],
            [
                'key' => 'removed',
                'type' => ArrayComparator::REMOVED,
                'value' => 'old',
            ],
            [
                'key' => 'changed',
                'type' => ArrayComparator::CHANGED,
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

