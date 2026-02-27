<?php

namespace Hexlet\Gendiff;

use Hexlet\Gendiff\Exception\FileNotFoundException;
use Hexlet\Gendiff\Exception\ParseException;
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

        // Удаляем BOM, если присутствует
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }

        $extension = pathinfo($absolutePath, PATHINFO_EXTENSION);
        $extension = strtolower($extension);
        if ($extension !== 'json' && $extension !== 'yaml') {
            throw new ParseException("Неподдерживаемый формат");
        }

        if ($extension == 'json') {
            $data = json_decode($content, true);
        } elseif ($extension == 'yaml') {
            $data = Yaml::parse($content);
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ParseException(
                "Ошибка парсинга JSON" .
                json_last_error_msg()
            );
        }

        return $data;
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