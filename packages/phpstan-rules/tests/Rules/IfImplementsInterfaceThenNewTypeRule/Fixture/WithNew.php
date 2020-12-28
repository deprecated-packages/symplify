<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\IfImplementsInterfaceThenNewTypeRule\Fixture;

use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

final class WithNew implements ConfigurableRuleInterface
{
    public function run()
    {
        return new CodeSample('...', '...');
    }
}
