<?php

namespace Hexlet\Gendiff\Formatters;

class PlainFormatter implements FormatterInterface
{
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

    private function renderPlain(array $diff, string $path = ''): string
    {
        return $this->format($diff, $path);
    }

    private function formatPlainValue(mixed $value): string
    {
        if (is_array($value)) {
            return '[complex value]';
        }

        return match (true) {
            is_null($value) => 'null',
            is_bool($value) => $value ? 'true' : 'false',
            default => is_string($value) ? "'$value'" : (string) $value,
        };
    }
}
