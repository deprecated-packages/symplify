<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use PHP_CodeSniffer\Standards\Squiz\Sniffs\Arrays\ArrayDeclarationSniff;
use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer;
<<<<<<< HEAD
=======
use SlevomatCodingStandard\Sniffs\Exceptions\ReferenceThrowableOnlySniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff;
use Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff;
use Symplify\EasyCodingStandard\Configuration\Option;
>>>>>>> 7b3f4a4aa... make use of PHP

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SETS, ['php70', 'php71', 'clean-code', 'symplify', 'common', 'psr12', 'dead-code']);

    $parameters->set(Option::PATHS, ['packages', 'tests']);

<<<<<<< HEAD
    $parameters->set('exclude_files', ['*/Fixture/*', '*/Source/*', 'packages/easy-coding-standard/compiler/build/scoper.inc.php', 'packages/easy-hydrator/tests/Fixture/TypedProperty.php', 'packages/easy-hydrator/tests/TypedPropertiesTest.php']);

    $parameters->set('skip', ['PHP_CodeSniffer\Standards\Squiz\Sniffs\Arrays\ArrayDeclarationSniff' => null, BlankLineAfterOpeningTagFixer::class => null, UnaryOperatorSpacesFixer::class => null, PhpUnitStrictFixer::class => ['packages/easy-coding-standard/tests/Indentation/IndentationTest.php'], 'SlevomatCodingStandard\Sniffs\Exceptions\ReferenceThrowableOnlySniff.ReferencedGeneralException' => ['packages/coding-standard/src/Rules/NoDefaultExceptionRule.php'], 'SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff.MissingNativeTypeHint' => ['*Sniff.php', '*YamlFileLoader.php', 'packages/package-builder/src/Reflection/PrivatesCaller.php'], 'Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff.Found' => ['packages/latte-to-twig-converter/src/CaseConverter/*CaseConverter.php']]);
=======
    $parameters->set(Option::EXCLUDE_PATHS, [
        '*/Fixture/*',
        '*/Source/*',
        'packages/easy-coding-standard/compiler/build/scoper.inc.php',
        'packages/easy-hydrator/tests/Fixture/TypedProperty.php',
        'packages/easy-hydrator/tests/TypedPropertiesTest.php',
    ]);

    $parameters->set(Option::SKIP, [
        ArrayDeclarationSniff::class => null, BlankLineAfterOpeningTagFixer::class => null,
        UnaryOperatorSpacesFixer::class => null,
        PhpUnitStrictFixer::class => ['packages/easy-coding-standard/tests/Indentation/IndentationTest.php'],
        ReferenceThrowableOnlySniff::class . '.ReferencedGeneralException' =>  [
            'packages/coding-standard/src/Rules/NoDefaultExceptionRule.php'
        ],
        ParameterTypeHintSniff::class . '.MissingNativeTypeHint' => [
            '*Sniff.php',
            '*YamlFileLoader.php',
            'packages/package-builder/src/Reflection/PrivatesCaller.php',
        ],
        CommentedOutCodeSniff::class => ['packages/latte-to-twig-converter/src/CaseConverter/*CaseConverter.php']
    ]);
>>>>>>> 7b3f4a4aa... make use of PHP
};
