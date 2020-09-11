<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoArrayStringObjectReturnRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoArrayStringObjectReturnRule;

final class NoArrayStringObjectReturnRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse(
            [__DIR__ . '/Fixture/ArrayStringObjectReturn.php'],
            [[NoArrayStringObjectReturnRule::ERROR_MESSAGE, 18]]
        );

        $this->analyse(
            [__DIR__ . '/Fixture/WithoutPropertyArrayStringObjectReturn.php'],
            [[NoArrayStringObjectReturnRule::ERROR_MESSAGE, 13]]
        );

        $this->analyse([__DIR__ . '/Fixture/SkipNonStringKey.php'], []);
    }

    protected function getRule(): Rule
    {
        return new NoArrayStringObjectReturnRule();
    }
}
