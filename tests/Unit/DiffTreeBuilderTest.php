<?php

namespace Tests;

use Hexlet\Gendiff\Utils\DiffTreeBuilder;
use PHPUnit\Framework\TestCase;

class DiffTreeBuilderTest extends TestCase
{
    private DiffTreeBuilder $diffTreeBuilder;

    protected function setUp(): void
    {
        $this->diffTreeBuilder = new DiffTreeBuilder();
    }

    public function testCompareIdenticalArrays(): void
    {
        $data = ['key' => 'value', 'number' => 42];

        $result = $this->diffTreeBuilder->compare($data, $data);

        $expected = [
            [
                'key' => 'key',
                'type' => DiffTreeBuilder::UNCHANGED,
                'value' => 'value',
            ],
            [
                'key' => 'number',
                'type' => DiffTreeBuilder::UNCHANGED,
                'value' => 42,
            ],
        ];

        $this->assertSame($expected, $result);
    }

    public function testCompareEmptyArrays(): void
    {
        $this->assertSame([], $this->diffTreeBuilder->compare([], []));
    }

    public function testCompareAddedKeys(): void
    {
        $data1 = ['old' => 'value'];
        $data2 = ['old' => 'value', 'new' => 'added'];

        $expected = [
            [
                'key' => 'new',
                'type' => DiffTreeBuilder::ADDED,
                'value' => 'added',
            ],
            [
                'key' => 'old',
                'type' => DiffTreeBuilder::UNCHANGED,
                'value' => 'value',
            ],
        ];

        $this->assertSame($expected, $this->diffTreeBuilder->compare($data1, $data2));
    }

    public function testCompareRemovedKeys(): void
    {
        $data1 = ['keep' => 'value', 'remove' => 'gone'];
        $data2 = ['keep' => 'value'];

        $expected = [
            [
                'key' => 'keep',
                'type' => DiffTreeBuilder::UNCHANGED,
                'value' => 'value',
            ],
            [
                'key' => 'remove',
                'type' => DiffTreeBuilder::REMOVED,
                'value' => 'gone',
            ],
        ];

        $this->assertSame($expected, $this->diffTreeBuilder->compare($data1, $data2));
    }

    public function testCompareChangedValues(): void
    {
        $data1 = ['key' => 'old'];
        $data2 = ['key' => 'new'];

        $expected = [[
            'key' => 'key',
            'type' => DiffTreeBuilder::CHANGED,
            'oldValue' => 'old',
            'newValue' => 'new',
        ]];

        $this->assertSame($expected, $this->diffTreeBuilder->compare($data1, $data2));
    }

    public function testCompareNestedStructures(): void
    {
        $data1 = ['nested' => ['key1' => 'value1']];
        $data2 = ['nested' => ['key1' => 'value1', 'key2' => 'value2']];

        $expected = [[
            'key' => 'nested',
            'type' => DiffTreeBuilder::NESTED,
            'children' => [
                [
                    'key' => 'key1',
                    'type' => DiffTreeBuilder::UNCHANGED,
                    'value' => 'value1',
                ],
                [
                    'key' => 'key2',
                    'type' => DiffTreeBuilder::ADDED,
                    'value' => 'value2',
                ],
            ],
        ]];

        $this->assertSame($expected, $this->diffTreeBuilder->compare($data1, $data2));
    }

    public function testCompareDeeplyNestedStructures(): void
    {
        $data1 = ['level1' => ['level2' => ['level3' => ['key' => 'value1']]]];
        $data2 = ['level1' => ['level2' => ['level3' => ['key' => 'value2']]]];

        $result = $this->diffTreeBuilder->compare($data1, $data2);

        $this->assertSame(DiffTreeBuilder::NESTED, $result[0]['type']);
        $this->assertSame(DiffTreeBuilder::NESTED, $result[0]['children'][0]['type']);
        $this->assertSame(DiffTreeBuilder::NESTED, $result[0]['children'][0]['children'][0]['type']);
        $this->assertSame(DiffTreeBuilder::CHANGED, $result[0]['children'][0]['children'][0]['children'][0]['type']);
    }

    public function testKeysAreSorted(): void
    {
        $data1 = ['zebra' => 'z', 'alpha' => 'a'];
        $data2 = ['zebra' => 'z', 'alpha' => 'a'];

        $result = $this->diffTreeBuilder->compare($data1, $data2);

        $this->assertSame('alpha', $result[0]['key']);
        $this->assertSame('zebra', $result[1]['key']);
    }

    public function testCompareWithNullValues(): void
    {
        $expected = [[
            'key' => 'key',
            'type' => DiffTreeBuilder::CHANGED,
            'oldValue' => null,
            'newValue' => 'value',
        ]];

        $this->assertSame($expected, $this->diffTreeBuilder->compare(['key' => null], ['key' => 'value']));
    }

    public function testCompareWithBooleanValues(): void
    {
        $expected = [[
            'key' => 'flag',
            'type' => DiffTreeBuilder::CHANGED,
            'oldValue' => true,
            'newValue' => false,
        ]];

        $this->assertSame($expected, $this->diffTreeBuilder->compare(['flag' => true], ['flag' => false]));
    }

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

        $result = $this->diffTreeBuilder->compare($data1, $data2);

        $this->assertCount(6, $result);
        $this->assertSame(DiffTreeBuilder::NESTED, $result[0]['type']);
        $this->assertSame(DiffTreeBuilder::CHANGED, $result[1]['type']);
        $this->assertSame(DiffTreeBuilder::UNCHANGED, $result[2]['type']);
        $this->assertSame(DiffTreeBuilder::CHANGED, $result[3]['type']);
        $this->assertSame(DiffTreeBuilder::CHANGED, $result[4]['type']);
        $this->assertSame(DiffTreeBuilder::UNCHANGED, $result[5]['type']);
    }
}
