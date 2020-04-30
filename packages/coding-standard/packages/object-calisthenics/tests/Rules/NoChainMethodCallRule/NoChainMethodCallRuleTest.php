<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\NoChainMethodCallRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\ObjectCalisthenics\Rules\NoChainMethodCallRule;

final class NoChainMethodCallRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/Source/ChainMethodCall.php'], [[NoChainMethodCallRule::ERROR_MESSAGE, 11]]);
    }

    protected function getRule(): Rule
    {
        return new NoChainMethodCallRule();
    }
}
