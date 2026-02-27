<?php

use PHPUnit\Framework\TestCase;
use Hexlet\Gendiff\Gendiff;

class GendiffTest extends TestCase
{
    private string $fixturesDir;

    protected function setUp(): void
    {
        $this->fixturesDir = __DIR__ . '/fixtures';
    }

    public function testGenDiffStylishFormat(): void
    {
        $file1 = $this->fixturesDir . '/file1.json';
        $file2 = $this->fixturesDir . '/file2.json';

        $result = Gendiff::compareFiles($file1, $file2);

        $expected = file_get_contents($this->fixturesDir . '/result_stylish.txt');
        $this->assertSame($expected, $result);
    }

    public function testGenDiffPlainFormat(): void
    {
        $file1 = $this->fixturesDir . '/file1.json';
        $file2 = $this->fixturesDir . '/file2.json';

        $result = Gendiff::compareFiles($file1, $file2, ['format' => 'plain']);

        $expected = file_get_contents($this->fixturesDir . '/result_plain.txt');
        $this->assertSame($expected, $result);
    }

    public function testGenDiffJsonFormat(): void
    {
        $file1 = $this->fixturesDir . '/file1.json';
        $file2 = $this->fixturesDir . '/file2.json';

        $result = Gendiff::compareFiles($file1, $file2, ['format' => 'json']);

        $expected = file_get_contents($this->fixturesDir . '/result_json.txt');
        $this->assertSame($expected, $result);
    }
}