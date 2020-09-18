<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoScalarAndArrayConstructorParameterRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoScalarAndArrayConstructorParameterRule;

final class NoScalarAndArrayConstructorParameterRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeWithConstructParameterScalarAndArray.php', [
            [NoScalarAndArrayConstructorParameterRule::ERROR_MESSAGE, 9],
            [NoScalarAndArrayConstructorParameterRule::ERROR_MESSAGE, 16],
            [NoScalarAndArrayConstructorParameterRule::ERROR_MESSAGE, 23],
            [NoScalarAndArrayConstructorParameterRule::ERROR_MESSAGE, 30],
            [NoScalarAndArrayConstructorParameterRule::ERROR_MESSAGE, 37],
        ]];
    }

    protected function getRule(): Rule
    {
        return new NoScalarAndArrayConstructorParameterRule();
    }
}
