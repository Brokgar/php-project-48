<?php

namespace Hexlet\Gendiff\Formatters;

use Hexlet\Gendiff\Exception\JsonEncodeException;

class JsonFormatter implements FormatterInterface
{
    public function format(array $diff): string
    {
        $json = json_encode($diff, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new JsonEncodeException('Failed to encode diff to JSON');
        }

        return str_replace("\n", PHP_EOL, $json);
    }
}
