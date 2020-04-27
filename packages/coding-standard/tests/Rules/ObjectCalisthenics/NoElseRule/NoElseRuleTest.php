<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ObjectCalisthenics\NoElseRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\ObjectCalisthenics\NoElseRule;

final class NoElseRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/Source/SomeElse.php'], [['Do not use "else" keyword', 13]]);
    }

    protected function getRule(): Rule
    {
        return new NoElseRule();
    }
}
