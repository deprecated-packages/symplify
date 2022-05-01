<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireStringRegexMatchKeyRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\RequireStringRegexMatchKeyRule;

/**
 * @extends RuleTestCase<RequireStringRegexMatchKeyRule>
 */
final class RequireStringRegexMatchKeyRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNotUsed.php', []];
        yield [__DIR__ . '/Fixture/SkipStringDim.php', []];
        yield [__DIR__ . '/Fixture/SkipNotRegexMatchResult.php', []];

        yield [__DIR__ . '/Fixture/NumericDim.php', [[RequireStringRegexMatchKeyRule::ERROR_MESSAGE, 15]]];
        yield [__DIR__ . '/Fixture/NumericDimDirectNext.php', [[RequireStringRegexMatchKeyRule::ERROR_MESSAGE, 15]]];
        yield [__DIR__ . '/Fixture/NumericDimInsideIfCond.php', [[RequireStringRegexMatchKeyRule::ERROR_MESSAGE, 15]]];
        yield [
            __DIR__ . '/Fixture/NumericDimOtherNameMultipleValues.php',
            [[RequireStringRegexMatchKeyRule::ERROR_MESSAGE, 15]], ];
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
        return self::getContainer()->getByType(RequireStringRegexMatchKeyRule::class);
    }
}
