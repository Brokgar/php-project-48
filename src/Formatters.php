<?php

namespace Hexlet\Gendiff;

use Hexlet\Gendiff\Exception\UnsupportedFormatException;
use Hexlet\Gendiff\Formatters\FormatterInterface;
use Hexlet\Gendiff\Formatters\JsonFormatter;
use Hexlet\Gendiff\Formatters\PlainFormatter;
use Hexlet\Gendiff\Formatters\StylishFormatter;

class Formatters
{
    /**
     * @var array<string, FormatterInterface>
     */
    private array $formatters;

    /**
     * @param array<string, FormatterInterface>|null $formatters
     */
    public function __construct(?array $formatters = null)
    {
        $this->formatters = $formatters ?? [
            'stylish' => new StylishFormatter(),
            'plain' => new PlainFormatter(),
            'json' => new JsonFormatter(),
        ];
    }

    public function format(array $diff, string $format = 'stylish'): string
    {
        $formatter = $this->formatters[$format] ?? null;
        if ($formatter === null) {
            throw new UnsupportedFormatException("Unsupported format: '$format'");
        }

        return $formatter->format($diff);
    }
}
