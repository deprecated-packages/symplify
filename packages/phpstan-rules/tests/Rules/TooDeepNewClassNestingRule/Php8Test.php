<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\TooDeepNewClassNestingRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\TooDeepNewClassNestingRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<TooDeepNewClassNestingRule>
 */
final class Php8Test extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<string[]|array<int, mixed[]>>
     */
    public function provideData(): Iterator
    {
        yield [__DIR__ . '/FixturePhp8/SkipExpressionThrow.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(TooDeepNewClassNestingRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
