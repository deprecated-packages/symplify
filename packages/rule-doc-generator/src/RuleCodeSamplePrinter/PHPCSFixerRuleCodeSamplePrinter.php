<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\RuleCodeSamplePrinter;

use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface;
use Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\DiffCodeSamplePrinter;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class PHPCSFixerRuleCodeSamplePrinter implements RuleCodeSamplePrinterInterface
{
    /**
     * @var DiffCodeSamplePrinter
     */
    private $diffCodeSamplePrinter;

    /**
     * @var ConfiguredCodeSamplerPrinter
     */
    private $configuredCodeSamplerPrinter;

    public function __construct(
        DiffCodeSamplePrinter $diffCodeSamplePrinter,
        ConfiguredCodeSamplerPrinter $configuredCodeSamplerPrinter
    ) {
        $this->diffCodeSamplePrinter = $diffCodeSamplePrinter;
        $this->configuredCodeSamplerPrinter = $configuredCodeSamplerPrinter;
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
            return $this->configuredCodeSamplerPrinter->printConfiguredCodeSample($ruleDefinition, $codeSample);
        }

        return $this->diffCodeSamplePrinter->print($codeSample);
    }
}
