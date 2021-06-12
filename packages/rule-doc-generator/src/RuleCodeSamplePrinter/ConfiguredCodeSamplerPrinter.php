<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\RuleCodeSamplePrinter;

use Symplify\PhpConfigPrinter\Printer\SmartPhpConfigPrinter;
use Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\DiffCodeSamplePrinter;
use Symplify\RuleDocGenerator\Printer\MarkdownCodeWrapper;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ConfiguredCodeSamplerPrinter
{
    public function __construct(
        private SmartPhpConfigPrinter $smartPhpConfigPrinter,
        private MarkdownCodeWrapper $markdownCodeWrapper,
        private DiffCodeSamplePrinter $diffCodeSamplePrinter
    ) {
    }

    /**
     * @return string[]
     */
    public function printConfiguredCodeSample(
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
