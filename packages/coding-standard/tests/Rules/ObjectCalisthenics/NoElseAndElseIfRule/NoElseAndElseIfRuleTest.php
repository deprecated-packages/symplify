<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ObjectCalisthenics\NoElseAndElseIfRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\ObjectCalisthenics\NoElseAndElseIfRule;

final class NoElseAndElseIfRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/Source/SomeElse.php'], [[NoElseAndElseIfRule::MESSAGE, 13]]);
    }

    protected function getRule(): Rule
    {
        return new NoElseAndElseIfRule();
    }
}
