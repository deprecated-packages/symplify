<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\RuleCodeSamplePrinter;

use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface;
use Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\BadGoodCodeSamplePrinter;
use Symplify\RuleDocGenerator\Printer\MarkdownCodeWrapper;
use Symplify\RuleDocGenerator\Printer\NeonPrinter;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class PHPStanRuleCodeSamplePrinter implements RuleCodeSamplePrinterInterface
{
    public function __construct(
        private readonly NeonPrinter $neonPrinter,
        private readonly MarkdownCodeWrapper $markdownCodeWrapper,
        private readonly BadGoodCodeSamplePrinter $badGoodCodeSamplePrinter
    ) {
    }

    public function isMatch(string $class): bool
    {
        return is_a($class, 'PHPStan\Rules\Rule', true);
    }

    /**
     * @return string[]
     */
    public function print(CodeSampleInterface $codeSample, RuleDefinition $ruleDefinition): array
    {
        if ($codeSample instanceof ConfiguredCodeSample) {
            return $this->printConfigurableCodeSample($codeSample, $ruleDefinition);
        }

        return $this->badGoodCodeSamplePrinter->print($codeSample);
    }

    /**
     * @return string[]
     */
    private function printConfigurableCodeSample(
        ConfiguredCodeSample $configuredCodeSample,
        RuleDefinition $ruleDefinition
    ): array {
        $lines = [];

        $phpStanNeon = [
            'services' => [
                [
                    'class' => $ruleDefinition->getRuleClass(),
                    'tags' => ['phpstan.rules.rule'],
                    'arguments' => $configuredCodeSample->getConfiguration(),
                ],
            ],
        ];

        $printedNeon = $this->neonPrinter->printNeon($phpStanNeon);
        $lines[] = $this->markdownCodeWrapper->printYamlCode($printedNeon);

        $lines[] = '↓';

        $newLines = $this->badGoodCodeSamplePrinter->print($configuredCodeSample);
        return array_merge($lines, $newLines);
    }
}
