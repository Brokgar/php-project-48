<?php

namespace Hexlet\Gendiff;

use Hexlet\Gendiff\Utils\ArrayComparator;

class Gendiff
{
    public function __construct(
        private ?Parser $parser = null,
        private ?ArrayComparator $comparator = null,
        private ?Formatters $formatters = null
    ) {
        $this->parser ??= new Parser();
        $this->comparator ??= new ArrayComparator();
        $this->formatters ??= new Formatters();
    }

    public function compareFiles(string $file1, string $file2, string $format = 'stylish'): string
    {
        $data1 = $this->parser->parseFile($file1);
        $data2 = $this->parser->parseFile($file2);

        $diff = $this->comparator->compare($data1, $data2);

        return $this->formatters->format($diff, $format);
    }
}
