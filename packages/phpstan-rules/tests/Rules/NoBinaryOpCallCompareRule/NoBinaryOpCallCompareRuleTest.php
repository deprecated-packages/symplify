<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoBinaryOpCallCompareRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\NoBinaryOpCallCompareRule;

/**
 * @extends RuleTestCase<NoBinaryOpCallCompareRule>
 */
final class NoBinaryOpCallCompareRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/CompareToFuncCall.php', [[NoBinaryOpCallCompareRule::ERROR_MESSAGE, 8]]];

        yield [__DIR__ . '/Fixture/SkipBool.php', []];
        yield [__DIR__ . '/Fixture/SkipAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipConcat.php', []];
        yield [__DIR__ . '/Fixture/SkipFuncCallCount.php', []];
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
        return self::getContainer()->getByType(NoBinaryOpCallCompareRule::class);
    }
}
