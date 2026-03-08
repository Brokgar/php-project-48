<?php

namespace Hexlet\Gendiff;

use Hexlet\Gendiff\Gendiff;

/**
 * Генерирует разницу между двумя файлами конфигурации.
 *
 * @param string $pathToFile1 Путь к первому файлу
 * @param string $pathToFile2 Путь ко второму файлу
 * @param string $format Формат вывода (stylish, plain, json)
 * @return string Отформатированная разница между файлами
 */
function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $gendiff = new Gendiff();
    return $gendiff->compareFiles($pathToFile1, $pathToFile2, $format);
}
