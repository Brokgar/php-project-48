<?php

use PHPUnit\Framework\TestCase;
use Hexlet\Gendiff\Parser;

class ParserTest extends TestCase
{
    private string $fixturesDir;

    protected function setUp(): void
    {
        $this->fixturesDir = __DIR__ . '/fixtures';
    }

    public function testParseValidJsonFile(): void
    {
        $file = $this->fixturesDir . '/file1.json';

        $result = Parser::parseFile($file);

        $expected = [
            'host' => 'hexlet.io',
            'timeout' => 50,
            'proxy' => '123.234.53.22',
            'follow' => false,
        ];

        $this->assertSame($expected, $result);
    }

    public function testParseNonExistingFileThrowsException(): void
    {
        $file = $this->fixturesDir . '/non-existing.json';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("не существует");

        Parser::parseFile($file);
    }

    public function testUnsupportedExtensionThrowsException(): void
    {
        $file = $this->fixturesDir . '/unsupported.txt';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Неподдерживаемый формат");

        Parser::parseFile($file);
    }

    public function testInvalidJsonThrowsException(): void
    {
        $file = $this->fixturesDir . '/invalid.json';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Ошибка парсинга JSON");

        Parser::parseFile($file);
    }

    public function testParseFileHandlesBom(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'gendiff_bom_') . '.json';
        $content = "\xEF\xBB\xBF" . json_encode(['key' => 'value']);
        file_put_contents($tempFile, $content);

        try {
            $result = Parser::parseFile($tempFile);
            $this->assertSame(['key' => 'value'], $result);
        } finally {
            @unlink($tempFile);
        }
    }
}

