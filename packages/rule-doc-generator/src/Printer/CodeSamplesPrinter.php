<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Printer;

use Migrify\PhpConfigPrinter\Printer\SmartPhpConfigPrinter;
use Symplify\MarkdownDiff\Differ\MarkdownDiffer;
use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\ValueObject\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class CodeSamplesPrinter
{
    /**
     * @var MarkdownDiffer
     */
    private $markdownDiffer;

    /**
     * @var SmartPhpConfigPrinter
     */
    private $smartPhpConfigPrinter;

    public function __construct(MarkdownDiffer $markdownDiffer, SmartPhpConfigPrinter $smartPhpConfigPrinter)
    {
        $this->markdownDiffer = $markdownDiffer;
        $this->smartPhpConfigPrinter = $smartPhpConfigPrinter;
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

            if ($codeSample instanceof ConfiguredCodeSample) {
                $configContent = $this->smartPhpConfigPrinter->printConfiguredServices([
                    $ruleDefinition->getRuleClass() => $codeSample->getConfiguration(),
                ]);

                $lines[] = $this->printPhpCode($configContent);
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
