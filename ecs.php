<?php

declare(strict_types=1);

use SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff;
use Symplify\CodingStandard\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(LineLengthFixer::class);
    $ecsConfig->rule(ParameterTypeHintSniff::class);
    $ecsConfig->rule(ReturnTypeHintSniff::class);

    $ecsConfig->ruleWithConfiguration(DoctrineAnnotationNestedBracketsFixer::class, [
        DoctrineAnnotationNestedBracketsFixer::ANNOTATION_CLASSES => ['Doctrine\ORM\JoinColumns'],
    ]);

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
    ]);

    $ecsConfig->skip([
        // paths to skip
        '*/Fixture/*',
        '*/Source/*',

        // PHP 8 only
        __DIR__ . '/packages/phpstan-rules/tests/Rules/ForbiddenArrayWithStringKeysRule/FixturePhp80/SkipAttributeArrayKey.php',

        // slevomat cs
        ParameterTypeHintSniff::class => [
            // break parent contract
            __DIR__ . '/packages/easy-coding-standard/packages/SniffRunner/ValueObject/File.php',
        ],
        ReturnTypeHintSniff::class => [
            // break parent contract
            __DIR__ . '/packages/easy-coding-standard/packages/SniffRunner/ValueObject/File.php',
            // returned null
            '*Visitor.php',
        ],
        ParameterTypeHintSniff::class . '.MissingNativeTypeHint' => [
            // breaks interface contract
            __DIR__ . '/packages/config-transformer/src/DependencyInjection/Loader/IdAwareXmlFileLoader.php',
        ],
    ]);
};
