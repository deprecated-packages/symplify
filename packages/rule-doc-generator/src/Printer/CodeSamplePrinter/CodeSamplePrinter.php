<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Printer\CodeSamplePrinter;

use Symplify\MarkdownDiff\Differ\MarkdownDiffer;
use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Printer\MarkdownCodeWrapper;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class CodeSamplePrinter
{
    /**
     * @var MarkdownDiffer
     */
    private $markdownDiffer;

    /**
     * @var ConfiguredCodeSamplePrinter
     */
    private $configuredCodeSamplePrinter;

    /**
     * @var MarkdownCodeWrapper
     */
    private $markdownCodeWrapper;

    public function __construct(
        MarkdownDiffer $markdownDiffer,
        ConfiguredCodeSamplePrinter $configuredCodeSamplePrinter,
        MarkdownCodeWrapper $markdownCodeWrapper
    ) {
        $this->markdownDiffer = $markdownDiffer;
        $this->configuredCodeSamplePrinter = $configuredCodeSamplePrinter;
        $this->markdownCodeWrapper = $markdownCodeWrapper;
    }

    /**
     * @return string[]
     */
    public function print(RuleDefinition $ruleDefinition): array
    {
        $lines = [];

        foreach ($ruleDefinition->getCodeSamples() as $codeSample) {
            if ($codeSample instanceof ConfiguredCodeSample) {
                $newLines = $this->configuredCodeSamplePrinter->print($codeSample, $ruleDefinition);
                $lines = array_merge($lines, $newLines);
            }

            /** @noRector */
            if ($ruleDefinition->isPHPCSFixer()) {
                $newLines = $this->printDiffCodeSample($codeSample);
                $lines = array_merge($lines, $newLines);
            } else {
                $newLines = $this->printGoodBadCodeSample($codeSample);
                $lines = array_merge($lines, $newLines);
            }

            $lines[] = '<br>';
        }

        return $lines;
    }

    /**
     * @return string[]
     */
    private function printGoodBadCodeSample(CodeSampleInterface $codeSample): array
    {
        $lines = [];

        $lines[] = $this->markdownCodeWrapper->printPhpCode($codeSample->getGoodCode());
        $lines[] = ':x:';

        $lines[] = '<br>';

        $lines[] = $this->markdownCodeWrapper->printPhpCode($codeSample->getBadCode());
        $lines[] = ':+1:';

        return $lines;
    }

    /**
     * @return string[]
     */
    private function printDiffCodeSample(CodeSampleInterface $codeSample): array
    {
        $lines = [];
        $lines[] = $this->markdownDiffer->diff($codeSample->getGoodCode(), $codeSample->getBadCode());

        return $lines;
    }
}
