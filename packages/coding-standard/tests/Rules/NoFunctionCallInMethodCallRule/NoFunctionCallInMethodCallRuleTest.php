<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoFunctionCallInMethodCallRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoFunctionCallInMethodCallRule;

final class NoFunctionCallInMethodCallRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $errorMessage = sprintf(NoFunctionCallInMethodCallRule::ERROR_MESSAGE, 'strlen');
        $this->analyse([__DIR__ . '/Fixture/FunctionCallNestedToMethodCall.php'], [[$errorMessage, 11]]);
    }

    protected function getRule(): Rule
    {
        return new NoFunctionCallInMethodCallRule();
    }
}
