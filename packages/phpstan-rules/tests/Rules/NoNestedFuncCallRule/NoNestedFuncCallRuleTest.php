<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNestedFuncCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\NoNestedFuncCallRule;

/**
 * @extends RuleTestCase<NoNestedFuncCallRule>
 */
final class NoNestedFuncCallRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/NestedYourself.php', [[NoNestedFuncCallRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/NestedFuncCall.php', [[NoNestedFuncCallRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/NestedArrayFilter.php', [[NoNestedFuncCallRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/SkipNonNested.php', []];
        yield [__DIR__ . '/Fixture/SkipCount.php', []];
        yield [__DIR__ . '/Fixture/SkipAssert.php', []];
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
        return self::getContainer()->getByType(NoNestedFuncCallRule::class);
    }
}
