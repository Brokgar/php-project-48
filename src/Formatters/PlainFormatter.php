<?php

namespace Hexlet\Gendiff\Formatters;

class PlainFormatter implements FormatterInterface
{
    public function format(array $diff, string $path = ''): string
    {
        $output = array_map(function (array $node) use ($path): ?string {
            $key = $node['key'];
            $currentPath = $path !== '' ? "$path.$key" : $key;

            return match ($node['type']) {
                'added' => "Property '$currentPath' was added with value: {$this->formatPlainValue($node['value'])}",
                'removed' => "Property '$currentPath' was removed",
                'changed' => sprintf(
                    "Property '%s' was updated. From %s to %s",
                    $currentPath,
                    $this->formatPlainValue($node['oldValue']),
                    $this->formatPlainValue($node['newValue'])
                ),
                'nested' => $this->formatNestedNode($node['children'], $currentPath),
                default => null,
            };
        }, $diff);

        return implode("\n", array_filter($output));
    }

    private function renderPlain(array $diff, string $path = ''): string
    {
        return $this->format($diff, $path);
    }

    private function formatNestedNode(array $children, string $currentPath): ?string
    {
        $formattedNode = $this->renderPlain($children, $currentPath);

        return $formattedNode !== '' ? $formattedNode : null;
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
