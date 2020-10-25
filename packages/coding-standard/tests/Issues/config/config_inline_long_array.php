<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer;
use PhpCsFixer\Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer;
use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    // @todo sets can't be used here, as test only works with dummy configs

    $services = $containerConfigurator->services();
    $services->set(LineLengthFixer::class);

    // array spacing
    $services->set(NoWhitespaceBeforeCommaInArrayFixer::class);
    $services->set(ArrayOpenerAndCloserNewlineFixer::class);
    $services->set(ArrayIndentationFixer::class);
    $services->set(TrimArraySpacesFixer::class);
    $services->set(WhitespaceAfterCommaInArrayFixer::class);
    $services->set(ArrayListItemNewlineFixer::class);

    // commas
    $services->set(TrailingCommaInMultilineArrayFixer::class);
    $services->set(NoTrailingCommaInSinglelineArrayFixer::class);
};
