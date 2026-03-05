<?php

namespace Hexlet\Gendiff\Formatters;

class StylishFormatter
{
    public static function renderStylish(array $diff, int $depth = 0): string
    {
        $currentIndent = str_repeat(' ', $depth * 4);
        $signIndent = $currentIndent . '  ';
        $lines = ['{'];

        foreach ($diff as $node) {
            $key = $node['key'];
            $type = $node['type'];

            if ($type === 'nested') {
                $value = self::renderStylish($node['children'], $depth + 1);
                $lines[] = sprintf('%s  %s: %s', $signIndent, $key, $value);
                continue;
            }

            if ($type === 'changed') {
                $oldValue = self::stringify($node['oldValue'], $depth + 1);
                $newValue = self::stringify($node['newValue'], $depth + 1);
                $lines[] = sprintf('%s- %s: %s', $signIndent, $key, $oldValue);
                $lines[] = sprintf('%s+ %s: %s', $signIndent, $key, $newValue);
                continue;
            }

            $sign = match ($type) {
                'added' => '+',
                'removed' => '-',
                default => ' ',
            };

            $value = self::stringify($node['value'], $depth + 1);
            $lines[] = sprintf('%s%s %s: %s', $signIndent, $sign, $key, $value);
        }

        $lines[] = $currentIndent . '}';

        return implode("\n", $lines);
    }

    private static function stringify(mixed $value, int $depth): string
    {
        if (!is_array($value)) {
            return match (true) {
                $value === null => 'null',
                is_bool($value) => $value ? 'true' : 'false',
                default => (string) $value,
            };
        }

        $currentIndent = str_repeat(' ', $depth * 4);
        $lines = ['{'];

        foreach ($value as $key => $item) {
            $formattedItem = self::stringify($item, $depth + 1);
            $lines[] = sprintf('%s    %s: %s', $currentIndent, (string) $key, $formattedItem);
        }

        $lines[] = $currentIndent . '}';

        return implode("\n", $lines);
    }
}
