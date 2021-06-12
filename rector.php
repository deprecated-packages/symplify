<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Rector\CodingStyle\Rector\ClassMethod\UnSpreadOperatorRector;
use Rector\CodingStyle\Rector\MethodCall\PreferThisOrSelfMethodCallRector;
use Rector\CodingStyle\ValueObject\PreferenceSelfThis;
use Rector\Core\Configuration\Option;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchExprVariableRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\Rector\ClassMethod\OptionalParametersAfterRequiredRector;
use Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\FunctionLike\ParamTypeDeclarationRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(SetList::CODE_QUALITY);
    $containerConfigurator->import(SetList::CODE_QUALITY_STRICT);
    $containerConfigurator->import(SetList::DEAD_CODE);
    $containerConfigurator->import(SetList::CODING_STYLE);
    $containerConfigurator->import(SetList::PHP_54);
    $containerConfigurator->import(SetList::PHP_55);
    $containerConfigurator->import(SetList::PHP_56);
    $containerConfigurator->import(SetList::PHP_70);
    $containerConfigurator->import(SetList::PHP_71);
    $containerConfigurator->import(SetList::PHP_72);
    $containerConfigurator->import(SetList::PHP_73);
    $containerConfigurator->import(SetList::PHP_74);
    $containerConfigurator->import(SetList::PHP_80);
    $containerConfigurator->import(SetList::TYPE_DECLARATION);
    $containerConfigurator->import(SetList::TYPE_DECLARATION_STRICT);
    $containerConfigurator->import(SetList::NAMING);
    $containerConfigurator->import(SetList::PRIVATIZATION);
    $containerConfigurator->import(SetList::EARLY_RETURN);
    $containerConfigurator->import(PHPUnitSetList::PHPUNIT_CODE_QUALITY);

    $services = $containerConfigurator->services();

    // PHP 8+
    $services->set(ClosureToArrowFunctionRector::class);
    $services->set(ClassPropertyAssignToConstructorPromotionRector::class);
    $services->set(ChangeSwitchToMatchRector::class);
    $services->set(ClassPropertyAssignToConstructorPromotionRector::class);
    $services->set(TypedPropertyRector::class);

    $services->set(StringClassNameToClassConstantRector::class)
        ->call('configure', [[
            StringClassNameToClassConstantRector::CLASSES_TO_SKIP => [
                'Error',
                'Exception',
                'Doctrine\ORM\EntityManagerInterface',
                'Doctrine\ORM\EntityManager',
                'Nette\Application\UI\Template',
                'Nette\DI\Attributes\Inject',
                'Nette\Bridges\ApplicationLatte\Template',
                'Nette\Bridges\ApplicationLatte\DefaultTemplate',
            ],
        ]]);

    $services->set(PreferThisOrSelfMethodCallRector::class)
        ->call('configure', [[
            PreferThisOrSelfMethodCallRector::TYPE_TO_PREFERENCE => [
                TestCase::class => PreferenceSelfThis::PREFER_THIS,
            ],
        ]]);

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
    $parameters->set(Option::AUTOLOAD_PATHS, [__DIR__ . '/tests/bootstrap.php', __DIR__ . '/ecs.php']);

    $parameters->set(Option::PATHS, [__DIR__ . '/packages']);
    $parameters->set(Option::ENABLE_CACHE, true);

    $parameters->set(Option::SKIP, [
        '*/scoper.php',
        '*/vendor/*',
        '*/init/*',
        '*/Source/*',
        '*/Fixture/*',
        '*/Fixture*/*',
        '*/ChangedFilesDetectorSource/*',
        __DIR__ . '/packages/monorepo-builder/templates',
        __DIR__ . '/packages/easy-coding-standard/build/build-preload.php',

        // fix in dev-main
        \Rector\Privatization\Rector\Property\PrivatizeLocalPropertyToPrivatePropertyRector::class => [
            __DIR__ . '/packages/package-builder/src/Console/Command/AbstractSymplifyCommand.php',
        ],

        // many false positives related to file class autoload
        __DIR__ . '/packages/easy-coding-standard/bin/ecs.php',

        # tests
        __DIR__ . '/packages/vendor-patches/tests/Finder/VendorFilesFinderSource/Vendor/some/package/src/PackageClass.php',

        // many false postivies
        RenameForeachValueVariableToMatchExprVariableRector::class,

        // buggy with parent interface contract
        ParamTypeDeclarationRector::class => [__DIR__ . '/packages/skipper/src/SkipVoter/*SkipVoter.php'],

        OptionalParametersAfterRequiredRector::class => [
            // @todo fix in Rector variadics are optional parameter
            __DIR__ . '/packages/git-wrapper',
        ],

        __DIR__ . '/packages/sniffer-fixer-to-ecs-converter/stubs/Sniff.php',

        UnSpreadOperatorRector::class => [__DIR__ . '/packages/git-wrapper'],
    ]);
};
