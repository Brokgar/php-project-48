<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class CliTest extends TestCase
{
    private string $fixturesDir;
    private string $binaryPath;

    protected function setUp(): void
    {
        $this->fixturesDir = __DIR__ . '/fixtures';
        $this->binaryPath = __DIR__ . '/../bin/gendiff';
    }

    public function testCliSupportsPlainFormatOption(): void
    {
        $file1 = $this->fixturesDir . '/file1.json';
        $file2 = $this->fixturesDir . '/file2.json';
        $expected = (string) file_get_contents($this->fixturesDir . '/result_plain.txt');
        $expected = str_replace("\n", PHP_EOL, $expected);

        [$output, $exitCode] = $this->runCli(['-f', 'plain', $file1, $file2]);

        $this->assertSame(0, $exitCode);
        $this->assertSame($expected, $output);
    }

    public function testCliReturnsErrorForUnknownFormat(): void
    {
        $file1 = $this->fixturesDir . '/file1.json';
        $file2 = $this->fixturesDir . '/file2.json';

        [$output, $exitCode] = $this->runCli(['-f', 'unknown', $file1, $file2]);

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('Error:', $output);
    }

    public function testCliReturnsErrorWhenFilesAreMissing(): void
    {
        [$output, $exitCode] = $this->runCli([]);

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('Usage:', $output);
        $this->assertStringContainsString('gendiff [options] <file1> <file2>', $output);
    }

    /**
     * @param array<int, string> $args
     * @return array{0: string, 1: int}
     */
    private function runCli(array $args): array
    {
        $parts = [
            escapeshellarg((string) PHP_BINARY),
            escapeshellarg($this->binaryPath),
        ];

        foreach ($args as $arg) {
            $parts[] = escapeshellarg($arg);
        }

        $command = implode(' ', $parts) . ' 2>&1';
        $output = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);

        return [implode(PHP_EOL, $output), $exitCode];
    }
}
