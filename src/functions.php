<?php

namespace Differ\Differ;

use Hexlet\Gendiff\Gendiff;

/**
 * Генерирует разницу между двумя файлами конфигурации
 *
 * @param string $pathToFile1 Путь к первому файлу
 * @param string $pathToFile2 Путь ко второму файлу
 * @param array $options Опции форматирования (формат, цвет и т. д.)
 * @return string Строка с разницей в выбранном формате
 */
function genDiff(string $pathToFile1, string $pathToFile2, array $options = []): string
{
    return Gendiff::compareFiles($pathToFile1, $pathToFile2, $options);
}
