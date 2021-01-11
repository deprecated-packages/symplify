<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$squizlabsAutoloadFile = __DIR__ . '/../vendor/squizlabs/php_codesniffer/autoload.php';
if (file_exists($squizlabsAutoloadFile)) {
    require_once $squizlabsAutoloadFile;
}
