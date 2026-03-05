<?php

namespace Tests;

use Hexlet\Gendiff\Formatters\PlainFormatter;
use Hexlet\Gendiff\Utils\ArrayComparator;
use PHPUnit\Framework\TestCase;

class PlainFormatterTest extends TestCase
{
    /**
     * Тест добавления свойства.
     */
    public function testFormatAddedProperty(): void
    {
        $diff = [
            [
                'key' => 'name',
                'type' => ArrayComparator::ADDED,
                'value' => 'new value',
            ],
        ];

        $result = PlainFormatter::format($diff);

        $this->assertSame("Property 'name' was added with value: 'new value'", $result);
    }

    /**
     * Тест удаления свойства.
     */
    public function testFormatRemovedProperty(): void
    {
        $diff = [
            [
                'key' => 'oldKey',
                'type' => ArrayComparator::REMOVED,
                'value' => 'old value',
            ],
        ];

        $result = PlainFormatter::format($diff);

        $this->assertSame("Property 'oldKey' was removed", $result);
    }

    /**
     * Тест изменения свойства.
     */
    public function testFormatChangedProperty(): void
    {
        $diff = [
            [
                'key' => 'status',
                'type' => ArrayComparator::CHANGED,
                'oldValue' => 'active',
                'newValue' => 'inactive',
            ],
        ];

        $result = PlainFormatter::format($diff);

        $this->assertSame("Property 'status' was updated. From 'active' to 'inactive'", $result);
    }

    /**
     * Тест вложенных свойств.
     */
    public function testFormatNestedProperties(): void
    {
        $diff = [
            [
                'key' => 'database',
                'type' => ArrayComparator::NESTED,
                'children' => [
                    [
                        'key' => 'host',
                        'type' => ArrayComparator::CHANGED,
                        'oldValue' => 'localhost',
                        'newValue' => '127.0.0.1',
                    ],
                ],
            ],
        ];

        $result = PlainFormatter::format($diff);

        $this->assertSame("Property 'database.host' was updated. From 'localhost' to '127.0.0.1'", $result);
    }

    /**
     * Тест глубоко вложенных свойств.
     */
    public function testFormatDeeplyNestedProperties(): void
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
                                'type' => ArrayComparator::ADDED,
                                'value' => 'value',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $result = PlainFormatter::format($diff);

        $this->assertSame("Property 'level1.level2.key' was added with value: 'value'", $result);
    }

    /**
     * Тест с null значением.
     */
    public function testFormatWithNullValue(): void
    {
        $diff = [
            [
                'key' => 'nullable',
                'type' => ArrayComparator::CHANGED,
                'oldValue' => null,
                'newValue' => 'not null',
            ],
        ];

        $result = PlainFormatter::format($diff);

        $this->assertSame("Property 'nullable' was updated. From null to 'not null'", $result);
    }

    /**
     * Тест с boolean значением.
     */
    public function testFormatWithBooleanValue(): void
    {
        $diff = [
            [
                'key' => 'enabled',
                'type' => ArrayComparator::CHANGED,
                'oldValue' => true,
                'newValue' => false,
            ],
        ];

        $result = PlainFormatter::format($diff);

        $this->assertSame("Property 'enabled' was updated. From true to false", $result);
    }

    /**
     * Тест с числовым значением.
     */
    public function testFormatWithNumericValue(): void
    {
        $diff = [
            [
                'key' => 'count',
                'type' => ArrayComparator::CHANGED,
                'oldValue' => 10,
                'newValue' => 20,
            ],
        ];

        $result = PlainFormatter::format($diff);

        $this->assertSame("Property 'count' was updated. From 10 to 20", $result);
    }

    /**
     * Тест с complex value (массив).
     */
    public function testFormatWithComplexValue(): void
    {
        $diff = [
            [
                'key' => 'settings',
                'type' => ArrayComparator::ADDED,
                'value' => ['key1' => 'value1', 'key2' => 'value2'],
            ],
        ];

        $result = PlainFormatter::format($diff);

        $this->assertSame("Property 'settings' was added with value: [complex value]", $result);
    }

    /**
     * Тест пустого diff.
     */
    public function testFormatEmptyDiff(): void
    {
        $result = PlainFormatter::format([]);

        $this->assertSame('', $result);
    }

    /**
     * Тест нескольких изменений.
     */
    public function testFormatMultipleChanges(): void
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

        $result = PlainFormatter::format($diff);

        $expected = <<<'EXPECTED'
Property 'added' was added with value: 'new'
Property 'removed' was removed
Property 'changed' was updated. From 'from' to 'to'
EXPECTED;

        $this->assertSame($expected, $result);
    }
}
