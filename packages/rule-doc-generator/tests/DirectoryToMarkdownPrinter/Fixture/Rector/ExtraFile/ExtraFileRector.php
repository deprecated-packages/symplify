<?php
declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\Rector\ExtraFile;

use Rector\Core\Contract\Rector\RectorInterface;
use Rector\Core\RectorDefinition\RectorDefinition;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ExtraFileCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ExtraFileRector implements RectorInterface, DocumentedRuleInterface
{
    public function getDefinition(): RectorDefinition
    {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Some change', [
            new ExtraFileCodeSample(
                <<<'CODE_SAMPLE'
before
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
after
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
extra file
CODE_SAMPLE
            )
        ]);
    }
}
