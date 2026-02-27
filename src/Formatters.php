<?php

namespace Hexlet\Gendiff;

use Hexlet\Gendiff\Formatters\JsonFormatter;
use Hexlet\Gendiff\Formatters\PlainFormatter;
use Hexlet\Gendiff\Formatters\StylishFormatter;

class Formatters
{
    public static function format(array $diff, string $format = 'stylish'): string
    {
        return match ($format) {
            'stylish' => StylishFormatter::format($diff),
            'plain' => PlainFormatter::format($diff),
            'json' => JsonFormatter::format($diff),
            default => throw new \InvalidArgumentException("Формат '$format' не поддерживается")
        };
    }
}