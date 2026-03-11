<?php

namespace Hexlet\Gendiff\Utils;

use function Functional\sort;

class DiffTreeBuilder
{
    public const ADDED = 'added';
    public const REMOVED = 'removed';
    public const UNCHANGED = 'unchanged';
    public const CHANGED = 'changed';
    public const NESTED = 'nested';

    public function compare(array $data1, array $data2): array
    {
        $keys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
        $sortedKeys = sort($keys, fn($left, $right) => $left <=> $right);

        return array_map(function ($key) use ($data1, $data2) {
            $inFirst  = array_key_exists($key, $data1);
            $inSecond = array_key_exists($key, $data2);

            if (!$inFirst) {
                return [
                    'key' => $key,
                    'type' => self::ADDED,
                    'value' => $data2[$key],
                ];
            }

            if (!$inSecond) {
                return [
                    'key' => $key,
                    'type' => self::REMOVED,
                    'value' => $data1[$key],
                ];
            }

            if (is_array($data1[$key]) && is_array($data2[$key])) {
                return [
                    'key' => $key,
                    'type' => self::NESTED,
                    'children' => $this->compare($data1[$key], $data2[$key]),
                ];
            }

            if ($data1[$key] !== $data2[$key]) {
                return [
                    'key' => $key,
                    'type' => self::CHANGED,
                    'oldValue' => $data1[$key],
                    'newValue' => $data2[$key],
                ];
            }

            return [
                'key' => $key,
                'type' => self::UNCHANGED,
                'value' => $data1[$key],
            ];
        }, $sortedKeys);
    }
}
