<?php

namespace Hexlet\Gendiff\Formatters;

class StylishFormatter implements FormatterInterface
{
    public function format(array $diff): string
    {
        return $this->renderStylish($diff);
    }

    public function renderStylish(array $diff, int $depth = 0): string
    {
        $currentIndent = str_repeat(' ', $depth * 4);
        $signIndent = sprintf('%s  ', $currentIndent);
        $mappedLines = array_map(function (array $node) use ($depth, $signIndent): array {
            $key = $node['key'];
            $type = $node['type'];

            if ($type === 'nested') {
                $value = $this->renderStylish($node['children'], $depth + 1);
                return [sprintf('%s  %s: %s', $signIndent, $key, $value)];
            }

            if ($type === 'changed') {
                $oldValue = $this->stringify($node['oldValue'], $depth + 1);
                $newValue = $this->stringify($node['newValue'], $depth + 1);

                return [
                    sprintf('%s- %s: %s', $signIndent, $key, $oldValue),
                    sprintf('%s+ %s: %s', $signIndent, $key, $newValue),
                ];
            }

            $sign = match ($type) {
                'added' => '+',
                'removed' => '-',
                default => ' ',
            };

            $value = $this->stringify($node['value'], $depth + 1);

            return [sprintf('%s%s %s: %s', $signIndent, $sign, $key, $value)];
        }, $diff);

        $lines = ['{', ...array_merge(...$mappedLines), sprintf('%s}', $currentIndent)];

        return implode(PHP_EOL, $lines);
    }

    private function stringify(mixed $value, int $depth): string
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
            $formattedItem = $this->stringify($item, $depth + 1);
            $lines[] = sprintf('%s    %s: %s', $currentIndent, (string) $key, $formattedItem);
        }

        $lines[] = sprintf('%s}', $currentIndent);

        return implode(PHP_EOL, $lines);
    }
}
