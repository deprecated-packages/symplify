<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ObjectCalisthenics\NoChainMethodCallRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\ObjectCalisthenics\NoChainMethodCallRule;

final class NoChainMethodCallRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/Source/ChainMethodCall.php'], [['Do not use chained method calls', 11]]);
    }

    protected function getRule(): Rule
    {
        return new NoChainMethodCallRule();
    }
}
