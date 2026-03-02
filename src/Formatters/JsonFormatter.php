<?php

namespace Hexlet\Gendiff\Formatters;

/**
 * Форматтер для вывода разницы в формате JSON.
 */
class JsonFormatter implements FormatterInterface
{
    /**
     * Форматирует разницу в JSON.
     *
     * @param array $diff Структура разницы от ArrayComparator
     * @return string JSON-представление разницы
     */
    public static function format(array $diff): string
    {
        return json_encode($diff, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
