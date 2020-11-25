<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\Rector\ComposerJsonAware;

use Rector\Core\Contract\Rector\RectorInterface;
use Rector\Core\RectorDefinition\RectorDefinition;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ComposerJsonAwareCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ComposerJsonAwareRector implements RectorInterface, DocumentedRuleInterface, ConfigurableRuleInterface
{
    public function getDefinition(): RectorDefinition
    {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Some change', [
            new ComposerJsonAwareCodeSample(
                <<<'CODE_SAMPLE'
before
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
after
CODE_SAMPLE
                ,
<<<'CODE_SAMPLE'
{
    "name": "some-project"
}
CODE_SAMPLE
        )]);
    }
}
