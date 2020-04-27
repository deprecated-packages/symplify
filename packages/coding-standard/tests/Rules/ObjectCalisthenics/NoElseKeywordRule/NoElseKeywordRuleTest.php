<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ObjectCalisthenics\NoElseKeywordRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\ObjectCalisthenics\NoElseKeywordRule;

final class NoElseKeywordRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/Source/SomeElse.php'], [['Do not use "else" keyword', 13]]);
    }

    protected function getRule(): Rule
    {
        return new NoElseKeywordRule();
    }
}
