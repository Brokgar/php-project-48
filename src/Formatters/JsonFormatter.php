<?php

namespace Hexlet\Gendiff\Formatters;

class JsonFormatter
{
    public static function format(array $diff): string
    {
        return json_encode($diff, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}