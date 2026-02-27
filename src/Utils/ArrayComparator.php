<?php

namespace Hexlet\Gendiff\Utils;

class ArrayComparator
{
    public const ADDED = 'added';
    public const REMOVED = 'removed';
    public const UNCHANGED = 'unchanged';
    public const CHANGED = 'changed';
    public const NESTED = 'nested';

    /**
     * Сравнивает два массива и возвращает структуру с разницей
     *
     * @param array $data1
     * @param array $data2
     * @return array
     */
    public static function compare(array $data1, array $data2): array
    {
        $keys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
        sort($keys);

        $result = [];

        foreach ($keys as $key) {
            $inFirst = array_key_exists($key, $data1);
            $inSecond = array_key_exists($key, $data2);

            if (!$inFirst) {
                $result[] = [
                    'key' => $key,
                    'type' => self::ADDED,
                    'value' => $data2[$key],
                ];
            } elseif (!$inSecond) {
                $result[] = [
                    'key' => $key,
                    'type' => self::REMOVED,
                    'value' => $data1[$key],
                ];
            } elseif (is_array($data1[$key]) && is_array($data2[$key])) {
                // Оба значения — массивы → рекурсивное сравнение
                $result[] = [
                    'key' => $key,
                    'type' => self::NESTED,
                    'children' => self::compare($data1[$key], $data2[$key]),
                ];
            } elseif ($data1[$key] !== $data2[$key]) {
                $result[] = [
                    'key' => $key,
                    'type' => self::CHANGED,
                    'oldValue' => $data1[$key],
                    'newValue' => $data2[$key],
                ];
            } else {
                $result[] = [
                    'key' => $key,
                    'type' => self::UNCHANGED,
                    'value' => $data1[$key],
                ];
            }
        }

        return $result;
    }
}