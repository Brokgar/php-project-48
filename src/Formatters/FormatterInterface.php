<?php

namespace Hexlet\Gendiff\Formatters;

/**
 * Интерфейс для форматтеров разницы между файлами.
 */
interface FormatterInterface
{
    /**
     * Форматирует разницу между файлами.
     *
     * @param array $diff Структура разницы от ArrayComparator
     * @return string Отформатированная строка
     */
    public function format(array $diff): string;
}
