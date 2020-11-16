<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Printer;

use Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\CodeSamplePrinter;
use Symplify\RuleDocGenerator\Text\KeywordHighlighter;
use Symplify\RuleDocGenerator\ValueObject\Lines;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class RuleDefinitionsPrinter
{
    /**
     * @var CodeSamplePrinter
     */
    private $codeSamplePrinter;

    /**
     * @var KeywordHighlighter
     */
    private $keywordHighlighter;

    public function __construct(CodeSamplePrinter $codeSamplePrinter, KeywordHighlighter $keywordHighlighter)
    {
        $this->codeSamplePrinter = $codeSamplePrinter;
        $this->keywordHighlighter = $keywordHighlighter;
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
            $lines[] = $this->keywordHighlighter->highlight($ruleDefinition->getDescription());

            if ($ruleDefinition->isConfigurable()) {
                $lines[] = Lines::CONFIGURE_IT;
            }

            $lines[] = '- class: `' . $ruleDefinition->getRuleClass() . '`';

            $codeSampleLines = $this->codeSamplePrinter->print($ruleDefinition);
            $lines = array_merge($lines, $codeSampleLines);
        }

        return $lines;
    }
}
