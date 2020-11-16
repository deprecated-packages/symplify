<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\RuleCodeSamplePrinter;

use Migrify\PhpConfigPrinter\Printer\SmartPhpConfigPrinter;
use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface;
use Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\DiffCodeSamplePrinter;
use Symplify\RuleDocGenerator\Printer\MarkdownCodeWrapper;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ComposerJsonAwareCodeSample;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ExtraFileCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class RectorRuleCodeSamplePrinter implements RuleCodeSamplePrinterInterface
{
    /**
     * @var DiffCodeSamplePrinter
     */
    private $diffCodeSamplePrinter;

    /**
     * @var MarkdownCodeWrapper
     */
    private $markdownCodeWrapper;

    /**
     * @var SmartPhpConfigPrinter
     */
    private $smartPhpConfigPrinter;

    public function __construct(
        DiffCodeSamplePrinter $diffCodeSamplePrinter,
        MarkdownCodeWrapper $markdownCodeWrapper,
        SmartPhpConfigPrinter $smartPhpConfigPrinter
    ) {
        $this->diffCodeSamplePrinter = $diffCodeSamplePrinter;
        $this->markdownCodeWrapper = $markdownCodeWrapper;
        $this->smartPhpConfigPrinter = $smartPhpConfigPrinter;
    }

    public function isMatch(string $class): bool
    {
        /** @noRector */
        return is_a($class, 'Rector\Core\Contract\Rector\RectorInterface', true);
    }

    /**
     * @return string[]
     */
    public function print(CodeSampleInterface $codeSample, RuleDefinition $ruleDefinition): array
    {
        if ($codeSample instanceof ExtraFileCodeSample) {
            return $this->printExtraFileCodeSample($codeSample);
        }

        if ($codeSample instanceof ComposerJsonAwareCodeSample) {
            return $this->printComposerJsonAwareCodeSample($codeSample);
        }

        if ($codeSample instanceof ConfiguredCodeSample) {
            return $this->printConfiguredCodeSample($ruleDefinition, $codeSample);
        }

        return $this->diffCodeSamplePrinter->print($codeSample);
    }

    private function printConfiguredCodeSample(
        RuleDefinition $ruleDefinition,
        ConfiguredCodeSample $configuredCodeSample
    ): array {
        $lines = [];

        $configPhpCode = $this->smartPhpConfigPrinter->printConfiguredServices([
            $ruleDefinition->getRuleClass() => $configuredCodeSample->getConfiguration(),
        ]);
        $lines[] = $this->markdownCodeWrapper->printPhpCode($configPhpCode);

        $lines[] = '↓';

        $newLines = $this->diffCodeSamplePrinter->print($configuredCodeSample);
        return array_merge($lines, $newLines);
    }

    private function printComposerJsonAwareCodeSample(ComposerJsonAwareCodeSample $composerJsonAwareCodeSample)
    {
        $lines = [];

        $lines[] = '- with `composer.json`:';
        $lines[] = $this->markdownCodeWrapper->printJsonCode($composerJsonAwareCodeSample->getComposerJson());
        $lines[] = '↓';

        $newLines = $this->diffCodeSamplePrinter->print($composerJsonAwareCodeSample);
        return array_merge($lines, $newLines);
    }

    /**
     * @return string[]
     */
    private function printExtraFileCodeSample(ExtraFileCodeSample $extraFileCodeSample): array
    {
        $lines = $this->diffCodeSamplePrinter->print($extraFileCodeSample);

        $lines[] = 'Extra file:';
        $lines[] = $this->markdownCodeWrapper->printPhpCode($extraFileCodeSample->getExtraFile());

        return $lines;
    }
}
