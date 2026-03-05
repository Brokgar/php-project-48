<?php

namespace Hexlet\Gendiff\Formatters;

use RuntimeException;

class JsonFormatter implements FormatterInterface
{
    public static function format(array $diff): string
    {
        $json = json_encode($diff, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new RuntimeException('Failed to encode diff to JSON');
        }

        return $json;
    }
}
