<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbidReturnValueOfIncludeOnceRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\ForbidReturnValueOfIncludeOnceRule;

final class ForbidReturnValueOfIncludeOnceRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse(
            [__DIR__ . '/Source/ReturnRequireOnce.php'],
            [[ForbidReturnValueOfIncludeOnceRule::ERROR_MESSAGE, 11]]
        );

        $this->analyse(
            [__DIR__ . '/Source/AssignRequireOnce.php'],
            [[ForbidReturnValueOfIncludeOnceRule::ERROR_MESSAGE, 11]]
        );
    }

    protected function getRule(): Rule
    {
        return new ForbidReturnValueOfIncludeOnceRule();
    }
}
