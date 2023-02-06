<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Tests\Filter\Source;

use Symplify\RuleDocGenerator\Contract\RuleOutFilterInterface;
use Symplify\RuleDocGenerator\ValueObject\RuleClassWithFilePath;

final class RemoveRuleOutFilter implements RuleOutFilterInterface
{
    /**
     * @param RuleClassWithFilePath[] $ruleClassWithFilePath
     * @return RuleClassWithFilePath[]
     */
    public function filter(array $ruleClassWithFilePath): array
    {
        return array_filter($ruleClassWithFilePath, static fn (RuleClassWithFilePath $ruleClassWithFilePath): bool => ! is_a($ruleClassWithFilePath->getClass(), SkippedRuleInterface::class, true));
    }
}
