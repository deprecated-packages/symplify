<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\NoSetterClassMethodRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\ObjectCalisthenics\Rules\NoSetterClassMethodRule;

final class NoSetterClassMethodRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse(
            [__DIR__ . '/Source/SetterMethod.php'],
            [[sprintf(NoSetterClassMethodRule::ERROR_MESSAGE, 'setName'), 9]]
        );
    }

    protected function getRule(): Rule
    {
        return new NoSetterClassMethodRule();
    }
}
