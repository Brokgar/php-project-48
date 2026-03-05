<?php

namespace Hexlet\Gendiff\Formatters;

/**
 * Форматтер для простого текстового вывода разницы.
 * Показывает только изменённые свойства.
 */
class PlainFormatter implements FormatterInterface
{
    /**
     * Форматирует разницу в простом текстовом формате.
     *
     * @param array $diff Структура разницы от ArrayComparator
     * @param string $path Текущий путь к свойству
     * @return string Отформатированная строка
     */
    public static function format(array $diff, string $path = ''): string
    {
        $output = [];

        foreach ($diff as $node) {
            $key = $node['key'];
            $currentPath = $path ? "$path.$key" : $key;

            if ($node['type'] === 'added') {
                $value = self::formatPlainValue($node['value']);
                $output[] = "Property '$currentPath' was added with value: $value";
            } elseif ($node['type'] === 'removed') {
                $output[] = "Property '$currentPath' was removed";
            } elseif ($node['type'] === 'changed') {
                $oldValue = self::formatPlainValue($node['oldValue']);
                $newValue = self::formatPlainValue($node['newValue']);
                $output[] = "Property '$currentPath' was updated. From $oldValue to $newValue";
            } elseif ($node['type'] === 'nested') {
                $nestedOutput = self::renderPlain($node['children'], $currentPath);
                if ($nestedOutput !== '') {
                    $output[] = $nestedOutput;
                }
            }
        }

        return implode("\n", array_filter($output));
    }

    /**
     * Рекурсивно форматирует разницу в простой формат.
     *
     * @param array $diff Структура разницы
     * @param string $path Текущий путь
     * @return string Отформатированная строка
     */
    private static function renderPlain(array $diff, string $path = ''): string
    {
        return self::format($diff, $path);
    }

    /**
     * Форматирует значение для простого вывода.
     *
     * @param mixed $value Значение для форматирования
     * @return string Отформатированное значение
     */
    private static function formatPlainValue($value): string
    {
        if (is_array($value)) {
            return '[complex value]';
        }

        return match (true) {
            is_null($value) => 'null',
            is_bool($value) => $value ? 'true' : 'false',
            default => is_string($value) ? "'$value'" : (string)$value
        };
    }
}
