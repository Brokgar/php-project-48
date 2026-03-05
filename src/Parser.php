<?php

namespace Hexlet\Gendiff;

use Hexlet\Gendiff\Exception\FileNotFoundException;
use Hexlet\Gendiff\Exception\ParseException;
use JsonException;
use Symfony\Component\Yaml\Exception\ParseException as YamlParseException;
use Symfony\Component\Yaml\Yaml;

class Parser
{
    public static function parseFile(string $filePath): array
    {
        $absolutePath = self::normalizePath($filePath);

        if (!file_exists($absolutePath)) {
            throw new FileNotFoundException("Файл не существует");
        }

        $content = file_get_contents($absolutePath);
        if ($content === false) {
            throw new FileNotFoundException("Не удалось прочитать файл: $filePath");
        }

        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }

        $extension = strtolower((string) pathinfo($absolutePath, PATHINFO_EXTENSION));
        if (!in_array($extension, ['json', 'yaml', 'yml'], true)) {
            throw new ParseException("Неподдерживаемый формат");
        }

        if ($extension === 'json') {
            return self::parseJson($content);
        }

        return self::parseYaml($content);
    }

    private static function parseJson(string $content): array
    {
        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ParseException("Ошибка парсинга JSON: {$e->getMessage()}", 0, $e);
        }

        return is_array($data) ? $data : [];
    }

    private static function parseYaml(string $content): array
    {
        try {
            $data = Yaml::parse($content);
        } catch (YamlParseException $e) {
            throw new ParseException("Ошибка парсинга YAML: {$e->getMessage()}", 0, $e);
        }

        return is_array($data) ? $data : [];
    }

    private static function normalizePath(string $path): string
    {
        if (self::isAbsolutePath($path)) {
            return $path;
        }

        return realpath(getcwd() . DIRECTORY_SEPARATOR . $path) ?: $path;
    }

    private static function isAbsolutePath(string $path): bool
    {
        if (DIRECTORY_SEPARATOR === '/' && strpos($path, '/') === 0) {
            return true;
        }

        if (DIRECTORY_SEPARATOR === '\\' && preg_match('~^[A-Z]:~i', $path)) {
            return true;
        }

        return false;
    }
}
