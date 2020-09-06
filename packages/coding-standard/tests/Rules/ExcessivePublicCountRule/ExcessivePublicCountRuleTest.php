<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ExcessivePublicCountRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\ExcessivePublicCountRule;

final class ExcessivePublicCountRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        $message = sprintf(ExcessivePublicCountRule::ERROR_MESSAGE, 6, 5);
        yield [__DIR__ . '/Fixture/TooManyPublicElements.php', [[$message, 7]]];

        yield [__DIR__ . '/Fixture/SkipUnderLimit.php', []];
        yield [__DIR__ . '/Fixture/ValueObject/SkipConstantInValueObject.php', []];
        yield [__DIR__ . '/Fixture/SkipConstructorAndMagicMethods.php', []];
    }

    protected function getRule(): Rule
    {
        return new ExcessivePublicCountRule(5);
    }
}
