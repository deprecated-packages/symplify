<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer;
use PhpCsFixer\Fixer\ControlStructure\ElseifFixer;
use PhpCsFixer\Fixer\ControlStructure\NoBreakCommentFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer;
use PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer;
use PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer;
use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use PhpCsFixer\Fixer\Whitespace\LineEndingFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesInsideParenthesisFixer;
use PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(EncodingFixer::class);
    $ecsConfig->rule(FullOpeningTagFixer::class);
    $ecsConfig->rule(BlankLineAfterNamespaceFixer::class);
    $ecsConfig->rule(BracesFixer::class);
    $ecsConfig->rule(ClassDefinitionFixer::class);
    $ecsConfig->rule(ConstantCaseFixer::class);
    $ecsConfig->rule(ElseifFixer::class);
    $ecsConfig->rule(FunctionDeclarationFixer::class);
    $ecsConfig->rule(IndentationTypeFixer::class);
    $ecsConfig->rule(LineEndingFixer::class);
    $ecsConfig->rule(LowercaseKeywordsFixer::class);

    $ecsConfig->ruleWithConfiguration(MethodArgumentSpaceFixer::class, [
        'on_multiline' => 'ensure_fully_multiline',
    ]);

    $ecsConfig->rule(NoBreakCommentFixer::class);
    $ecsConfig->rule(NoClosingTagFixer::class);
    $ecsConfig->rule(NoSpacesAfterFunctionNameFixer::class);
    $ecsConfig->rule(NoSpacesInsideParenthesisFixer::class);
    $ecsConfig->rule(NoTrailingWhitespaceFixer::class);
    $ecsConfig->rule(NoTrailingWhitespaceInCommentFixer::class);
    $ecsConfig->rule(SingleBlankLineAtEofFixer::class);

    $ecsConfig->ruleWithConfiguration(SingleClassElementPerStatementFixer::class, [
        'elements' => ['property'],
    ]);

    $ecsConfig->rule(SingleImportPerStatementFixer::class);
    $ecsConfig->rule(SingleLineAfterImportsFixer::class);
    $ecsConfig->rule(SwitchCaseSemicolonToColonFixer::class);
    $ecsConfig->rule(SwitchCaseSpaceFixer::class);
    $ecsConfig->rule(VisibilityRequiredFixer::class);
};
