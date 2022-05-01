<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ConstantMapRuleRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\ConstantMapRuleRule;

/**
 * @extends RuleTestCase<ConstantMapRuleRule>
 */
final class ConstantMapRuleRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipMethodsCalls.php', []];
        yield [__DIR__ . '/Fixture/SkipVariousReturns.php', []];
        yield [__DIR__ . '/Fixture/SkipEmptyArray.php', []];
        yield [__DIR__ . '/Fixture/SkipSomeReflectionMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipHalfHalf.php', []];

        yield [__DIR__ . '/Fixture/TypicalMap.php', [[ConstantMapRuleRule::ERROR_MESSAGE, 14]]];
        yield [__DIR__ . '/Fixture/ManyIfsThenStaticCall.php', [[ConstantMapRuleRule::ERROR_MESSAGE, 15]]];
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
        return self::getContainer()->getByType(ConstantMapRuleRule::class);
    }
}
