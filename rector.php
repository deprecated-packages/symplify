<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Rector\CodeQuality\Rector\Array_\CallableThisArrayToAnonymousFunctionRector;
use Rector\CodeQuality\Rector\Foreach_\UnusedForeachValueToArrayKeysRector;
use Rector\CodingStyle\Enum\PreferenceSelfThis;
use Rector\CodingStyle\Rector\ClassMethod\UnSpreadOperatorRector;
use Rector\CodingStyle\Rector\MethodCall\PreferThisOrSelfMethodCallRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector;
use Rector\Core\Configuration\Option;
use Rector\DeadCode\Rector\If_\RemoveDeadInstanceOfRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchExprVariableRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\ParamTypeByMethodCallTypeRector;
use Rector\TypeDeclaration\Rector\FunctionLike\ParamTypeDeclarationRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

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
        ->call('configure', [[
            StringClassNameToClassConstantRector::CLASSES_TO_SKIP => [
                'Error',
                'Exception',
                'Dibi\Connection',
                'Doctrine\ORM\EntityManagerInterface',
                'Doctrine\ORM\EntityManager',
                'Nette\*',
            ],
        ]]);

    $services->set(PreferThisOrSelfMethodCallRector::class)
        ->call('configure', [[
            PreferThisOrSelfMethodCallRector::TYPE_TO_PREFERENCE => [
                TestCase::class => ValueObjectInliner::inline(PreferenceSelfThis::PREFER_THIS()),
            ],
        ]]);

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
    $parameters->set(Option::AUTOLOAD_PATHS, [__DIR__ . '/tests/bootstrap.php', __DIR__ . '/ecs.php']);

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
        __DIR__ . '/packages/easy-coding-standard/build/build-preload.php',
        // test fixtures
        '*/packages/phpstan-extensions/tests/TypeExtension/*/*Extension/data/*',

        // many false positives related to file class autoload
        __DIR__ . '/packages/easy-coding-standard/bin/ecs.php',

        # tests
        __DIR__ . '/packages/vendor-patches/tests',

        // many false postivies - @todo fix in Rector!
        RenameForeachValueVariableToMatchExprVariableRector::class,

        StringClassNameToClassConstantRector::class => [
            // for prefixed version skip
            __DIR__ . '/packages/php-config-printer/src/PhpParser/NodeFactory/ConfiguratorClosureNodeFactory.php',
        ],

        // variadics issues
        ParamTypeByMethodCallTypeRector::class => [__DIR__ . '/packages/git-wrapper/src/GitWorkingCopy.php'],

        // on purpose Latte macro magic
        SymplifyQuoteEscapeRector::class => [
            __DIR__ . '/packages/latte-phpstan-compiler/src/Latte/Macros/LatteMacroFaker.php',
        ],

        // buggy on array access object
        UnusedForeachValueToArrayKeysRector::class => [
            __DIR__ . '/packages/coding-standard/src/Fixer/Annotation/DoctrineAnnotationNestedBracketsFixer.php',
        ],

        // buggy with parent interface contract
        ParamTypeDeclarationRector::class => [__DIR__ . '/packages/skipper/src/SkipVoter/*SkipVoter.php'],
        UnSpreadOperatorRector::class => [
            __DIR__ . '/packages/git-wrapper',
            '*/packages/phpstan-extensions/tests/TypeExtension/*/*TypeExtension/*TypeExtensionTest.php',
        ],

        CallableThisArrayToAnonymousFunctionRector::class => [
            // not a callable, accidental array
            __DIR__ . '/packages/phpstan-rules/src/Rules/',
        ],

        // protected param rename to create miss-matching param names with parent class method
        // @see https://github.com/symplify/symplify/pull/3429 - paths must be relative, so child process in specific directory skips it
        RenameParamToMatchTypeRector::class => [
            'src/Php/Type/NativeFunctionDynamicFunctionReturnTypeExtension.php',
            'src/Printer/PhpParserPhpConfigPrinter.php',
            'src/DependencyInjection/Loader/IdAwareXmlFileLoader.php',
        ],

        // something broken on array of string and method
        __DIR__ . '/packages/phpstan-rules/src/Rules/PreferredMethodCallOverFuncCallRule.php',

        // conflicting with php-parser 4.13- in phpstan for now
        RemoveDeadInstanceOfRector::class,
    ]);
};
