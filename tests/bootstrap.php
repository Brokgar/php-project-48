<?php

declare(strict_types=1);

$autoloadPaths = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
];

$autoloadLoaded = false;
foreach ($autoloadPaths as $autoloadPath) {
    if (is_file($autoloadPath)) {
        require_once $autoloadPath;
        $autoloadLoaded = true;
        break;
    }
}

if (!$autoloadLoaded) {
    fwrite(STDERR, 'Error: composer autoload file not found' . PHP_EOL);
    exit(1);
}

$projectRoot = dirname(__DIR__);
$binDirs = [
    $projectRoot . DIRECTORY_SEPARATOR . 'bin',
    $projectRoot . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'bin',
];

$currentPath = getenv('PATH') ?: '';
$pathSeparator = PATH_SEPARATOR;
$existingEntries = $currentPath === '' ? [] : explode($pathSeparator, $currentPath);
$existingEntriesMap = array_fill_keys($existingEntries, true);

foreach (array_reverse($binDirs) as $binDir) {
    $shouldAdd = match (true) {
        !is_dir($binDir) => false,
        isset($existingEntriesMap[$binDir]) => false,
        default => true,
    };

    if ($shouldAdd) {
        array_unshift($existingEntries, $binDir);
        $existingEntriesMap[$binDir] = true;
    }
}

$newPath = implode($pathSeparator, $existingEntries);
putenv("PATH={$newPath}");
$_SERVER['PATH'] = $newPath;
$_ENV['PATH'] = $newPath;
