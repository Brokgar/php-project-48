<?php

namespace Hexlet\Code;


class Parser
{
    /**
     * Парсит файл и возвращает его содержимое в виде массива
     *
     * @param string $filePath Путь к файлу (относительный или абсолютный)
     * @return array Содержимое файла как ассоциативный массив
     * @throws Exception Если файл не существует, не читается или имеет неверный формат
     */
    public static function parseFile(string $filePath): array
    {
        // Нормализуем путь — преобразуем относительный в абсолютный
        $absolutePath = self::normalizePath($filePath);

        if (!file_exists($absolutePath)) {
            throw new \Exception("Файл '$filePath' (абсолютный путь: '$absolutePath') не существует");
        }

        if (!is_readable($absolutePath)) {
            throw new \Exception("Файл '$filePath' (абсолютный путь: '$absolutePath') недоступен для чтения");
        }

        $fileContent = file_get_contents($absolutePath);
        if ($fileContent === false) {
            throw new \Exception("Не удалось прочитать файл '$filePath'");
        }

        // Печать содержимого файла на экран
        self::printFileContent($filePath, $absolutePath, $fileContent);

        $extension = pathinfo($absolutePath, PATHINFO_EXTENSION);

        if (strtolower($extension) !== 'json') {
            throw new \Exception("Неподдерживаемый формат файла: $extension. Поддерживается только JSON");
        }

        return self::parseJson($fileContent);
    }

    /**
     * Печатает содержимое файла на экран с форматированием
     *
     * @param string $originalPath Исходный путь к файлу
     * @param string $absolutePath Абсолютный путь к файлу
     * @param string $content Содержимое файла
     */
    private static function printFileContent(string $originalPath, string $absolutePath, string $content): void
    {
        echo "Содержимое файла '$originalPath' (путь: '$absolutePath'):\n";
        echo str_repeat("-", 60) . "\n";
        echo $content . "\n";
        echo str_repeat("=", 60) . "\n\n"; // Разделитель между файлами
    }

    /**
     * Нормализует путь — преобразует относительный путь в абсолютный
     *
     * @param string $path Исходный путь (относительный или абсолютный)
     * @return string Абсолютный путь
     */
    private static function normalizePath(string $path): string
    {
        // Если путь уже абсолютный, возвращаем его как есть
        if (self::isAbsolutePath($path)) {
            return $path;
        }

        // Преобразуем относительный путь в абсолютный относительно текущей рабочей директории
        return realpath(getcwd() . DIRECTORY_SEPARATOR . $path) ?: $path;
    }

    /**
     * Проверяет, является ли путь абсолютным
     *
     * @param string $path Путь для проверки
     * @return bool true, если путь абсолютный
     */
    private static function isAbsolutePath(string $path): bool
    {
        // Для Unix‑систем: путь начинается с /
        if (DIRECTORY_SEPARATOR === '/' && strpos($path, '/') === 0) {
            return true;
        }

        // Для Windows: путь начинается с буквы диска и двоеточия (C:\) или с \\
        if (DIRECTORY_SEPARATOR === '\\') {
            if (preg_match('~^[A-Z]:~i', $path)) { // C:\, D:\ и т. д.
                return true;
            }
            if (strpos($path, '\\\\') === 0) { // \\server\share
                return true;
            }
        }

        return false;
    }

    /**
     * Парсит JSON‑строку
     *
     * @param string $jsonContent Содержимое JSON‑файла
     * @return array Данные в виде массива
     * @throws Exception Если JSON некорректен
     */
    private static function parseJson(string $jsonContent): array
    {
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Ошибка парсинга JSON: " . json_last_error_msg());
        }

        return $data;
    }
}
?>
