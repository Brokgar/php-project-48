<?php

namespace Hexlet\Gendiff\Formatters;

interface FormatterInterface
{
    public function format(array $diff): string;
}
