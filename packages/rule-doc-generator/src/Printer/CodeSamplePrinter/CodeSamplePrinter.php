<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Printer\CodeSamplePrinter;

use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface;
use Symplify\RuleDocGenerator\Exception\ConfigurationBoundException;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\DirectoryToMarkdownPrinterTest
 */
final class CodeSamplePrinter
{
    /**
     * @param RuleCodeSamplePrinterInterface[] $ruleCodeSamplePrinters
     */
    public function __construct(
        private array $ruleCodeSamplePrinters
    ) {
    }

    /**
     * @return string[]
     */
    public function print(RuleDefinition $ruleDefinition, bool $shouldUseConfigureMethod): array
    {
        $lines = [];

        foreach ($ruleDefinition->getCodeSamples() as $codeSample) {
            $this->ensureConfigureRuleBoundsConfiguredCodeSample($codeSample, $ruleDefinition);

            foreach ($this->ruleCodeSamplePrinters as $ruleCodeSamplePrinter) {
                if (! $ruleCodeSamplePrinter->isMatch($ruleDefinition->getRuleClass())) {
                    continue;
                }

                $newLines = $ruleCodeSamplePrinter->print($codeSample, $ruleDefinition, $shouldUseConfigureMethod);
                $lines = array_merge($lines, $newLines);
                break;
            }

            $lines[] = '<br>';
        }

        return $lines;
    }

    private function ensureConfigureRuleBoundsConfiguredCodeSample(
        CodeSampleInterface $codeSample,
        RuleDefinition $ruleDefinition
    ): void {
        // ensure the configured rule + configure code sample are used
        if ($codeSample instanceof ConfiguredCodeSample) {
            if (is_a($ruleDefinition->getRuleClass(), ConfigurableRuleInterface::class, true)) {
                return;
            }

            $errorMessage = sprintf(
                'The "%s" rule has configure code sample and must implements "%s" interface',
                $ruleDefinition->getRuleClass(),
                ConfigurableRuleInterface::class
            );
            throw new ConfigurationBoundException($errorMessage);
        } else {
            if (! is_a($ruleDefinition->getRuleClass(), ConfigurableRuleInterface::class, true)) {
                return;
            }

            $errorMessage = sprintf(
                'The "%s" rule implements "%s" and code sample must be "%s"',
                $ruleDefinition->getRuleClass(),
                ConfigurableRuleInterface::class,
                ConfiguredCodeSample::class,
            );
            throw new ConfigurationBoundException($errorMessage);
        }
    }
}
