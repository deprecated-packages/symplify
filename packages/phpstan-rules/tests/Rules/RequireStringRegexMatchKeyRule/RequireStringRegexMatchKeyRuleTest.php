<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireStringRegexMatchKeyRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\Contract\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Rules\RequireStringRegexMatchKeyRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireStringRegexMatchKeyRule>
 */
final class RequireStringRegexMatchKeyRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
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

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireStringRegexMatchKeyRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
