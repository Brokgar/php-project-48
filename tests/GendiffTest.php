<?php

namespace Tests;

use Hexlet\Gendiff\Gendiff;
use PHPUnit\Framework\TestCase;

class GendiffTest extends TestCase
{
    private string $fixturesDir;
    private Gendiff $gendiff;

    private function normalizeLineEndings(string $value): string
    {
        return str_replace("\r\n", "\n", $value);
    }

    protected function setUp(): void
    {
        $this->fixturesDir = __DIR__ . '/fixtures';
        $this->gendiff = new Gendiff();
    }

    /**
     * @dataProvider compareFilesProvider
     */
    public function testGenDiffFormats(string $inputExtension, ?string $format, string $expectedFixture): void
    {
        $file1 = $this->fixturePath("file1.{$inputExtension}");
        $file2 = $this->fixturePath("file2.{$inputExtension}");
        $expectedFixturePath = $this->fixturePath($expectedFixture);

        $result = $format === null
            ? $this->gendiff->compareFiles($file1, $file2)
            : $this->gendiff->compareFiles($file1, $file2, $format);

        $expected = file_get_contents($expectedFixturePath);

        $this->assertIsString($expected);
        $this->assertSame($this->normalizeLineEndings($expected), $this->normalizeLineEndings($result));
    }

    /**
     * @return array<string, array{0: string, 1: string|null, 2: string}>
     */
    public static function compareFilesProvider(): array
    {
        return [
            'json default stylish' => ['json', null, 'result_stylish.txt'],
            'json explicit stylish' => ['json', 'stylish', 'result_stylish.txt'],
            'json plain' => ['json', 'plain', 'result_plain.txt'],
            'json json' => ['json', 'json', 'result_json.txt'],
            'yaml default stylish' => ['yaml', null, 'result_stylish.txt'],
            'yaml explicit stylish' => ['yaml', 'stylish', 'result_stylish.txt'],
            'yaml plain' => ['yaml', 'plain', 'result_plain.txt'],
            'yaml json' => ['yaml', 'json', 'result_json.txt'],
        ];
    }

    private function fixturePath(string $fixtureName): string
    {
        return $this->fixturesDir . '/' . $fixtureName;
    }
}
