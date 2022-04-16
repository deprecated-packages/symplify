<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\ControlStructures\InlineControlStructureSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\ByteOrderMarkSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\DisallowMultipleStatementsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\FunctionCallArgumentSpacingSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseConstantSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseKeywordSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ScopeIndentSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\Functions\ValidDefaultValueSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\ControlStructureSpacingSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\ElseIfDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\SwitchDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\ClosingTagSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\EndFileNewlineSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\FunctionCallSignatureSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\FunctionClosingBraceSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\MethodDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\NamespaceDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\UseDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ValidClassNameSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures\ControlSignatureSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures\ForEachLoopDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures\ForLoopDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures\LowercaseDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\FunctionDeclarationArgumentSpacingSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\FunctionDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\LowercaseFunctionKeywordsSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\MultiLineFunctionDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Scope\MethodScopeSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\ScopeClosingBraceSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\ScopeKeywordSpacingSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(NamespaceDeclarationSniff::class);
    $ecsConfig->rule(UseDeclarationSniff::class);
    $ecsConfig->rule(ClassDeclarationSniff::class);
    $ecsConfig->rule(PropertyDeclarationSniff::class);
    $ecsConfig->rule(EndFileNewlineSniff::class);
    $ecsConfig->rule(ClosingTagSniff::class);
    $ecsConfig->rule(ControlStructureSpacingSniff::class);
    $ecsConfig->rule(SwitchDeclarationSniff::class);
    $ecsConfig->rule(ElseIfDeclarationSniff::class);
    $ecsConfig->rule(FunctionCallSignatureSniff::class);
    $ecsConfig->rule(MethodDeclarationSniff::class);
    $ecsConfig->rule(FunctionClosingBraceSniff::class);
    $ecsConfig->rule(ByteOrderMarkSniff::class);
    $ecsConfig->rule(ValidClassNameSniff::class);
    $ecsConfig->rule(UpperCaseConstantNameSniff::class);
    $ecsConfig->rule(DisallowMultipleStatementsSniff::class);

    $ecsConfig->ruleWithConfiguration(LineEndingsSniff::class, [
        'eolChar' => '\n',
    ]);

    $ecsConfig->ruleWithConfiguration(SuperfluousWhitespaceSniff::class, [
        'ignoreBlankLines' => true,
    ]);

    $ecsConfig->ruleWithConfiguration(ScopeIndentSniff::class, [
        'ignoreIndentationTokens' => ['T_COMMENT', 'T_DOC_COMMENT_OPEN_TAG'],
    ]);

    $ecsConfig->rule(DisallowTabIndentSniff::class);
    $ecsConfig->rule(LowerCaseKeywordSniff::class);
    $ecsConfig->rule(LowerCaseConstantSniff::class);
    $ecsConfig->rule(MethodScopeSniff::class);
    $ecsConfig->rule(ScopeKeywordSpacingSniff::class);
    $ecsConfig->rule(FunctionDeclarationSniff::class);
    $ecsConfig->rule(LowercaseFunctionKeywordsSniff::class);

    $ecsConfig->ruleWithConfiguration(FunctionDeclarationArgumentSpacingSniff::class, [
        'equalsSpacing' => 1,
    ]);

    $ecsConfig->rule(ValidDefaultValueSniff::class);
    $ecsConfig->rule(MultiLineFunctionDeclarationSniff::class);
    $ecsConfig->rule(FunctionCallArgumentSpacingSniff::class);
    $ecsConfig->rule(ControlSignatureSniff::class);
    $ecsConfig->rule(ScopeClosingBraceSniff::class);
    $ecsConfig->rule(ForEachLoopDeclarationSniff::class);
    $ecsConfig->rule(ForLoopDeclarationSniff::class);
    $ecsConfig->rule(LowercaseDeclarationSniff::class);
    $ecsConfig->rule(InlineControlStructureSniff::class);

    $ecsConfig->skip([
        ControlStructureSpacingSniff::class . '.SpacingAfterOpenBrace',
        ControlStructureSpacingSniff::class . '.SpaceBeforeCloseBrace',
        ControlStructureSpacingSniff::class . '.LineAfterClose',
        ControlStructureSpacingSniff::class . '.NoLineAfterClose',
        FunctionCallSignatureSniff::class . '.OpeningIndent',
    ]);
};
