<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Hexlet\Gendiff\Gendiff;

class GendiffTest extends TestCase
{
    private string $fixturesDir;
    private Gendiff $gendiff;

    protected function setUp(): void
    {
        $this->fixturesDir = __DIR__ . '/fixtures';
        $this->gendiff = new Gendiff();
    }

    public function testGenDiffStylishFormat(): void
    {
        $file1 = $this->fixturesDir . '/file1.json';
        $file2 = $this->fixturesDir . '/file2.json';

        $result = $this->gendiff->compareFiles($file1, $file2);

        $expected = file_get_contents($this->fixturesDir . '/result_stylish.txt');
        $this->assertSame($expected, $result);
    }

    public function testGenDiffPlainFormat(): void
    {
        $file1 = $this->fixturesDir . '/file1.json';
        $file2 = $this->fixturesDir . '/file2.json';

        $result = $this->gendiff->compareFiles($file1, $file2, 'plain');

        $expected = file_get_contents($this->fixturesDir . '/result_plain.txt');
        $this->assertSame($expected, $result);
    }

    public function testGenDiffJsonFormat(): void
    {
        $file1 = $this->fixturesDir . '/file1.json';
        $file2 = $this->fixturesDir . '/file2.json';

        $result = $this->gendiff->compareFiles($file1, $file2, 'json');

        $expected = file_get_contents($this->fixturesDir . '/result_json.txt');
        $this->assertSame($expected, $result);
    }
}
