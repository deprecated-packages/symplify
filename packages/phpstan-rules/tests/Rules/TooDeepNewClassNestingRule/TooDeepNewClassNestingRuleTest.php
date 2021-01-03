<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\TooDeepNewClassNestingRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\TooDeepNewClassNestingRule;

final class TooDeepNewClassNestingRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNoArgs.php', []];
        yield [__DIR__ . '/Fixture/SkipAllowedInsideFunctionParameters.php', []];
        yield [__DIR__ . '/Fixture/SkipAllowedDeepNewClass.php', []];

        yield [__DIR__ . '/Fixture/TooDeepNewClassInSameLevel.php', [
            [sprintf(TooDeepNewClassNestingRule::ERROR_MESSAGE, 3, 4), 7],
            [sprintf(TooDeepNewClassNestingRule::ERROR_MESSAGE, 3, 5), 13],
            [sprintf(TooDeepNewClassNestingRule::ERROR_MESSAGE, 3, 4), 14],
        ]];

        yield [__DIR__ . '/Fixture/TooDeepNewClass.php', [
            [sprintf(TooDeepNewClassNestingRule::ERROR_MESSAGE, 3, 6), 7],
            [sprintf(TooDeepNewClassNestingRule::ERROR_MESSAGE, 3, 5), 8],
            [sprintf(TooDeepNewClassNestingRule::ERROR_MESSAGE, 3, 4), 9],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(TooDeepNewClassNestingRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
