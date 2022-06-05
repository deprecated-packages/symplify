<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ValueObjectDestructRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Complexity\ValueObjectDestructRule;

/**
 * @extends RuleTestCase<ValueObjectDestructRule>
 */
final class ValueObjectDestructRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/UsingPublicMethods.php', [[ValueObjectDestructRule::ERROR_MESSAGE, 13]]];

        yield [__DIR__ . '/Fixture/SkipUsedJustOne.php', []];
        yield [__DIR__ . '/Fixture/SkipSingleMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipSingleMethodCalledTwice.php', []];
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
        return self::getContainer()->getByType(ValueObjectDestructRule::class);
    }
}
