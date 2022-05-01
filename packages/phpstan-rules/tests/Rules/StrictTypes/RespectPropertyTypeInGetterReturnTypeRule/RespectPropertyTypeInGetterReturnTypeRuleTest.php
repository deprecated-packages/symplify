<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\StrictTypes\RespectPropertyTypeInGetterReturnTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\StrictTypes\RespectPropertyTypeInGetterReturnTypeRule;

/**
 * @extends RuleTestCase<RespectPropertyTypeInGetterReturnTypeRule>
 */
final class RespectPropertyTypeInGetterReturnTypeRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[]|array<int, array<int|string>> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipPromotedProperty.php', []];
        yield [__DIR__ . '/Fixture/SkipMatchingArrayType.php', []];
        yield [__DIR__ . '/Fixture/SkipInterface.php', []];
        yield [__DIR__ . '/Fixture/SkipUntrustableDocblock.php', []];
        yield [__DIR__ . '/Fixture/SkipNullableSetter.php', []];

        yield [
            __DIR__ . '/Fixture/ArrayGetterNullable.php',
            [[RespectPropertyTypeInGetterReturnTypeRule::ERROR_MESSAGE, 11]],
        ];

        yield [
            __DIR__ . '/Fixture/IntegerGetterFloat.php',
            [[RespectPropertyTypeInGetterReturnTypeRule::ERROR_MESSAGE, 11]],
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
        return self::getContainer()->getByType(RespectPropertyTypeInGetterReturnTypeRule::class);
    }
}
