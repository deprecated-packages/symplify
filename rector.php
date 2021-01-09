<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Rector\CodingStyle\Rector\MethodCall\PreferThisOrSelfMethodCallRector;
use Rector\Core\Configuration\Option;
use Rector\DeadCode\Rector\Class_\RemoveUnusedClassesRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Privatization\Rector\ClassMethod\PrivatizeLocalOnlyMethodRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(StringClassNameToClassConstantRector::class)
        ->call('configure', [[
            StringClassNameToClassConstantRector::CLASSES_TO_SKIP => [
                'Error',
                'Exception',
                'Doctrine\ORM\EntityManagerInterface',
                'Doctrine\ORM\EntityManager',
            ],
        ]]);

    $services->set(PreferThisOrSelfMethodCallRector::class)
        ->call('configure', [[
            PreferThisOrSelfMethodCallRector::TYPE_TO_PREFERENCE => [
                TestCase::class => PreferThisOrSelfMethodCallRector::PREFER_THIS,
            ],
        ]]);

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
    $parameters->set(Option::AUTOLOAD_PATHS, [__DIR__ . '/tests/bootstrap.php', __DIR__ . '/ecs.php']);

    $parameters->set(Option::SETS, [
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::CODING_STYLE,
        SetList::PHP_54,
        SetList::PHP_55,
        SetList::PHP_56,
        SetList::PHP_70,
        SetList::PHP_71,
        SetList::PHP_72,
        SetList::PHP_73,
        SetList::TYPE_DECLARATION,
        SetList::PHPUNIT_CODE_QUALITY,
        SetList::NAMING,
        SetList::PRIVATIZATION,
        // enable later
        // SetList::DEAD_CLASSES,
        SetList::EARLY_RETURN,
    ]);

    $parameters->set(Option::PATHS, [__DIR__ . '/packages']);

    $parameters->set(Option::SKIP, [
        '*/scoper.inc.php',
        '*/vendor/*',
        '*/init/*',
        '*/Source/*',
        '*/Fixture/*',
        '*/ChangedFilesDetectorSource/*',
        __DIR__ . '/packages/monorepo-builder/packages/init/templates',

        // many false positives related to file class autoload
        __DIR__ . '/packages/easy-coding-standard/bin/ecs.php',

        # tests
        __DIR__ . '/packages/vendor-patches/tests/Finder/VendorFilesFinderSource/Vendor/some/package/src/PackageClass.php',

        PrivatizeLocalOnlyMethodRector::class => [
            // @api + used in test
            __DIR__ . '/packages/autodiscovery/src/Discovery.php',
            __DIR__ . '/packages/flex-loader/src/Flex/FlexLoader.php',
            __DIR__ . '/packages/symfony-static-dumper/tests/test_project/src/HttpKernel/TestSymfonyStaticDumperKernel.php',
            __DIR__ . '/packages/phpstan-rules/tests/Rules/ForbiddenArrayWithStringKeysRule/FixturePhp80/SkipAttributeArrayKey.php',
        ],

        RemoveUnusedClassesRector::class => [
            __DIR__ . '/packages/easy-coding-standard/packages/changed-files-detector/tests/FileHashComputerSource/ChangedScannedClass.php',
            __DIR__ . '/packages/easy-coding-standard/packages/changed-files-detector/tests/FileHashComputerSource/SomeScannedClass.php',
            __DIR__ . '/packages/easy-coding-standard/packages/sniff-runner/tests/Application/FixerSource/SomeFile.php',
            __DIR__ . '/packages/phpstan-rules/tests/Rules/ForbiddenArrayWithStringKeysRule/FixturePhp80/SkipAttributeArrayKey.php',
            __DIR__ . '/packages/template-checker/tests/SomeBundle/RealClassBundle.php',
            __DIR__ . '/packages/easy-coding-standard/packages/sniff-runner/tests/Error/ErrorSorterSource/SomeFile.php',
        ],

        __DIR__ . '/packages/sniffer-fixer-to-ecs-converter/stubs/Sniff.php',
    ]);
};
