<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoClassWithStaticMethodWithoutStaticNameRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoClassWithStaticMethodWithoutStaticNameRule;

final class NoClassWithStaticMethodWithoutStaticNameRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $errorMessage = sprintf(NoClassWithStaticMethodWithoutStaticNameRule::ERROR_MESSAGE, 'ClassWithMethod');
        $this->analyse([__DIR__ . '/Fixture/ClassWithMethod.php'], [[$errorMessage, 7]]);
        $this->analyse([__DIR__ . '/Fixture/SkipEventSubscriber.php'], []);

        $this->analyse([__DIR__ . '/Fixture/SkipValueObjectFactory.php'], []);
    }

    protected function getRule(): Rule
    {
        return new NoClassWithStaticMethodWithoutStaticNameRule();
    }
}
