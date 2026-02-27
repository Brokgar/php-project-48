<?php

namespace Hexlet\Gendiff;

use Hexlet\Gendiff\Parser;
use Hexlet\Gendiff\Utils\ArrayComparator;
use Hexlet\Gendiff\Formatters;

class Gendiff
{
    // ANSI цветовые коды
    private const COLOR_GREEN = "\033[32m";
    private const COLOR_RED = "\033[31m";
    private const COLOR_YELLOW = "\033[33m";
    private const COLOR_CYAN = "\033[36m";
    private const COLOR_GRAY = "\033[90m";
    private const COLOR_RESET = "\033[0m";

    
    public static function compareFiles(string $file1, string $file2, array $options = []): string
    {
        $data1 = Parser::parseFile($file1);
        $data2 = Parser::parseFile($file2);

        $format = $options['format'] ?? 'stylish';
        $color = $options['color'] ?? false;

        $diff = ArrayComparator::compare($data1, $data2);

        $result = Formatters::format($diff, $format);

        if ($color) {
            $result = self::applyColor($result, $format);
        }

        return $result;
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
                    $coloredLines[] = self::COLOR_GREEN . $line . self::COLOR_RESET; // Зелёный
                } elseif (str_contains($line, 'was removed')) {
                    $coloredLines[] = self::COLOR_RED . $line . self::COLOR_RESET; // Красный
                } elseif (str_contains($line, 'changed')) {
                    $coloredLines[] = self::COLOR_YELLOW . $line . self::COLOR_RESET; // Жёлтый
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
                $coloredLines[] = self::COLOR_GREEN . $line . self::COLOR_RESET; // Зелёный
            } elseif (str_starts_with(trim($line), '-')) {
                // Удалённые свойства — красный
                $coloredLines[] = self::COLOR_RED . $line . self::COLOR_RESET; // Красный
            } elseif (str_starts_with(trim($line), '~')) {
                // Изменённые свойства — жёлтый
                $coloredLines[] = self::COLOR_YELLOW . $line . self::COLOR_RESET; // Жёлтый
            } elseif (str_starts_with(trim($line), '{') || str_starts_with(trim($line), '}')) {
                // Фигурные скобки — серый
                $coloredLines[] = self::COLOR_GRAY . $line . self::COLOR_RESET;
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
        return self::COLOR_CYAN . $diff . self::COLOR_RESET; // Голубой для JSON
    }
}
