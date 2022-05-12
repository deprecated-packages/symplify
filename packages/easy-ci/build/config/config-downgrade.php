<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DowngradePhp81\Rector\Array_\DowngradeArraySpreadStringKeyRector;
use Rector\Set\ValueObject\DowngradeLevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([DowngradeLevelSetList::DOWN_TO_PHP_72]);

    $rectorConfig->skip([
        '*/Tests/*',
        '*/tests/*',
        __DIR__ . '/../../tests',
        // only one should run, in case of PHP 7.2 the stronger one
        DowngradeArraySpreadStringKeyRector::class,
    ]);
};
