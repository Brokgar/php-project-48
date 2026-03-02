<?php

namespace Hexlet\Gendiff;

use Hexlet\Gendiff\Formatters\FormatterInterface;
use Hexlet\Gendiff\Formatters\JsonFormatter;
use Hexlet\Gendiff\Formatters\PlainFormatter;
use Hexlet\Gendiff\Formatters\StylishFormatter;

/**
 * Класс-агрегатор для работы с форматтерами.
 * Выбирает и применяет нужный форматтер в зависимости от указанного формата.
 */
class Formatters
{
    /**
     * Форматирует разницу между файлами в указанный формат.
     *
     * @param array $diff Структура разницы от ArrayComparator
     * @param string $format Тип формата (stylish, plain, json)
     * @return string Отформатированная строка
     * @throws \InvalidArgumentException Если формат не поддерживается
     */
    public static function format(array $diff, string $format = 'stylish'): string
    {
        return match ($format) {
            'stylish' => StylishFormatter::renderStylish($diff),
            'plain' => PlainFormatter::format($diff),
            'json' => JsonFormatter::format($diff),
            default => throw new \InvalidArgumentException("Формат '$format' не поддерживается")
        };
    }
}
