<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Printer;

use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class RuleDefinitionsPrinter
{
    /**
     * @var CodeSamplesPrinter
     */
    private $codeSamplesPrinter;

    public function __construct(CodeSamplesPrinter $codeSamplesPrinter)
    {
        $this->codeSamplesPrinter = $codeSamplesPrinter;
    }

    /**
     * @param RuleDefinition[] $ruleDefinitions
     * @return string[]
     */
    public function print(array $ruleDefinitions): array
    {
        $lines = [];
        $lines[] = '# Rules Overview';

        foreach ($ruleDefinitions as $ruleDefinition) {
            $lines[] = '## ' . $ruleDefinition->getRuleShortClass();
            $lines[] = $ruleDefinition->getDescription();

            if ($ruleDefinition->isConfigurable()) {
                $lines[] = ':wrench: **configure it!**';
            }

            $lines[] = '- class: `' . $ruleDefinition->getRuleClass() . '`';

            $codeSampleLines = $this->codeSamplesPrinter->print($ruleDefinition);
            $lines = array_merge($lines, $codeSampleLines);
        }

        return $lines;
    }
}
