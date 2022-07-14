<?php
declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\Rector\Configurable;

use Rector\Core\Contract\Rector\RectorInterface;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class DirectConfiguredStringKeyRector implements RectorInterface, ConfigurableRuleInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Some change', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
before
CODE_SAMPLE
,
                <<<'CODE_SAMPLE'
after
CODE_SAMPLE,
                [
                    'view' => 'Laravel\\Templating\\render',
                    'redirect' => 'Some\\Redirector\\redirect',
                ]
            )
        ]);
    }
}
