<?php

namespace Differ\Differ;

use Hexlet\Gendiff\Gendiff;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    return Gendiff::compareFiles($pathToFile1, $pathToFile2, $options);
}
