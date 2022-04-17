<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Rector\CodingStyle\Enum\PreferenceSelfThis;
use Rector\CodingStyle\Rector\ClassConst\VarConstantCommentRector;
use Rector\CodingStyle\Rector\ClassMethod\UnSpreadOperatorRector;
use Rector\CodingStyle\Rector\MethodCall\PreferThisOrSelfMethodCallRector;
use Rector\Config\RectorConfig;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        LevelSetList::UP_TO_PHP_80,
        SetList::CODING_STYLE,
        SetList::TYPE_DECLARATION,
        SetList::TYPE_DECLARATION_STRICT,
        SetList::NAMING,
        SetList::PRIVATIZATION,
        SetList::EARLY_RETURN,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
    ]);

    $rectorConfig->ruleWithConfiguration(StringClassNameToClassConstantRector::class, [
        'Error',
        'Exception',
        'Dibi\Connection',
        'Doctrine\ORM\EntityManagerInterface',
        'Doctrine\ORM\EntityManager',
        'Nette\*',
        'Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator',
        'PHPUnit\Framework\TestCase',
        'Symplify\EasyCodingStandard\Config\ECSConfig',
        'Rector\Config\RectorConfig',
    ]);

    $rectorConfig->ruleWithConfiguration(PreferThisOrSelfMethodCallRector::class, [
        TestCase::class => PreferenceSelfThis::PREFER_THIS(),
    ]);

    $rectorConfig->paths([__DIR__ . '/packages']);

    $rectorConfig->parallel();
    $rectorConfig->importNames();

    $rectorConfig->autoloadPaths([__DIR__ . '/tests/bootstrap.php']);

    $rectorConfig->skip([
        '*/scoper.php',
        '*/vendor/*',
        '*/init/*',
        '*/Source/*',
        '*/Fixture/*',
        '*/Fixture*/*',
        '*/ChangedFilesDetectorSource/*',
        __DIR__ . '/packages/monorepo-builder/templates',
        // test fixtures
        '*/packages/phpstan-extensions/tests/TypeExtension/*/*Extension/data/*',
        // many false positives related to file class autoload
        __DIR__ . '/packages/easy-coding-standard/bin/ecs.php',

        // adopted 3rd party package - keep API compatible
        UnSpreadOperatorRector::class => [__DIR__ . '/packages/git-wrapper'],

        // false positive on "locale" string
        VarConstantCommentRector::class => [
            __DIR__ . '/packages/php-config-printer/src/RoutingCaseConverter/ImportRoutingCaseConverter.php',
        ],
    ]);
};
