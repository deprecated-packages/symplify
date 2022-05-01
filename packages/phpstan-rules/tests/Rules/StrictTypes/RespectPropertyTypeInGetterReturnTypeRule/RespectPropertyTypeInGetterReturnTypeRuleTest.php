<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\StrictTypes\RespectPropertyTypeInGetterReturnTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\Contract\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Rules\StrictTypes\RespectPropertyTypeInGetterReturnTypeRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RespectPropertyTypeInGetterReturnTypeRule>
 */
final class RespectPropertyTypeInGetterReturnTypeRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
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

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RespectPropertyTypeInGetterReturnTypeRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
