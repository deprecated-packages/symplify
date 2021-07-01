<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs as Generic;
use PHP_CodeSniffer\Standards\PEAR\Sniffs as PEAR;
use PHP_CodeSniffer\Standards\PSR2\Sniffs as PSR2;
use PHP_CodeSniffer\Standards\Squiz\Sniffs as Squiz;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $skip = [];

    ////////////////////////////////////////////////////////////////////////////////
    // PSR-1
    ////////////////////////////////////////////////////////////////////////////////
    // 2. Files

    // 2.1 PHP Tags
    $services->set(Generic\PHP\DisallowAlternativePHPTagsSniff::class);
    $services->set(Generic\PHP\DisallowShortOpenTagSniff::class);
    $skip[Generic\PHP\DisallowShortOpenTagSniff::class . '.EchoFound'] = null;

    // 2.2 Character Encoding
    $services->set(Generic\Files\ByteOrderMarkSniff::class);

    // 3. Namespace and Class Names
    $services->set(Squiz\Classes\ValidClassNameSniff::class);

    // 4. Class Constants, Properties, and Methods

    // 4.1. Constants
    $services->set(Generic\NamingConventions\UpperCaseConstantNameSniff::class);

    ////////////////////////////////////////////////////////////////////////////////
    // PSR-12
    ////////////////////////////////////////////////////////////////////////////////

    // 2. General

    // 2.2 Files

    $services->set(Generic\Files\LineEndingsSniff::class)
             ->property('eolChar', '\\n');

    $services->set(PSR2\Files\EndFileNewlineSniff::class);

    /* The closing ?> tag MUST be omitted from files containing only PHP. */
    $services->set(PSR2\Files\ClosingTagSniff::class);

    // 2.3 Lines

    $services->set(Generic\Files\LineLengthSniff::class)
             ->property('lineLimit', 120)
             ->property('absoluteLineLimit', 0);

    $services->set(Squiz\WhiteSpace\SuperfluousWhitespaceSniff::class);
    $skip[Squiz\WhiteSpace\SuperfluousWhitespaceSniff::class . '.StartFile'] = null;
    $skip[Squiz\WhiteSpace\SuperfluousWhitespaceSniff::class . '.EndFile'] = null;
    $skip[Squiz\WhiteSpace\SuperfluousWhitespaceSniff::class . '.EmptyLines'] = null;

    $services->set(Generic\Formatting\DisallowMultipleStatementsSniff::class);

    // 2.4 Indenting

    $services->set(Generic\WhiteSpace\ScopeIndentSniff::class)
             ->property('ignoreIndentationTokens', [
                 'T_COMMENT',
                 'T_DOC_COMMENT_OPEN_TAG',
             ]);
    $services->set(Generic\WhiteSpace\DisallowTabIndentSniff::class);

    // 2.5 Keywords and Types

    $services->set(Generic\PHP\LowerCaseKeywordSniff::class);
    $services->set(Generic\PHP\LowerCaseConstantSniff::class);
    $services->set(Generic\PHP\LowerCaseTypeSniff::class);

    // 4. Classes, Properties, and Methods

    // 4.1 Extends and Implements
    $services->set(PSR2\Classes\ClassDeclarationSniff::class);

    // 4.3 Properties and Constants
    $services->set(PSR2\Classes\PropertyDeclarationSniff::class);

    // 4.4 Methods and Functions
    $services->set(Squiz\Scope\MethodScopeSniff::class);
    $services->set(Squiz\WhiteSpace\ScopeKeywordSpacingSniff::class);
    $services->set(PSR2\Methods\MethodDeclarationSniff::class);
    $services->set(PSR2\Methods\FunctionClosingBraceSniff::class);
    $services->set(Squiz\Functions\FunctionDeclarationSniff::class);
    $services->set(Squiz\Functions\LowercaseFunctionKeywordsSniff::class);

    // 4.5 Method and Function Arguments
    $services->set(Squiz\Functions\FunctionDeclarationArgumentSpacingSniff::class)
             ->property('equalsSpacing', 1);
    $services->set(PEAR\Functions\ValidDefaultValueSniff::class);
    $services->set(Squiz\Functions\MultiLineFunctionDeclarationSniff::class);

    // 4.7 Method and Function Calls
    $services->set(Generic\Functions\FunctionCallArgumentSpacingSniff::class);
    $services->set(PSR2\Methods\FunctionCallSignatureSniff::class);
    $skip[PSR2\Methods\FunctionCallSignatureSniff::class . '.SpaceAfterCloseBracket'] = null;
    $skip[PSR2\Methods\FunctionCallSignatureSniff::class . '.OpeningIndent'] = null;

    // 5. Control Structures

    $services->set(Squiz\ControlStructures\ControlSignatureSniff::class);
    #$services->set(Squiz\WhiteSpace\ControlStructureSpacing::class . '.SpacingAfterOpen');
    #$services->set(Squiz\WhiteSpace\ControlStructureSpacing::class . '.SpacingBeforeClose');
    $services->set(Squiz\WhiteSpace\ScopeClosingBraceSniff::class);
    $services->set(Squiz\ControlStructures\ForEachLoopDeclarationSniff::class);

    $services->set(Squiz\ControlStructures\ForLoopDeclarationSniff::class)
             ->property('ignoreNewlines', \true);
    $skip[Squiz\ControlStructures\ForLoopDeclarationSniff::class . '.SpacingAfterOpen'] = null;
    $skip[Squiz\ControlStructures\ForLoopDeclarationSniff::class . '.SpacingBeforeClose'] = null;

    $services->set(Squiz\ControlStructures\LowercaseDeclarationSniff::class);
    $services->set(Generic\ControlStructures\InlineControlStructureSniff::class);
    $skip[Squiz\ControlStructures\ForEachLoopDeclarationSniff::class . '.AsNotLower'] = null;

    // 5.1 if, elseif, else
    $services->set(PSR2\ControlStructures\ElseIfDeclarationSniff::class);

    // 5.2 switch, case
    $services->set(PSR2\ControlStructures\SwitchDeclarationSniff::class);

    // 5.5 foreach
    $skip[Squiz\ControlStructures\ForEachLoopDeclaration::class . '.SpaceAfterOpen'] = null;
    $skip[Squiz\ControlStructures\ForEachLoopDeclaration::class . '.SpaceBeforeClose'] = null;

    // 6. Operators

    // 6.1. Unary operators
    $services->set(Generic\WhiteSpace\IncrementDecrementSpacingSniff::class);
    $services->set(Squiz\WhiteSpace\CastSpacingSniff::class);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SKIP, $skip);
};
