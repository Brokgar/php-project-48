<?php

namespace Hexlet\Gendiff;

use Hexlet\Gendiff\Utils\ArrayComparator;

class Gendiff
{
    private Parser $parser;
    private ArrayComparator $comparator;
    private Formatters $formatters;

    public function __construct(
        ?Parser $parser = null,
        ?ArrayComparator $comparator = null,
        ?Formatters $formatters = null
    ) {
        $this->parser = $parser ?? new Parser();
        $this->comparator = $comparator ?? new ArrayComparator();
        $this->formatters = $formatters ?? new Formatters();
    }

    public function compareFiles(string $file1, string $file2, string $format = 'stylish'): string
    {
        $data1 = $this->parser->parseFile($file1);
        $data2 = $this->parser->parseFile($file2);

        $diff = $this->comparator->compare($data1, $data2);

        return $this->formatters->format($diff, $format);
    }
}
