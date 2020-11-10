<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Printer\CodeSamplePrinter;

use Symplify\MarkdownDiff\Differ\MarkdownDiffer;
use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
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

    public function __construct(
        MarkdownDiffer $markdownDiffer,
        ConfiguredCodeSamplePrinter $configuredCodeSamplePrinter
    ) {
        $this->markdownDiffer = $markdownDiffer;
        $this->configuredCodeSamplePrinter = $configuredCodeSamplePrinter;
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

        $lines[] = $this->printPhpCode($codeSample->getGoodCode());
        $lines[] = ':x:';

        $lines[] = $this->printPhpCode($codeSample->getBadCode());
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

    private function printPhpCode(string $content): string
    {
        return $this->printCodeWrapped($content, 'php');
    }

    private function printCodeWrapped(string $content, string $format): string
    {
        return sprintf('```%s%s%s%s```', $format, PHP_EOL, rtrim($content), PHP_EOL) . PHP_EOL;
    }
}
