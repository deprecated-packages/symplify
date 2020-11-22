<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\RuleCodeSamplePrinter;

use Symplify\PhpConfigPrinter\Printer\SmartPhpConfigPrinter;
use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface;
use Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\DiffCodeSamplePrinter;
use Symplify\RuleDocGenerator\Printer\MarkdownCodeWrapper;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class PHPCSFixerRuleCodeSamplePrinter implements RuleCodeSamplePrinterInterface
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
        return is_a($class, 'PhpCsFixer\Fixer\FixerInterface', true);
    }

    /**
     * @return mixed[]|string[]
     */
    public function print(CodeSampleInterface $codeSample, RuleDefinition $ruleDefinition): array
    {
        if ($codeSample instanceof ConfiguredCodeSample) {
            return $this->printConfiguredCodeSample($ruleDefinition, $codeSample);
        }

        return $this->diffCodeSamplePrinter->print($codeSample);
    }

    /**
     * @return string[]
     */
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
}
