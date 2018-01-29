<?php declare(strict_types=1);

$possibleAutoloadPaths = [
    // composer create-project
    __DIR__ . '/../../../autoload.php',
    // composer require
    __DIR__ . '/../vendor/autoload.php',
    // mono-repository
    __DIR__ . '/../../../vendor/autoload.php',
];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (is_file($possibleAutoloadPath)) {
        require_once $possibleAutoloadPath;

        break;
    }
}
