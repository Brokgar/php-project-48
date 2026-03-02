<?php

namespace Hexlet\Gendiff\Formatters;

/**
 * Форматтер для красивого вывода разницы между файлами.
 * Использует отступы и символы для обозначения изменений.
 */
class StylishFormatter
{
    /**
     * Форматирует разницу в красивом стиле.
     *
     * @param array $diff Структура разницы от ArrayComparator
     * @param int $depth Текущая глубина вложенности
     * @return string Отформатированная строка
     */
    public static function renderStylish(array $diff, int $depth = 0): string
    {
        $indent = str_repeat('  ', $depth + 1);
        $lines = ['{'];

        foreach ($diff as $node) {
            $key = $node['key'];
            $type = $node['type'];

            if ($type === 'nested') {
                $value = self::renderStylish($node['children'], $depth + 1);
                $prefix = '  ';
                $lines[] = "$indent$prefix$key: $value";
            } else {
                $value = self::formatValue($node, $depth);
                $prefix = match ($type) {
                    'added' => '+ ',
                    'removed' => '- ',
                    'changed' => '~ ',
                    default => '  '
                };
                $lines[] = "$indent$prefix$key: $value";
            }
        }

        $lines[] = str_repeat('  ', $depth) . '}';
        return implode("\n", $lines);
    }

    /**
     * Форматирует значение для вывода.
     *
     * @param array $node Узел разницы
     * @param int $depth Текущая глубина
     * @return string Отформатированное значение
     */
    private static function formatValue(array $node, int $depth = 0): string
    {
        $value = match ($node['type']) {
            'added', 'removed', 'unchanged' => $node['value'],
            'changed' => $node['newValue'],
            default => null
        };

        if (is_array($value)) {
            return self::renderStylish($value, $depth + 1);
        }

        return match (true) {
            is_null($value) => 'null',
            is_bool($value) => $value ? 'true' : 'false',
            default => (string)$value
        };
    }
}
