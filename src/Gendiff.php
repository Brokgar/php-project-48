<?php

namespace Hexlet\Gendiff;

use Hexlet\Gendiff\Parser;
use Hexlet\Gendiff\Generator;

class Gendiff
{
    public static function compareFiles(string $file1, string $file2, array $options = []): string
    {
        $data1 = Parser::parseFile($file1);
        $data2 = Parser::parseFile($file2);

        $format = $options['format'] ?? 'plain';
        $color = $options['color'] ?? false;

        $diff = Generator::generateDiff($data1, $data2, $format);

        if ($color) {
            $diff = self::applyColor($diff, $format);
        }

        return $diff;
    }

    /**
     * Применяет цветовые коды ANSI к выводу в зависимости от формата
     */
    private static function applyColor(string $diff, string $format): string
    {
        // Проверяем, поддерживает ли терминал цвета
        if (!self::supportsColor()) {
            return $diff;
        }

        switch ($format) {
            case 'plain':
                return self::colorizePlainDiff($diff);
            case 'stylish':
                return self::colorizeStylishDiff($diff);
            case 'json':
                return self::colorizeJsonDiff($diff);
            default:
                return $diff;
        }
    }

    /**
     * Проверяет поддержку цветовых кодов ANSI в терминале
     */
    private static function supportsColor(): bool
    {
        // Проверяем переменные окружения
        if (isset($_SERVER['NO_COLOR'])) {
            return false;
        }

        // Проверяем явно заданный вывод в файл/пайп
        if (!function_exists('posix_isatty') || !posix_isatty(STDOUT)) {
            return false;
        }

        // Проверяем TERM
        $term = $_SERVER['TERM'] ?? '';
        if ($term === 'dumb' || strpos($term, 'vt100') !== false) {
            return false;
        }

        return true;
    }

    /**
     * Раскрашивает plain‑формат
     */
    private static function colorizePlainDiff(string $diff): string
    {
        $lines = explode("\n", $diff);
        $coloredLines = [];

        foreach ($lines as $line) {
            if (str_starts_with($line, 'Property')) {
                if (str_contains($line, 'was added')) {
                    $coloredLines[] = "\033[32m" . $line . "\033[0m"; // Зелёный
                } elseif (str_contains($line, 'was removed')) {
                    $coloredLines[] = "\033[31m" . $line . "\033[0m"; // Красный
                } elseif (str_contains($line, 'changed')) {
                    $coloredLines[] = "\033[33m" . $line . "\033[0m"; // Жёлтый
                } else {
                    $coloredLines[] = $line;
                }
            } else {
                $coloredLines[] = $line;
            }
        }

        return implode("\n", $coloredLines);
    }

    /**
     * Раскрашивает stylish‑формат
     */
    private static function colorizeStylishDiff(string $diff): string
    {
        $lines = explode("\n", $diff);
        $coloredLines = [];

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '+')) {
                // Добавленные свойства — зелёный
                $coloredLines[] = "\033[32m" . $line . "\033[0m";
            } elseif (str_starts_with(trim($line), '-')) {
                // Удалённые свойства — красный
                $coloredLines[] = "\033[31m" . $line . "\033[0m";
            } elseif (str_starts_with(trim($line), '~')) {
                // Изменённые свойства — жёлтый
                $coloredLines[] = "\033[33m" . $line . "\033[0m";
            } elseif (str_starts_with(trim($line), '{') || str_starts_with(trim($line), '}')) {
                // Фигурные скобки — серый
                $coloredLines[] = "\033[90m" . $line . "\033[0m";
            } else {
                $coloredLines[] = $line;
            }
        }

        return implode("\n", $coloredLines);
    }

    /**
     * Раскрашивает JSON‑формат (упрощённая версия)
     */
    private static function colorizeJsonDiff(string $diff): string
    {
        return "\033[36m" . $diff . "\033[0m"; // Голубой для JSON
    }
}
