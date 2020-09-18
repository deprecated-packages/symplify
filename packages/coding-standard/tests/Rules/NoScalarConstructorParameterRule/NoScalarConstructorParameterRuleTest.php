<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoScalarConstructorParameterRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoScalarConstructorParameterRule;

final class NoScalarConstructorParameterRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/ValueObject/SomeConstruct.php', []];
        yield [__DIR__ . '/Fixture/SomeConstruct.php', []];
        yield [__DIR__ . '/Fixture/SomeWithoutConstruct.php', []];
        yield [__DIR__ . '/Fixture/SomeWithConstructParameterNonScalar.php', []];
        yield [__DIR__ . '/Fixture/SomeWithConstructParameterNotype.php', []];
        yield [__DIR__ . '/Fixture/SomeWithConstructParameterScalar.php', [
            [NoScalarConstructorParameterRule::ERROR_MESSAGE, 9],
            [NoScalarConstructorParameterRule::ERROR_MESSAGE, 16],
            [NoScalarConstructorParameterRule::ERROR_MESSAGE, 23],
            [NoScalarConstructorParameterRule::ERROR_MESSAGE, 30],
        ]];
    }

    protected function getRule(): Rule
    {
        return new NoScalarConstructorParameterRule();
    }
}
