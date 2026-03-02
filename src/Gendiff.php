<?php

namespace Hexlet\Gendiff;

use Hexlet\Gendiff\Parser;
use Hexlet\Gendiff\Utils\ArrayComparator;
use Hexlet\Gendiff\Formatters;

class Gendiff
{
    public static function compareFiles(string $file1, string $file2, string $format = 'stylish'): string
    {
        $data1 = Parser::parseFile($file1);
        $data2 = Parser::parseFile($file2);

        // $color is not used, format is passed as parameter

        $diff = ArrayComparator::compare($data1, $data2);

        return Formatters::format($diff, $format);
    }
}
