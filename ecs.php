<?php

declare(strict_types=1);

use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(LineLengthFixer::class);

    $ecsConfig->sets([
        SetList::CLEAN_CODE,
        SetList::SYMPLIFY,
        SetList::COMMON,
        SetList::PSR_12,
        SetList::DOCTRINE_ANNOTATIONS,
    ]);

    $ecsConfig->paths([
        __DIR__ . '/packages',
        __DIR__ . '/tests',
        __DIR__ . '/ecs.php',
        __DIR__ . '/monorepo-builder.php',
        __DIR__ . '/rector.php',
        __DIR__ . '/composer-unused.php',
    ]);

    $ecsConfig->skip([
        // paths to skip
        '*/Fixture/*',
        '*/Source/*',

        // PHP 8 only
        __DIR__ . '/packages/phpstan-rules/tests/Rules/ForbiddenArrayWithStringKeysRule/FixturePhp80/SkipAttributeArrayKey.php',
    ]);
};
