<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoScalarAndArrayConstructorParameterRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoScalarAndArrayConstructorParameterRule;

final class NoScalarAndArrayConstructorParameterRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipPHPStanRuleWithConstructorConfiguration.php', []];

        yield [__DIR__ . '/Fixture/SkipExtension.php', []];
        yield [__DIR__ . '/Fixture/Entity/SkipApple.php', []];
        yield [__DIR__ . '/Fixture/Event/SkipEvent.php', []];
        yield [__DIR__ . '/Fixture/ValueObject/Deep/SkipSomeConstruct.php', []];

        yield [__DIR__ . '/Fixture/SkipNonScalar.php', []];
        yield [__DIR__ . '/Fixture/SkipNullalbeNonScalar.php', []];
        yield [__DIR__ . '/Fixture/SkipNonConstruct.php', []];
        yield [__DIR__ . '/Fixture/SkipAutowireArrayTypes.php', []];
        yield [__DIR__ . '/Fixture/SkipDummyArray.php', []];

        yield [
            __DIR__ . '/Fixture/StringScalarType.php',
            [[NoScalarAndArrayConstructorParameterRule::ERROR_MESSAGE, 16]],
        ];

        yield [
            __DIR__ . '/Fixture/BoolScalarType.php',
            [[NoScalarAndArrayConstructorParameterRule::ERROR_MESSAGE, 16]],
        ];

        yield [__DIR__ . '/Fixture/StringArray.php', [[NoScalarAndArrayConstructorParameterRule::ERROR_MESSAGE, 19]]];
        yield [__DIR__ . '/Fixture/IntScalarType.php', [[NoScalarAndArrayConstructorParameterRule::ERROR_MESSAGE, 16]]];

        yield [__DIR__ . '/Fixture/FloatScalarType.php', [
            [NoScalarAndArrayConstructorParameterRule::ERROR_MESSAGE, 19],
        ]];

        yield [__DIR__ . '/Fixture/SomeWithConstructParameterNullableScalar.php', [
            [NoScalarAndArrayConstructorParameterRule::ERROR_MESSAGE, 16],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoScalarAndArrayConstructorParameterRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
