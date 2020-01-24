<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\Rules\Classes;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanExtensions\Rules\Classes\MatchingTypeConstantRule;

final class MatchingTypeConstantRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse(
            [__DIR__ . '/Source/ClassWithConstants.php'],
            [
                ['Constant type should be "bool", but is "string"', 12],
                ['Constant type should be "string", but is "bool"', 17],
            ]
        );
    }

    protected function getRule(): Rule
    {
        return new MatchingTypeConstantRule();
    }
}
