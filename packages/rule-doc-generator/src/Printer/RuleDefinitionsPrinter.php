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
        $markdownLines = [];
        foreach ($ruleDefinitions as $ruleDefinition) {
            $markdownLines[] = '## ' . $ruleDefinition->getRuleShortClass();
            $markdownLines[] = $ruleDefinition->getDescription();
            $markdownLines[] = '- `' . $ruleDefinition->getRuleClass() . '`';

            $codeSampleLines = $this->codeSamplesPrinter->print($ruleDefinition);
            $markdownLines = array_merge($markdownLines, $codeSampleLines);
        }

        return $markdownLines;
    }
}
