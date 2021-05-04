<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\OnlyOneClassMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\OnlyOneClassMethodRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<OnlyOneClassMethodRule>
 */
final class OnlyOneClassMethodRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|string[]|int[]> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipOneUsed.php', []];

        $errorMessage = sprintf(OnlyOneClassMethodRule::ERROR_MESSAGE, implode('", "', ['run', 'go']));
        yield [__DIR__ . '/Fixture/DoubleUsed.php', [[$errorMessage, 9]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(OnlyOneClassMethodRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
