<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\Rules\Include_;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanExtensions\Rules\Include_\ForbidReturnValueOfIncludeOnceRule;

final class ForbidReturnValueOfIncludeOnceRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse(
            [__DIR__ . '/Source/ReturnRequireOnce.php'],
            [['Cannot return include_once/require_once', 11]]
        );

        $this->analyse(
            [__DIR__ . '/Source/AssignRequireOnce.php'],
            [['Cannot return include_once/require_once', 11]]
        );
    }

    protected function getRule(): Rule
    {
        return new ForbidReturnValueOfIncludeOnceRule();
    }
}
