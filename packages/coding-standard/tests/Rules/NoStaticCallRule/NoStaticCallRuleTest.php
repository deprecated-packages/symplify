<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoStaticCallRule;
use Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Source\AllowedStaticMethods;

final class NoStaticCallRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $analysedFilePath, array $expectedErrorsWithLine): void
    {
        $this->analyse([$analysedFilePath], $expectedErrorsWithLine);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeStaticCall.php', [[NoStaticCallRule::ERROR_MESSAGE, 13]]];

        yield [__DIR__ . '/Fixture/SkipAllowedStaticCall.php', []];
        yield [__DIR__ . '/Fixture/SkipAllowedDateTime.php', []];
        yield [__DIR__ . '/Fixture/SkipParentSelfStatic.php', []];
        yield [__DIR__ . '/Fixture/SkipStaticFactory.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoStaticCallRule([AllowedStaticMethods::class]);
    }
}
