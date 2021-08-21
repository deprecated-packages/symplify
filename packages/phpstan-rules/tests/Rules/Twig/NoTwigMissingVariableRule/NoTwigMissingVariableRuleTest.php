<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Twig\NoTwigMissingVariableRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Twig\NoTwigMissingVariableRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoTwigMissingVariableRule>
 */
final class NoTwigMissingVariableRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeMissingVariable.php', [
            [sprintf(NoTwigMissingVariableRule::ERROR_MESSAGE, 'missing_variable'), 13],
        ]];

        yield [__DIR__ . '/Fixture/SkipUsedVariable.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoTwigMissingVariableRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
