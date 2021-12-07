<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Rector\CodingStyle\Enum\PreferenceSelfThis;
use Rector\CodingStyle\Rector\ClassMethod\UnSpreadOperatorRector;
use Rector\CodingStyle\Rector\MethodCall\PreferThisOrSelfMethodCallRector;
use Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector;
use Rector\Core\Configuration\Option;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(SetList::CODE_QUALITY);
    $containerConfigurator->import(SetList::DEAD_CODE);

    $containerConfigurator->import(LevelSetList::UP_TO_PHP_80);
    $containerConfigurator->import(SetList::CODING_STYLE);
    $containerConfigurator->import(SetList::TYPE_DECLARATION);
    $containerConfigurator->import(SetList::TYPE_DECLARATION_STRICT);
    $containerConfigurator->import(SetList::NAMING);
    $containerConfigurator->import(SetList::PRIVATIZATION);
    $containerConfigurator->import(SetList::EARLY_RETURN);
    $containerConfigurator->import(PHPUnitSetList::PHPUNIT_CODE_QUALITY);

    $services = $containerConfigurator->services();

    $services->set(StringClassNameToClassConstantRector::class)
        ->configure([
            'Error',
            'Exception',
            'Dibi\Connection',
            'Doctrine\ORM\EntityManagerInterface',
            'Doctrine\ORM\EntityManager',
            'Nette\*',
            'Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator',
        ]);

    $services->set(PreferThisOrSelfMethodCallRector::class)
        ->configure([
            TestCase::class => PreferenceSelfThis::PREFER_THIS(),
        ]);

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
    $parameters->set(Option::AUTOLOAD_PATHS, [__DIR__ . '/tests/bootstrap.php']);

    $parameters->set(Option::PATHS, [__DIR__ . '/packages']);

    $parameters->set(Option::SKIP, [
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

        // on purpose Latte macro magic
        SymplifyQuoteEscapeRector::class => [
            __DIR__ . '/packages/latte-phpstan-compiler/src/Latte/Macros/LatteMacroFaker.php',
        ],

        // adopted 3rd party package - keep API compatible
        UnSpreadOperatorRector::class => [__DIR__ . '/packages/git-wrapper'],
    ]);
};
