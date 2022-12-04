<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\RuleCodeSamplePrinter;

use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface;
use Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\BadGoodCodeSamplePrinter;
use Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\DiffCodeSamplePrinter;
use Symplify\RuleDocGenerator\RuleCodeSamplePrinter\ConfiguredRuleCustomPrinter\ECSConfigConfiguredRuleCustomPrinter;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ECSRuleCodeSamplePrinter implements RuleCodeSamplePrinterInterface
{
    public function __construct(
        private BadGoodCodeSamplePrinter $badGoodCodeSamplePrinter,
        private ConfiguredCodeSamplerPrinter $configuredCodeSamplerPrinter,
        private ECSConfigConfiguredRuleCustomPrinter $ecsConfigConfiguredRuleCustomPrinter,
        private DiffCodeSamplePrinter $diffCodeSamplePrinter
    ) {
    }

    public function isMatch(string $class): bool
    {
        if (is_a($class, 'PHP_CodeSniffer\Sniffs\Sniff', true)) {
            return true;
        }

        return is_a($class, 'PhpCsFixer\Fixer\FixerInterface', true);
    }

    /**
     * @return string[]
     */
    public function print(CodeSampleInterface $codeSample, RuleDefinition $ruleDefinition): array
    {
        if ($codeSample instanceof ConfiguredCodeSample) {
            return $this->configuredCodeSamplerPrinter->printConfiguredCodeSample(
                $ruleDefinition,
                $codeSample,
                $this->ecsConfigConfiguredRuleCustomPrinter
            );
        }

        if (is_a($ruleDefinition->getRuleClass(), 'PHP_CodeSniffer\Sniffs\Sniff', true)) {
            return $this->badGoodCodeSamplePrinter->print($codeSample);
        }

        return $this->diffCodeSamplePrinter->print($codeSample);
    }
}
