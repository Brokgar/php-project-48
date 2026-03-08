<?php

namespace Tests;

use Hexlet\Gendiff\Exception\ParseException;
use Hexlet\Gendiff\Exception\FileNotFoundException;
use PHPUnit\Framework\TestCase;
use Hexlet\Gendiff\Parser;

class ParserTest extends TestCase
{
    private string $fixturesDir;
    private Parser $parser;

    protected function setUp(): void
    {
        $this->fixturesDir = __DIR__ . '/fixtures';
        $this->parser = new Parser();
    }

    public function testParseValidJsonFile(): void
    {
        $file = $this->fixturesDir . '/file1.json';

        $result = $this->parser->parseFile($file);

        $expected = [
            'host' => 'hexlet.io',
            'timeout' => 50,
            'proxy' => '123.234.53.22',
            'follow' => false,
        ];

        $this->assertSame($expected, $result);
    }

    public function testParseValidYamlFile(): void
    {
        $file = $this->fixturesDir . '/file1.yaml';

        $result = $this->parser->parseFile($file);

        $expected = [
            'host' => 'hexlet.io',
            'timeout' => 50,
            'proxy' => '123.234.53.22',
            'follow' => false,
        ];

        $this->assertSame($expected, $result);
    }

    public function testParseValidYmlFile(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'gendiff_yml_') . '.yml';
        file_put_contents($tempFile, "host: hexlet.io\ntimeout: 50\n");

        try {
            $result = $this->parser->parseFile($tempFile);
            $this->assertSame([
                'host' => 'hexlet.io',
                'timeout' => 50,
            ], $result);
        } finally {
            @unlink($tempFile);
        }
    }

    public function testParseNonExistingFileThrowsException(): void
    {
        $file = $this->fixturesDir . '/non-existing.json';

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage("Файл не существует");

        $this->parser->parseFile($file);
    }

    public function testUnsupportedExtensionThrowsException(): void
    {
        $file = $this->fixturesDir . '/unsupported.txt';

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage("Неподдерживаемый формат");

        $this->parser->parseFile($file);
    }

    public function testInvalidJsonThrowsException(): void
    {
        $file = $this->fixturesDir . '/invalid.json';

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage("Ошибка парсинга JSON");

        $this->parser->parseFile($file);
    }

    public function testParseFileHandlesBom(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'gendiff_bom_') . '.json';
        $content = "\xEF\xBB\xBF" . json_encode(['key' => 'value']);
        file_put_contents($tempFile, $content);

        try {
            $result = $this->parser->parseFile($tempFile);
            $this->assertSame(['key' => 'value'], $result);
        } finally {
            @unlink($tempFile);
        }
    }

    public function testParseFileHandlesUtf16LeBom(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'gendiff_utf16_') . '.json';
        $utf8Json = (string) json_encode(['key' => 'value', 'num' => 1]);
        $utf16Le = "\xFF\xFE" . mb_convert_encoding($utf8Json, 'UTF-16LE', 'UTF-8');
        file_put_contents($tempFile, $utf16Le);

        try {
            $result = $this->parser->parseFile($tempFile);
            $this->assertSame(['key' => 'value', 'num' => 1], $result);
        } finally {
            @unlink($tempFile);
        }
    }
}
