<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\Rules\ClassMethod;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanExtensions\Rules\ClassMethod\BoolishClassMethodPrefixRule;

final class BoolishClassMethodPrefixRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse(
            [__DIR__ . '/Source/ClassWithBoolishMethods.php'],
            [
                ['Method "honesty()" returns bool type, so the name should start with is/has/was...', 9],
                ['Method "thatWasGreat()" returns bool type, so the name should start with is/has/was...', 14],
            ]
        );

        $this->analyse([__DIR__ . '/Source/ClassWithEmptyReturn.php'], []);

        $this->analyse([__DIR__ . '/Source/ClassThatImplementsInterface.php'], [
            ['Method "nothing()" returns bool type, so the name should start with is/has/was...', 9],
        ]);
    }

    protected function getRule(): Rule
    {
        return new BoolishClassMethodPrefixRule();
    }
}
