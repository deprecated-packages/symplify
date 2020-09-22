<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoTraitExceptItsMethodsRequired;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoTraitExceptItsMethodsRequired;

final class NoTraitExceptItsMethodsRequiredTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeClassWithoutTrait.php', []];
        yield [__DIR__ . '/Fixture/SomeClassWithTrait.php', [[NoTraitExceptItsMethodsRequired::ERROR_MESSAGE, 7]]];
    }

    protected function getRule(): Rule
    {
        return new NoTraitExceptItsMethodsRequired();
    }
}
