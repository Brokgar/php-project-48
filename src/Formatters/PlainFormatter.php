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
    public function format(array $diff, string $path = ''): string
    {
        $output = [];

        foreach ($diff as $node) {
            $key = $node['key'];
            $currentPath = $path !== '' ? "$path.$key" : $key;

            if ($node['type'] === 'added') {
                $value = $this->formatPlainValue($node['value']);
                $output[] = "Property '$currentPath' was added with value: $value";
                continue;
            }

            if ($node['type'] === 'removed') {
                $output[] = "Property '$currentPath' was removed";
                continue;
            }

            if ($node['type'] === 'changed') {
                $oldValue = $this->formatPlainValue($node['oldValue']);
                $newValue = $this->formatPlainValue($node['newValue']);
                $output[] = "Property '$currentPath' was updated. From $oldValue to $newValue";
                continue;
            }

            if ($node['type'] === 'nested') {
                $nestedOutput = $this->renderPlain($node['children'], $currentPath);
                if ($nestedOutput !== '') {
                    $output[] = $nestedOutput;
                }
            }
        }

        return implode("\n", $output);
    }

    /**
     * Рекурсивно форматирует разницу в простой формат.
     *
     * @param array $diff Структура разницы
     * @param string $path Текущий путь
     * @return string Отформатированная строка
     */
    private function renderPlain(array $diff, string $path = ''): string
    {
        return $this->format($diff, $path);
    }

    /**
     * Форматирует значение для простого вывода.
     *
     * @param mixed $value Значение для форматирования
     * @return string Отформатированное значение
     */
    private function formatPlainValue($value): string
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
