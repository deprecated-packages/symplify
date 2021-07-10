<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(LineLengthFixer::class);

    $services->set(DoctrineAnnotationNestedBracketsFixer::class)
        ->call('configure', [[
            DoctrineAnnotationNestedBracketsFixer::ANNOTATION_CLASSES => ['Doctrine\ORM\JoinColumns'],
        ]]);

    $containerConfigurator->import(SetList::CLEAN_CODE);
    $containerConfigurator->import(SetList::SYMPLIFY);
    $containerConfigurator->import(SetList::COMMON);
    $containerConfigurator->import(SetList::PSR_12);
    $containerConfigurator->import(SetList::DOCTRINE_ANNOTATIONS);

    $parameters = $containerConfigurator->parameters();

    // experimental
    $parameters->set(Option::PARALLEL, false);

    $parameters->set(Option::PATHS, [
        __DIR__ . '/packages',
        __DIR__ . '/tests',
        __DIR__ . '/ecs.php',
        __DIR__ . '/monorepo-builder.php',
        __DIR__ . '/rector.php',
    ]);

    $parameters->set(Option::SKIP, [
        // paths to skip
        '*/Fixture/*',
        '*/Source/*',
        __DIR__ . '/packages/easy-hydrator/tests/Fixture/TypedProperty.php',
        __DIR__ . '/packages/easy-hydrator/tests/TypedPropertiesTest.php',

        // PHP 8 only
        __DIR__ . '/packages/phpstan-rules/tests/Rules/ForbiddenArrayWithStringKeysRule/FixturePhp80/SkipAttributeArrayKey.php',
        __DIR__ . '/packages/phpstan-rules/tests/Rules/TooDeepNewClassNestingRule/FixturePhp8/SkipExpressionThrow.php',

        // class in paths
        PhpUnitStrictFixer::class => [
            __DIR__ . '/packages/easy-coding-standard/tests/Indentation/IndentationTest.php',
        ],
    ]);
};
