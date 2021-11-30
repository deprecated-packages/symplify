<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Spotter\IfElseToMatchSpotterRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<IfElseToMatchSpotterRule>
 */
final class IfElseToMatchSpotterRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNonEmpty.php', []];
        yield [__DIR__ . '/Fixture/SkipNonMatchIf.php', []];
        yield [__DIR__ . '/Fixture/SkipAlmostMatching.php', []];

        yield [__DIR__ . '/Fixture/EmptyArrayAssign.php', [[IfElseToMatchSpotterRule::ERROR_MESSAGE, 13]]];
        yield [__DIR__ . '/Fixture/MatchingIfCandidate.php', [[IfElseToMatchSpotterRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(IfElseToMatchSpotterRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
