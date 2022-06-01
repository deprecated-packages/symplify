<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule;

/**
 * @extends RuleTestCase<RequireEnumDocBlockOnConstantListPassRule>
 */
final class RequireEnumDocBlockOnConstantListPassRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipWithEnumLikeType.php', []];
        yield [__DIR__ . '/Fixture/SkipMoreStrings.php', []];
        yield [__DIR__ . '/Fixture/SkipExternalCarWithType.php', []];
        yield [__DIR__ . '/Fixture/SkipClassConstReference.php', []];
        yield [__DIR__ . '/Fixture/SkipSelfReference.php', []];
        yield [__DIR__ . '/Fixture/SkipParameterProvider.php', []];

        yield [
            __DIR__ . '/Fixture/ClassWithoutParamEnumType.php',
            [[RequireEnumDocBlockOnConstantListPassRule::ERROR_MESSAGE, 13]],
        ];

        yield [
            __DIR__ . '/Fixture/OnePositionCovered.php',
            [[RequireEnumDocBlockOnConstantListPassRule::ERROR_MESSAGE, 13]],
        ];

        yield [
            __DIR__ . '/Fixture/ExternalNoType.php',
            [[RequireEnumDocBlockOnConstantListPassRule::ERROR_MESSAGE, 15]],
        ];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(RequireEnumDocBlockOnConstantListPassRule::class);
    }
}
