<?php

namespace Tests;

use Hexlet\Gendiff\Utils\ArrayComparator;
use PHPUnit\Framework\TestCase;

class ArrayComparatorTest extends TestCase
{
    /**
     * Тест сравнения одинаковых массивов.
     */
    public function testCompareIdenticalArrays(): void
    {
        $data = ['key' => 'value', 'number' => 42];

        $result = ArrayComparator::compare($data, $data);

        $expected = [
            [
                'key' => 'key',
                'type' => ArrayComparator::UNCHANGED,
                'value' => 'value',
            ],
            [
                'key' => 'number',
                'type' => ArrayComparator::UNCHANGED,
                'value' => 42,
            ],
        ];

        $this->assertSame($expected, $result);
    }

    /**
     * Тест сравнения пустых массивов.
     */
    public function testCompareEmptyArrays(): void
    {
        $result = ArrayComparator::compare([], []);

        $this->assertSame([], $result);
    }

    /**
     * Тест добавления новых ключей.
     */
    public function testCompareAddedKeys(): void
    {
        $data1 = ['old' => 'value'];
        $data2 = ['old' => 'value', 'new' => 'added'];

        $result = ArrayComparator::compare($data1, $data2);

        $expected = [
            [
                'key' => 'new',
                'type' => ArrayComparator::ADDED,
                'value' => 'added',
            ],
            [
                'key' => 'old',
                'type' => ArrayComparator::UNCHANGED,
                'value' => 'value',
            ],
        ];

        $this->assertSame($expected, $result);
    }

    /**
     * Тест удаления ключей.
     */
    public function testCompareRemovedKeys(): void
    {
        $data1 = ['keep' => 'value', 'remove' => 'gone'];
        $data2 = ['keep' => 'value'];

        $result = ArrayComparator::compare($data1, $data2);

        $expected = [
            [
                'key' => 'keep',
                'type' => ArrayComparator::UNCHANGED,
                'value' => 'value',
            ],
            [
                'key' => 'remove',
                'type' => ArrayComparator::REMOVED,
                'value' => 'gone',
            ],
        ];

        $this->assertSame($expected, $result);
    }

    /**
     * Тест изменения значений.
     */
    public function testCompareChangedValues(): void
    {
        $data1 = ['key' => 'old'];
        $data2 = ['key' => 'new'];

        $result = ArrayComparator::compare($data1, $data2);

        $expected = [
            [
                'key' => 'key',
                'type' => ArrayComparator::CHANGED,
                'oldValue' => 'old',
                'newValue' => 'new',
            ],
        ];

        $this->assertSame($expected, $result);
    }

    /**
     * Тест вложенных структур.
     */
    public function testCompareNestedStructures(): void
    {
        $data1 = ['nested' => ['key1' => 'value1']];
        $data2 = ['nested' => ['key1' => 'value1', 'key2' => 'value2']];

        $result = ArrayComparator::compare($data1, $data2);

        $expected = [
            [
                'key' => 'nested',
                'type' => ArrayComparator::NESTED,
                'children' => [
                    [
                        'key' => 'key1',
                        'type' => ArrayComparator::UNCHANGED,
                        'value' => 'value1',
                    ],
                    [
                        'key' => 'key2',
                        'type' => ArrayComparator::ADDED,
                        'value' => 'value2',
                    ],
                ],
            ],
        ];

        $this->assertSame($expected, $result);
    }

    /**
     * Тест глубоко вложенных структур.
     */
    public function testCompareDeeplyNestedStructures(): void
    {
        $data1 = ['level1' => ['level2' => ['level3' => ['key' => 'value1']]]];
        $data2 = ['level1' => ['level2' => ['level3' => ['key' => 'value2']]]];

        $result = ArrayComparator::compare($data1, $data2);

        $this->assertCount(1, $result);
        $this->assertSame('level1', $result[0]['key']);
        $this->assertSame(ArrayComparator::NESTED, $result[0]['type']);

        $level2 = $result[0]['children'];
        $this->assertCount(1, $level2);
        $this->assertSame('level2', $level2[0]['key']);
        $this->assertSame(ArrayComparator::NESTED, $level2[0]['type']);

        $level3 = $level2[0]['children'];
        $this->assertCount(1, $level3);
        $this->assertSame('level3', $level3[0]['key']);
        $this->assertSame(ArrayComparator::NESTED, $level3[0]['type']);

        $level4 = $level3[0]['children'];
        $this->assertCount(1, $level4);
        $this->assertSame('key', $level4[0]['key']);
        $this->assertSame(ArrayComparator::CHANGED, $level4[0]['type']);
        $this->assertSame('value1', $level4[0]['oldValue']);
        $this->assertSame('value2', $level4[0]['newValue']);
    }

    /**
     * Тест сортировки ключей.
     */
    public function testKeysAreSorted(): void
    {
        $data1 = ['zebra' => 'z', 'alpha' => 'a'];
        $data2 = ['zebra' => 'z', 'alpha' => 'a'];

        $result = ArrayComparator::compare($data1, $data2);

        $this->assertSame('alpha', $result[0]['key']);
        $this->assertSame('zebra', $result[1]['key']);
    }

    /**
     * Тест с null значениями.
     */
    public function testCompareWithNullValues(): void
    {
        $data1 = ['key' => null];
        $data2 = ['key' => 'value'];

        $result = ArrayComparator::compare($data1, $data2);

        $expected = [
            [
                'key' => 'key',
                'type' => ArrayComparator::CHANGED,
                'oldValue' => null,
                'newValue' => 'value',
            ],
        ];

        $this->assertSame($expected, $result);
    }

    /**
     * Тест с boolean значениями.
     */
    public function testCompareWithBooleanValues(): void
    {
        $data1 = ['flag' => true];
        $data2 = ['flag' => false];

        $result = ArrayComparator::compare($data1, $data2);

        $expected = [
            [
                'key' => 'flag',
                'type' => ArrayComparator::CHANGED,
                'oldValue' => true,
                'newValue' => false,
            ],
        ];

        $this->assertSame($expected, $result);
    }

    /**
     * Тест смешанных типов данных.
     */
    public function testCompareMixedTypes(): void
    {
        $data1 = [
            'string' => 'text',
            'number' => 42,
            'float' => 3.14,
            'bool' => true,
            'null' => null,
            'array' => [1, 2, 3],
        ];
        $data2 = [
            'string' => 'text',
            'number' => 100,
            'float' => 3.14,
            'bool' => false,
            'null' => 'not null',
            'array' => [1, 2, 3],
        ];

        $result = ArrayComparator::compare($data1, $data2);

        $this->assertCount(6, $result);

        // Ключи сортируются по алфавиту
        $this->assertSame('array', $result[0]['key']);
        $this->assertSame(ArrayComparator::NESTED, $result[0]['type']); // array сравнивается как вложенная структура

        $this->assertSame('bool', $result[1]['key']);
        $this->assertSame(ArrayComparator::CHANGED, $result[1]['type']); // bool

        $this->assertSame('float', $result[2]['key']);
        $this->assertSame(ArrayComparator::UNCHANGED, $result[2]['type']); // float

        $this->assertSame('null', $result[3]['key']);
        $this->assertSame(ArrayComparator::CHANGED, $result[3]['type']); // null changed

        $this->assertSame('number', $result[4]['key']);
        $this->assertSame(ArrayComparator::CHANGED, $result[4]['type']); // number

        $this->assertSame('string', $result[5]['key']);
        $this->assertSame(ArrayComparator::UNCHANGED, $result[5]['type']); // string
    }
}
