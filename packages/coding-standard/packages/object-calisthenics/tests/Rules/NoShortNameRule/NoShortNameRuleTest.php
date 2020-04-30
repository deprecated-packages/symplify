<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\NoShortNameRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\ObjectCalisthenics\Rules\NoShortNameRule;

final class NoShortNameRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/Source/ShortNamingClass.php'], [
            [sprintf(NoShortNameRule::ERROR_MESSAGE, 'em'), 9],
            [sprintf(NoShortNameRule::ERROR_MESSAGE, 'YE'), 11],
        ]);
    }

    protected function getRule(): Rule
    {
        return new NoShortNameRule();
    }
}
