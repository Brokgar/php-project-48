<?php

namespace Hexlet\Gendiff;

class Generator
{
    public static function generateDiff(array $data1, array $data2, string $format = 'plain'): string
    {
        switch ($format) {
            case 'plain':
                return self::generatePlainDiff($data1, $data2);
            case 'stylish':
                return self::generateStylishDiff($data1, $data2);
            case 'json':
                return json_encode(self::calculateDiff($data1, $data2), JSON_PRETTY_PRINT);
            default:
                throw new \InvalidArgumentException("Неизвестный формат: $format");
        }
    }

    private static function generatePlainDiff(array $data1, array $data2): string
    {
        $diff = self::calculateDiff($data1, $data2);
        $output = [];

        foreach ($diff['added'] as $key => $value) {
            $output[] = "Property '$key' was added with value: $value";
        }

        foreach ($diff['removed'] as $key => $value) {
            $output[] = "Property '$key' was removed (was: $value)";
        }

        foreach ($diff['changed'] as $key => $change) {
            $output[] = "Property '$key' changed from '{$change['old']}' to '{$change['new']}'";
        }

        if (empty($output)) {
            $output[] = "No differences found";
        }

        return implode("\n", $output) . "\n";
    }

    private static function generateStylishDiff(array $data1, array $data2): string
    {
        $diff = self::calculateDiff($data1, $data2);
        $output = ['{'];
        $indent = '  ';

        // Сортируем ключи для единообразного вывода
        $allKeys = array_unique(array_merge(
            array_keys($data1),
            array_keys($data2)
        ));
        sort($allKeys);

        foreach ($allKeys as $key) {
            if (isset($diff['added'][$key])) {
                $output[] = $indent . "+ $key: " . self::formatValue($diff['added'][$key]);
            } elseif (isset($diff['removed'][$key])) {
                $output[] = $indent . "- $key: " . self::formatValue($diff['removed'][$key]);
            } elseif (isset($diff['changed'][$key])) {
                $output[] = $indent . "~ $key: " . self::formatValue($diff['changed'][$key]['old']) .
                            " → " . self::formatValue($diff['changed'][$key]['new']);
            } else {
                // Неизменное свойство
                $value = $data1[$key] ?? $data2[$key];
                $output[] = $indent . "  $key: " . self::formatValue($value);
            }
        }

        $output[] = '}';
        return implode("\n", $output) . "\n";
    }
                
    /**
     * Форматирует значение для красивого вывода
     */
    private static function formatValue($value): string
    {
        if (is_array($value)) {
            return '[array]';
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif ($value === null) {
            return 'null';
        } else {
            return (string)$value;
        }
    }

    private static function calculateDiff(array $data1, array $data2): array
    {
        $result = [
            'added' => [],
            'removed' => [],
            'changed' => [],
            'unchanged' => []
        ];

        // Проверяем ключи, которые есть в первом массиве, но отсутствуют во втором (удалённые)
        foreach ($data1 as $key => $value) {
            if (!array_key_exists($key, $data2)) {
                $result['removed'][$key] = $value;
            } elseif ($data2[$key] !== $value) {
                // Если ключ есть в обоих, но значения различаются (изменённые)
                $result['changed'][$key] = [
                    'old' => $value,
            'new' => $data2[$key]
                ];
            } else {
                // Значения совпадают (неизменные)
                $result['unchanged'][$key] = $value;
            }
        }

        // Проверяем ключи, которые есть во втором массиве, но отсутствуют в первом (добавленные)
        foreach ($data2 as $key => $value) {
            if (!array_key_exists($key, $data1)) {
                $result['added'][$key] = $value;
            }
        }

        return $result;
    }
}
?>
