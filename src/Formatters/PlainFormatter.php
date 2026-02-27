<?php

namespace Hexlet\Gendiff\Formatters;

class PlainFormatter
{
    public static function format(array $diff): string
    {
        return self::renderPlain($diff);
    }

    private static function renderPlain(array $diff, string $path = ''): string
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
                $output[] = "Property '$currentPath' changed from $oldValue to $newValue";
            } elseif ($node['type'] === 'nested') {
                $output[] = self::renderPlain($node['children'], $currentPath);
            }
        }

        return implode("\n", array_filter($output));
    }

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