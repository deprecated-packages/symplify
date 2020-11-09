<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Printer;

use Symplify\ConsoleColorDiff\Differ\MarkdownDiffer;
use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\ValueObject\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class CodeSamplesPrinter
{
    /**
     * @var MarkdownDiffer
     */
    private $markdownDiffer;

    public function __construct(MarkdownDiffer $markdownDiffer)
    {
        $this->markdownDiffer = $markdownDiffer;
    }

    /**
     * @return string[]
     */
    public function print(RuleDefinition $ruleDefinition): array
    {
        $lines = [];

        foreach ($ruleDefinition->getCodeSamples() as $codeSample) {
            /** @noRector */
            if (is_a($ruleDefinition->getRuleClass(), 'PhpCsFixer\Fixer\FixerInterface', true)) {
                $lines = array_merge($lines, $this->printDiffCodeSample($codeSample));
            } else {
                $lines = array_merge($lines, $this->printGoodBadCodeSample($codeSample));
            }

            $lines[] = '<br>';
        }

        return $lines;
    }

    private function printPhpCode(string $content): string
    {
        return $this->printCodeWrapped($content, 'php');
    }

    private function printCodeWrapped(string $content, string $format): string
    {
        return sprintf('```%s%s%s%s```', $format, PHP_EOL, rtrim($content), PHP_EOL);
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

        if ($codeSample instanceof ConfiguredCodeSample) {
            // @todo
        }
        return $lines;
    }

    /**
     * @return string[]
     */
    private function printDiffCodeSample(CodeSampleInterface $codeSample): array
    {
        $diffContent = $this->markdownDiffer->diff($codeSample->getGoodCode(), $codeSample->getBadCode());
        $diffLine = $this->printCodeWrapped($diffContent, 'diff');

        return [$diffLine];
    }
}
