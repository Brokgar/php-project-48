<?php

namespace Hexlet\Gendiff;

use Hexlet\Gendiff\Exception\FileNotFoundException;

class FileReader
{
    public function resolvePath(string $path): string
    {
        $resolved = realpath($path);
        if ($resolved !== false) {
            return $resolved;
        }

        $resolved = realpath(getcwd() . DIRECTORY_SEPARATOR . $path);

        return $resolved !== false ? $resolved : $path;
    }

    public function read(string $filePath, ?string $sourcePath = null): string
    {
        if (!file_exists($filePath)) {
            throw new FileNotFoundException("Файл не существует");
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            $pathForMessage = $sourcePath ?? $filePath;
            throw new FileNotFoundException("Не удалось прочитать файл: $pathForMessage");
        }

        return $content;
    }
}
