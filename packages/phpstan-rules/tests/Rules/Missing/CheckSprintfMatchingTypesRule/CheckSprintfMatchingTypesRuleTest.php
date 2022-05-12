<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckSprintfMatchingTypesRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Missing\CheckSprintfMatchingTypesRule;

/**
 * @extends RuleTestCase<CheckSprintfMatchingTypesRule>
 */
final class CheckSprintfMatchingTypesRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param        mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [
            __DIR__ . '/Fixture/MissMatchSprintf.php',
            [[CheckSprintfMatchingTypesRule::ERROR_MESSAGE, 11]]
        ];

        yield [__DIR__ . '/Fixture/SkipCorrectSprintf.php', []];
        yield [__DIR__ . '/Fixture/SkipCorrectForeachKey.php', []];
        yield [__DIR__ . '/Fixture/SkipToString.php', []];
        yield [__DIR__ . '/Fixture/SkipErrorType.php', []];
        yield [__DIR__ . '/Fixture/SkipValidTernary.php', []];
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
        return self::getContainer()->getByType(CheckSprintfMatchingTypesRule::class);
    }
}
