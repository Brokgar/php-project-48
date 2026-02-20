<?php

namespace Hexlet\Gendiff;

class Parser
{
    public static function parseFile(string $filePath): array
    {
        $absolutePath = self::normalizePath($filePath);

        if (!file_exists($absolutePath)) {
            throw new \Exception("Файл '$filePath' не существует");
        }

        $content = file_get_contents($absolutePath);
        if ($content === false) {
            throw new \Exception("Не удалось прочитать файл: $filePath");
        }

        // Удаляем BOM, если присутствует
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }

        $extension = pathinfo($absolutePath, PATHINFO_EXTENSION);
        if (strtolower($extension) !== 'json') {
            throw new \Exception("Неподдерживаемый формат: $extension");
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(
                "Ошибка парсинга JSON в файле '$filePath': " .
                json_last_error_msg() .
                "\nСодержимое файла (первые 200 символов): " .
                substr($content, 0, 200)
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
?>
