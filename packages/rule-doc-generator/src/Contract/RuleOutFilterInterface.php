<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Contract;

use Symplify\RuleDocGenerator\ValueObject\RuleClassWithFilePath;

/**
 * @api Implement this interface to remove some rules from the generated docs.
 * E.g. meta rules or private rules.
 *
 * @see \Symplify\RuleDocGenerator\Tests\Filter\RuleOutFilterTest
 */
interface RuleOutFilterInterface
{
    /**
     * @param RuleClassWithFilePath[] $ruleClassWithFilePath
     * @return RuleClassWithFilePath[]
     */
    public function filter(array $ruleClassWithFilePath): array;
}
