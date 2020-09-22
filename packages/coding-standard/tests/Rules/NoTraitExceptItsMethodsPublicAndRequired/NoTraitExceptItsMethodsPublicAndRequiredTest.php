<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoTraitExceptItsMethodsPublicAndRequired;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoTraitExceptItsMethodsPublicAndRequired;

final class NoTraitExceptItsMethodsPublicAndRequiredTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeTraitWithPublicMethodRequired.php', []];
        yield [__DIR__ . '/Fixture/SomeTrait.php', [[NoTraitExceptItsMethodsPublicAndRequired::ERROR_MESSAGE, 7]]];
        yield [
            __DIR__ . '/Fixture/SomeTraitWithoutMethod.php',
            [[NoTraitExceptItsMethodsPublicAndRequired::ERROR_MESSAGE, 7]],
        ];
        yield [
            __DIR__ . '/Fixture/SomeTraitWithPublicMethod.php',
            [[NoTraitExceptItsMethodsPublicAndRequired::ERROR_MESSAGE, 7]],
        ];
        yield [
            __DIR__ . '/Fixture/SomeTraitWithPrivateMethodRequired.php',
            [[NoTraitExceptItsMethodsPublicAndRequired::ERROR_MESSAGE, 7]],
        ];
    }

    protected function getRule(): Rule
    {
        return new NoTraitExceptItsMethodsPublicAndRequired();
    }
}
