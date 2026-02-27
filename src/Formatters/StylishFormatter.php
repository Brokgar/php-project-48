<?php

namespace Hexlet\Gendiff\Formatters;

class StylishFormatter
{
    public static function format(array $diff, int $depth = 0): string
    {
        $indent = str_repeat('  ', $depth + 1);
        $lines = ['{'];

        foreach ($diff as $node) {
            $key = $node['key'];
            $type = $node['type'];

            $value = match (true) {
                $type === 'added' => self::formatValue($node['value'], $depth),
                $type === 'removed' => self::formatValue($node['value'], $depth),
                $type === 'unchanged' => self::formatValue($node['value'], $depth),
                $type === 'changed' => self::formatValue($node['newValue'], $depth),
                $type === 'nested' => self::renderStylish($node['children'], $depth + 1),
                default => ''
            };

            $prefix = match ($type) {
                'added' => '+ ',
                'removed' => '- ',
                'changed' => '~ ',
                'unchanged' => '  ',
                'nested' => '  ',
                default => '  '
            };

            $lines[] = "$indent$prefix$key: $value";
        }

        $lines[] = str_repeat('  ', $depth) . '}';
        return implode("\n", $lines);
    }

    private static function formatValue($value, int $depth = 0): string
    {
        if (is_array($value)) {
            $indent = str_repeat('  ', $depth + 2);
            $innerIndent = str_repeat('  ', $depth + 3);
            $lines = ['[complex value]'];
            foreach ($value as $k => $v) {
                $lines[] = "$innerIndent$k: " . self::formatValue($v, $depth + 1);
            }
            $lines[] = $indent . ']';
            return implode("\n", $lines);
        }

        return match (true) {
            is_null($value) => 'null',
            is_bool($value) => $value ? 'true' : 'false',
            default => (string)$value
        };
    }
}