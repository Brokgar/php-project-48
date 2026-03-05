<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$projectRoot = dirname(__DIR__);
$binDirs = [
    $projectRoot . DIRECTORY_SEPARATOR . 'bin',
    $projectRoot . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'bin',
];

$currentPath = getenv('PATH') ?: '';
$pathSeparator = PATH_SEPARATOR;
$existingEntries = $currentPath === '' ? [] : explode($pathSeparator, $currentPath);

foreach (array_reverse($binDirs) as $binDir) {
    if (is_dir($binDir) && !in_array($binDir, $existingEntries, true)) {
        array_unshift($existingEntries, $binDir);
    }
}

$newPath = implode($pathSeparator, $existingEntries);
putenv("PATH={$newPath}");
$_SERVER['PATH'] = $newPath;
$_ENV['PATH'] = $newPath;
