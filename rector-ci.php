<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Naming\Rector\ClassMethod\MakeIsserClassMethodNameStartWithIsRector;
use Rector\Naming\Rector\Property\MakeBoolPropertyRespectIsHasWasMethodNamingRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Set\ValueObject\SetList;
use Rector\SOLID\Rector\Property\ChangeReadOnlyPropertyWithDefaultValueToConstantRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddArrayParamDocTypeRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ChangeReadOnlyPropertyWithDefaultValueToConstantRector::class);

    $services->set(AddArrayParamDocTypeRector::class);

    $services->set(StringClassNameToClassConstantRector::class)
        ->call('configure', [[
            StringClassNameToClassConstantRector::CLASSES_TO_SKIP => [
                'Error',
                'Exception',
                'Doctrine\ORM\EntityManagerInterface',
                'Doctrine\ORM\EntityManager',
            ]
        ]]);

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
    $parameters->set(Option::AUTOLOAD_PATHS, [__DIR__ . '/tests/bootstrap.php', __DIR__ . '/ecs.php']);

    $parameters->set(Option::SETS, [
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::CODING_STYLE,
        SetList::PHP_70,
        SetList::PHP_71,
        SetList::PHP_72,
        SetList::TYPE_DECLARATION,
        SetList::PHPUNIT_CODE_QUALITY,
        SetList::NAMING,
    ]);

    $parameters->set(Option::PATHS, [__DIR__ . '/packages']);

    $parameters->set(Option::EXCLUDE_RECTORS, [
        // fixed on master 2020-10-16
        MakeBoolPropertyRespectIsHasWasMethodNamingRector::class,
        MakeIsserClassMethodNameStartWithIsRector::class,
    ]);

    $parameters->set(Option::EXCLUDE_PATHS, [
        '*/scoper.inc.php',
        '/vendor/',
        '/init/',
        '/Source/',
        '/Fixture/',
        '/ChangedFilesDetectorSource/',
        # parameter Symfony autowire hack
        __DIR__ . '/packages/changelog-linker/src/DependencyInjection/Dummy/ResolveAutowiringExceptionHelper.php',
        __DIR__ . '/packages/monorepo-builder/packages/init/templates',

        // few dead-code false positives, solve later
        __DIR__ . '/packages/easy-coding-standard/bin/ecs.php'
    ]);
};
