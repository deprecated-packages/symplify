<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Spotter\IfElseToMatchSpotterRule;

/**
 * @extends RuleTestCase<IfElseToMatchSpotterRule>
 */
final class IfElseToMatchSpotterRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipComplexValue.php', []];
        yield [__DIR__ . '/Fixture/SkipAlmostMatching.php', []];

        yield [__DIR__ . '/Fixture/IncludeNonEmpty.php', [[IfElseToMatchSpotterRule::ERROR_MESSAGE, 13]]];
        yield [__DIR__ . '/Fixture/EmptyArrayAssign.php', [[IfElseToMatchSpotterRule::ERROR_MESSAGE, 13]]];
        yield [__DIR__ . '/Fixture/MatchingIfCandidate.php', [[IfElseToMatchSpotterRule::ERROR_MESSAGE, 11]]];
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
        return self::getContainer()->getByType(IfElseToMatchSpotterRule::class);
    }
}
