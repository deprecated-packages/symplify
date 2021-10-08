<?php

declare(strict_types=1);

namespace Symplify\PHPStanTwigRules\Tests\Rules\NoTwigMissingVariableRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanTwigRules\Rules\NoTwigMissingVariableRule;

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
        yield [__DIR__ . '/Fixture/SomeMissingVariableController.php', [
            [sprintf(NoTwigMissingVariableRule::ERROR_MESSAGE, 'missing_variable'), 14],
        ]];

        yield [__DIR__ . '/Fixture/SkipUsedVariable.php', []];
        yield [__DIR__ . '/Fixture/SkipForeachVariable.php', []];
        yield [__DIR__ . '/Fixture/SkipTemplateSetVariable.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoTwigMissingVariableRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
